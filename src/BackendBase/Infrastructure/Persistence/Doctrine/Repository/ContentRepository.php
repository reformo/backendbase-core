<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManager;
use BackendBase\Domain\Contents\Exception\ContentNotFound;
use Redislabs\Module\ReJSON\ReJSON;
use BackendBase\Shared\Services\ArrayKeysCamelCaseConverter;
use const JSON_THROW_ON_ERROR;
use function json_decode;
use function str_replace;
use Cocur\Slugify\Slugify;
use Keiko\Uuid\Shortener\Dictionary;
use Keiko\Uuid\Shortener\Shortener;
use Carbon\CarbonImmutable;

class ContentRepository
{
    protected EntityManager $entityManager;
    protected Connection $connection;
    private ReJSON $reJSON;

    public function __construct(EntityManager $entityManager, Connection $connection, ReJSON $reJSON)
    {
        $this->connection    = $connection;
        $this->entityManager = $entityManager;
        $this->reJSON        = $reJSON;
    }

    public function getCategory(string $category) : array
    {
        $sql       = '
            SELECT L.id, L.key, L.name, L.metadata, L.slug 
              FROM public.lookup_table L 
             WHERE L.key = :category 
               AND L.is_deleted = 0
             LIMIT 1
        ';
        $statement = $this->connection->executeQuery($sql, ['category' => $category]);
        $data      = $statement->fetch();
        if ($data === false) {
            return [];
        }
        $data['metadata'] = json_decode($data['metadata'], true, 512, JSON_THROW_ON_ERROR);

        return ArrayKeysCamelCaseConverter::convertArrayKeys($data);
    }

    public function getCategories(string $parentKey) : array
    {
        $parent = $this->getCategory($parentKey);

        return $this->getCategoriesByParentId($parent['id']);
    }

    public function getCategoriesByParentId(string $parentId) : array
    {
        $sql       = '
            SELECT L.key, L.name, L.metadata, L.slug 
              FROM public.lookup_table L 
             WHERE L.parent_id = :parentId 
               AND L.is_deleted = 0
               AND L.is_active = 1
             ORDER BY l.name ASC
        ';
        $statement = $this->connection->executeQuery($sql, ['parentId' => $parentId]);
        $data      = $statement->fetchAll();
        if ($data === false) {
            return [];
        }
        $returnData = [];
        foreach ($data as $datum) {
            $datum['metadata'] = json_decode($datum['metadata'], true, 512, JSON_THROW_ON_ERROR);
            $returnData[]      = $datum;
        }

        return ArrayKeysCamelCaseConverter::convertArrayKeys($returnData);
    }

    public function getContentById(string $contentId) : array
    {
        $sql       = '
            SELECT *
              FROM public.contents C 
             WHERE C.id = :id
               AND C.is_deleted = 0
             LIMIT 1
        ';
        $statement = $this->connection->executeQuery($sql, ['id' => $contentId]);
        $data      = $statement->fetch();
        if ($data === false) {
            throw ContentNotFound::create('Content not found. It may be deleted.');
        }
        $data['metadata']  = json_decode($data['metadata'] ?? '[]', true, 512, JSON_THROW_ON_ERROR);
        $data['canonical'] = json_decode($data['canonical'] ?? '[]', true, 512, JSON_THROW_ON_ERROR);
        $data['images']    = json_decode($data['images'] ?? '[]', true, 512, JSON_THROW_ON_ERROR);
        unset($data['is_deleted']);

        return ArrayKeysCamelCaseConverter::convertArrayKeys($data);
    }

    public function getContentByIdForClient(string $contentId) : array
    {
        $slugify = new  Slugify(['rulesets' => ['default', 'turkish']]);
        $shortener = Shortener::make(
            Dictionary::createUnmistakable() // or pass your own characters set
        );
        $now = new \DateTimeImmutable();
        $nowLocale = $now->setTimezone(new \DateTimeZone('Europe/Istanbul'));
        $nowLocaleDateString = $nowLocale->format(DATE_ATOM);
        $sql       =<<<SQL
            SELECT *
              FROM public.contents C 
             WHERE C.id = :id
               AND C.is_deleted = 0
               AND C.is_active = 1
               AND (jsonb_path_exists(C.metadata, '$.publishDate') = false OR C.metadata->>'publishDate' <= :nowLocaleDate)
               AND (jsonb_path_exists(C.metadata, '$.expireDate') = false OR C.metadata->>'expireDate' >= :nowLocaleDate)
             LIMIT 1
SQL;
        $statement = $this->connection->executeQuery($sql, ['id' => $contentId, 'nowLocaleDate' => $nowLocaleDateString]);
        $data      = $statement->fetch();
        if ($data === false) {
            throw ContentNotFound::create('Content not found. It may be deleted.');
        }
        unset($data['is_deleted']);
        $data['type'] = 'plain';
        $data['useCdn'] = 0;
        $data['images']   = json_decode($data['images'], true, 512, JSON_THROW_ON_ERROR);
        $data['metadata'] = json_decode($data['metadata'] ?? '[]', true, 512, JSON_THROW_ON_ERROR);
        $data['heroImage'] =  '';
        $data['publishDate'] = new CarbonImmutable($data['created_at'], 'UTC');
        $data['publishDate'] = $data['publishDate']->setTimezone(new \DateTimeZone('europe/istanbul'))
            ->format('d.m.Y');
        if (array_key_exists('publishDate', $data['metadata'])) {
            $data['publishDate'] = new CarbonImmutable($data['metadata']['publishDate']);
            $data['publishDate'] = $data['publishDate']->format('d.m.Y');
        }
        $data['slug'] = $slugify->slugify($data['title']) . '-' . $shortener->reduce($data['id']);

        if (count($data['images']) > 0) {
            $data['heroImage'] = $data['images'][0];
        }
        if ($data['heroImage'] === '' && array_key_exists('headerVideo', $data['metadata']) && !empty($data['metadata']['headerVideo']) ) {
            $videoId = str_replace('https://www.youtube.com/embed/', '', $data['metadata']['headerVideo']);
            $data['heroImage'] = str_replace('{videoId}', $videoId, "https://i3.ytimg.com/vi/{videoId}/maxresdefault.jpg");
        }
        if (!empty($data['heroImage']) && strpos($data['heroImage'], 'http') !== 0) {
            $data['useCdn'] = 1;
        }
        return ArrayKeysCamelCaseConverter::convertArrayKeys($data);
    }

    public function getContentBySlugForClient(string $slug) : array
    {

        $sql       =<<<SQL
            SELECT C.id
              FROM public.contents C 
             WHERE C.metadata.slug = :slug
             LIMIT 1
SQL;
        $statement = $this->connection->executeQuery($sql, ['slug' => $slug]);
        $data      = $statement->fetch();
        if ($data === false) {
            throw ContentNotFound::create('Content not found. It may be deleted.');
        }
        return $this->getContentByIdForClient($data['id']);
    }


    public function getContentsByCategory(string $category, ?bool $withBody = false) : array
    {
        $slugify = new  Slugify(['rulesets' => ['default', 'turkish']]);
        $shortener = Shortener::make(
            Dictionary::createUnmistakable() // or pass your own characters set
        );
        $returnData  = [];
        $withBodySql = '';
        $sql         = '
            SELECT C.id, 
                   C.title, 
                   C.type, 
                   L.name as category_str, 
                   L.slug as category_slug,
                   C.category, 
                   C.created_at, 
                   C.updated_at, 
                   C.sort_order, 
                   C.images, 
                   C.metadata,
                   C.redirect,
                   {withBodySql}
                   C.is_active
              FROM public.contents C
              LEFT JOIN lookup_table L ON L.key=C.category
             WHERE C.category = :category AND C.is_deleted = 0
             ORDER BY C.sort_order DESC
        ';
        if ($withBody === true) {
            $withBodySql =  'C.body,';
        }
        $sql       = str_replace('{withBodySql}', $withBodySql, $sql);
        $statement = $this->connection->executeQuery($sql, ['category' => $category]);
        $data      = $statement->fetchAll();
        if ($data === false) {
            return $returnData;
        }

        foreach ($data as $datum) {
            $datum['images']   = json_decode($datum['images'], true, 512, JSON_THROW_ON_ERROR);
            $datum['metadata'] = json_decode($datum['metadata'], true, 512, JSON_THROW_ON_ERROR);
            $datum['slug'] = $datum['category_slug'] .'/'. $slugify->slugify($datum['title']).'-'.$shortener->reduce($datum['id']);

            $returnData[]      = $datum;
        }

        return ArrayKeysCamelCaseConverter::convertArrayKeys($returnData);
    }
    public function getRandomExcludingOne(string $category, $contentId, $limit, ?bool $withBody = false) :array
    {
        $postsData = $this->getContentsByCategoryForClient($category, 0, $limit+1, $withBody);
        $posts = [];
        for ($i=0; $i < $limit; $i++) {
            if ($postsData[$i]['id'] === $contentId) {
                $i++;
            }
            $posts[] = $postsData[$i];
        }
        return $posts;
    }


    public function getContentsByCategoryForClient(string $category, int $offset, int $limit, ?bool $withBody = false) : array
    {
        $slugify = new  Slugify(['rulesets' => ['default', 'turkish']]);
        $shortener = Shortener::make(
            Dictionary::createUnmistakable() // or pass your own characters set
        );
        $now = new \DateTimeImmutable();
        $nowLocale = $now->setTimezone(new \DateTimeZone('Europe/Istanbul'));
        $nowLocaleDateString = $nowLocale->format(DATE_ATOM);
        $returnData  = [];
        $withBodySql = '';
        $sql         = <<<SQL
            SELECT C.id, 
                   C.title, 
                   C.type, 
                   L.name as category_str, 
                   C.category, 
                   C.created_at, 
                   C.updated_at, 
                   C.sort_order, 
                   C.images, 
                   C.metadata,
                   C.redirect,
                   {withBodySql}
                   C.is_active
              FROM public.contents C
              LEFT JOIN lookup_table L ON L.key=C.category
             WHERE C.category = :category AND C.is_deleted = 0
                AND (jsonb_path_exists(C.metadata, '$.publishDate') = false OR C.metadata->>'publishDate' <= :nowLocaleDate)
               AND (jsonb_path_exists(C.metadata, '$.expireDate') = false OR C.metadata->>'expireDate' >= :nowLocaleDate)
             ORDER BY C.sort_order DESC
             OFFSET :offset
             LIMIT :limit
SQL;
        if ($withBody === true) {
            $withBodySql =  'C.body,';
        }
        $sql       = str_replace('{withBodySql}', $withBodySql, $sql);
        $statement = $this->connection->executeQuery($sql, [
            'category' => $category,
            'offset'=> $offset,
            'limit' => $limit,
            'nowLocaleDate' => $nowLocaleDateString
        ]);
        $data      = $statement->fetchAll();
        if ($data === false) {
            return $returnData;
        }

        foreach ($data as $datum) {
            $datum['useCdn'] = 0;
            $datum['images']   = json_decode($datum['images'], true, 512, JSON_THROW_ON_ERROR);
            $datum['metadata'] = json_decode($datum['metadata'] ?? '[]', true, 512, JSON_THROW_ON_ERROR);
            $datum['heroImage'] =  '';
            $datum['publishDate'] = new CarbonImmutable($datum['created_at'], 'UTC');
            $datum['publishDate'] = $datum['publishDate']->setTimezone(new \DateTimeZone('europe/istanbul'))
            ->format('d.m.Y');
            if (array_key_exists('publishDate', $datum['metadata'])) {
                $datum['publishDate'] = new CarbonImmutable($datum['metadata']['publishDate']);
                $datum['publishDate'] = $datum['publishDate']->format('d.m.Y');
            }

            if (count($datum['images']) > 0) {
                $datum['heroImage'] = $datum['images'][0];
            }
            if ($datum['heroImage'] === '' && array_key_exists('headerVideo', $datum['metadata']) && !empty($datum['metadata']['headerVideo']) ) {
                $videoId = str_replace('https://www.youtube.com/embed/', '', $datum['metadata']['headerVideo']);
                $datum['heroImage'] = str_replace('{videoId}', $videoId, "https://i3.ytimg.com/vi/{videoId}/maxresdefault.jpg");
            }
            $datum['slug'] = $slugify->slugify($datum['title']) . '-' . $shortener->reduce($datum['id']);
            if (!empty($datum['heroImage']) && strpos($datum['heroImage'], 'http') !== 0) {
                $datum['useCdn'] = 1;
            }
            $returnData[]      = $datum;
        }

        return ArrayKeysCamelCaseConverter::convertArrayKeys($returnData);
    }


    public function getContentMenuByCategory(string $category) : array
    {
        $returnData = [];
        $sql        = '
            SELECT C.id, 
                   C.title, 
                   C.images,
                   C.redirect
              FROM public.contents C
             WHERE C.category = :category 
               AND C.is_deleted = 0
               AND C.is_active = 1
             ORDER BY C.sort_order ASC            
        ';

        $statement = $this->connection->executeQuery($sql, ['category' => $category]);
        $data      = $statement->fetchAll();
        if ($data === false) {
            return $returnData;
        }

        foreach ($data as $datum) {
            $datum['images'] = json_decode($datum['images'], true, 512, JSON_THROW_ON_ERROR);
            $returnData[]    = $datum;
        }

        return ArrayKeysCamelCaseConverter::convertArrayKeys($returnData);
    }

    public function getContentByModuleName(string $moduleName) : array
    {
        $returnData = [];
        $sql        = <<<SQL
            SELECT C.*
              FROM public.contents C
             WHERE C.metadata->>'module' = :moduleName      
SQL;
        $statement = $this->connection->executeQuery($sql, ['moduleName' => $moduleName]);
        $data      = $statement->fetch();
        if ($data === false) {
            return $returnData;
        }
        $data['images'] = json_decode($data['images'], true, 512, JSON_THROW_ON_ERROR);
        $data['metadata'] = json_decode($data['metadata'], true, 512, JSON_THROW_ON_ERROR);
        return ArrayKeysCamelCaseConverter::convertArrayKeys($data);
    }

    public function getMenuByCategory(string $category) : array
    {
        $returnData = [];
        $sql        = '
            SELECT C.id,
                   C.title,
                   C.images,
                   C.redirect
              FROM public.contents C
             WHERE C.category = :category 
               AND C.is_deleted = 0
               AND C.is_active = 1
             ORDER BY C.sort_order DESC            
        ';

        $statement = $this->connection->executeQuery($sql, ['category' => $category]);
        $data      = $statement->fetchAll();
        if ($data === false) {
            return $returnData;
        }

        foreach ($data as $datum) {
            $datum['images'] = json_decode($datum['images'], true, 512, JSON_THROW_ON_ERROR);
            $returnData[]    = $datum;
        }

        return ArrayKeysCamelCaseConverter::convertArrayKeys($returnData);
    }

}
