<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Repository;

use BackendBase\Domain\Contents\Exception\ContentNotFound;
use Carbon\CarbonImmutable;
use Cocur\Slugify\Slugify;
use DateTimeImmutable;
use DateTimeZone;
use PascalDeVink\ShortUuid\ShortUuid;
use Selami\Stdlib\Arrays\ArrayKeysCamelCaseConverter;

use function array_key_exists;
use function count;
use function json_decode;
use function str_replace;
use function strpos;

use const DATE_ATOM;
use const JSON_THROW_ON_ERROR;

class ContentRepository extends GenericRepository
{
    public function getCategory(string $category): array
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

    public function getCategoryById(string $categoryId): array
    {
        $sql       = '
            SELECT L.id, L.key, L.name, L.metadata, L.slug 
              FROM public.lookup_table L 
             WHERE L.id = :categoryId 
               AND L.is_deleted = 0
             LIMIT 1
        ';
        $statement = $this->connection->executeQuery($sql, ['categoryId' => $categoryId]);
        $data      = $statement->fetch();
        if ($data === false) {
            return [];
        }

        $data['metadata'] = json_decode($data['metadata'], true, 512, JSON_THROW_ON_ERROR);

        return ArrayKeysCamelCaseConverter::convertArrayKeys($data);
    }

    public function getCategories(string $parentKey): array
    {
        $parent = $this->getCategory($parentKey);

        return $this->getCategoriesByParentId($parent['id']);
    }

    public function getCategoriesByParentId(string $parentId): array
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

    public function getContentBySlug(string $slug, string $language, string $region): array
    {
        $otherLanguage = $language === 'tr' ?  'en' : 'tr';
        $sql           = <<<SQL
            SELECT CD.title, CD.slug, CD2.slug as other_lang_slug, CD.keywords, CD.serp_title, CD.content_id, C.id, 
                   CD.description, CD.body, C.tags, C.robots, 
                   C.redirect_url, C.cover_image_landscape,
                   C.template, LT.metadata->'itemData'->>'templateFile' as template_file, C.sort_order,
                   (SELECT C2.id FROM public.contents C2 WHERE C2.category = C.category  
                       AND C2.is_active = 1
                       AND C2.is_deleted = 0
                       AND C2.publish_at <= :now
                       AND (C2.expire_at >= :now OR C2.expire_at IS NULL) AND C2.sort_order > C.sort_order ORDER BY C2.sort_order ASC LIMIT 1 ) as prev_id,
                   (SELECT C3.id FROM public.contents C3 WHERE C3.category = C.category  
                       AND C3.is_active = 1
                       AND C3.is_deleted = 0
                       AND C3.publish_at <= :now
                       AND (C3.expire_at >= :now OR C3.expire_at IS NULL) AND C3.sort_order < C.sort_order ORDER BY C3.sort_order DESC LIMIT 1 ) as next_id
                   
              FROM public.content_details CD
              LEFT JOIN public.content_details CD2 ON CD2.content_id = CD.content_id AND CD2.language = :otherLanguage
              LEFT JOIN contents C ON C.id=CD.content_id
              LEFT JOIN lookup_table LT ON LT.key=C.template
             WHERE CD.slug = :slug
               AND CD.language = :language
               AND CD.region = :region
               AND C.is_active = 1
               AND C.is_deleted = 0
               AND C.publish_at <= :now
               AND (C.expire_at >= :now OR C.expire_at IS NULL)
SQL;
        $statement     = $this->connection->executeQuery($sql, [
            'slug' => $slug,
            'language' => $language,
            'otherLanguage' => $otherLanguage,
            'region' => $region,
            'now' => (new DateTimeImmutable())->format(DATE_ATOM),
        ]);
        $contentData   = $statement->fetch();
        if ($contentData === false) {
            throw ContentNotFound::create('Content not found. It may be deleted.');
        }

        $contentData['body'] = json_decode($contentData['body'], true, 512, JSON_THROW_ON_ERROR);
        $contentData['tags'] = json_decode($contentData['tags'], true, 512, JSON_THROW_ON_ERROR);
        unset($contentData['is_deleted']);

        return ArrayKeysCamelCaseConverter::convertArrayKeys($contentData);
    }

    public function getContentById(string $contentId): array
    {
        $sql         = '
            SELECT *
              FROM public.contents C
             WHERE C.id = :id
               AND C.is_deleted = 0
        ';
        $statement   = $this->connection->executeQuery($sql, ['id' => $contentId]);
        $contentData = $statement->fetch();
        if ($contentData === false) {
            throw ContentNotFound::create('Content not found. It may be deleted.');
        }

        $contentData['tags'] = json_decode($contentData['tags'], true, 512, JSON_THROW_ON_ERROR);
        unset($contentData['is_deleted']);

        return ArrayKeysCamelCaseConverter::convertArrayKeys($contentData);
    }

    public function getContentDetailsById(string $contentId): array
    {
        $sql         = '
            SELECT *
              FROM public.content_details CD
             WHERE CD.content_id = :id
        ';
        $statement   = $this->connection->executeQuery($sql, ['id' => $contentId]);
        $contentData = $statement->fetchAll();
        if ($contentData === false) {
            throw ContentNotFound::create('Content not found. It may be deleted.');
        }

        $data = [];
        foreach ($contentData as $contentDatum) {
            $contentDatum['body']            = json_decode($contentDatum['body'], true, 512, JSON_THROW_ON_ERROR);
            $data[$contentDatum['language']] = $contentDatum;
        }

        return ArrayKeysCamelCaseConverter::convertArrayKeys($data);
    }

    public function getContentByIdForClient(string $contentId): array
    {
        $slugify             = new Slugify(['rulesets' => ['default', 'turkish']]);
        $shortener           = Shortener::make(
            Dictionary::createUnmistakable() // or pass your own characters set
        );
        $now                 = new DateTimeImmutable();
        $nowLocale           = $now->setTimezone(new DateTimeZone('Europe/Istanbul'));
        $nowLocaleDateString = $nowLocale->format(DATE_ATOM);
        $sql                 = <<<SQL
            SELECT *
              FROM public.contents C 
             WHERE C.id = :id
               AND C.is_deleted = 0
               AND C.is_active = 1
               AND (jsonb_path_exists(C.metadata, '$.publishDate') = false OR C.metadata->>'publishDate' <= :nowLocaleDate)
               AND (jsonb_path_exists(C.metadata, '$.expireDate') = false OR C.metadata->>'expireDate' >= :nowLocaleDate)
             LIMIT 1
SQL;
        $statement           = $this->connection->executeQuery($sql, ['id' => $contentId, 'nowLocaleDate' => $nowLocaleDateString]);
        $data                = $statement->fetch();
        if ($data === false) {
            throw ContentNotFound::create('Content not found. It may be deleted.');
        }

        unset($data['is_deleted']);
        $data['type']        = 'plain';
        $data['useCdn']      = 0;
        $data['images']      = json_decode($data['images'], true, 512, JSON_THROW_ON_ERROR);
        $data['metadata']    = json_decode($data['metadata'] ?? '[]', true, 512, JSON_THROW_ON_ERROR);
        $data['heroImage']   =  '';
        $data['publishDate'] = new CarbonImmutable($data['created_at'], 'UTC');
        $data['publishDate'] = $data['publishDate']->setTimezone(new DateTimeZone('europe/istanbul'))
            ->format('d.m.Y');
        if (array_key_exists('publishDate', $data['metadata'])) {
            $data['publishDate'] = new CarbonImmutable($data['metadata']['publishDate']);
            $data['publishDate'] = $data['publishDate']->format('d.m.Y');
        }

        $data['slug'] = $slugify->slugify($data['title']) . '-' . $shortener->reduce($data['id']);

        if ((is_countable($data['images']) ? count($data['images']) : 0) > 0) {
            $data['heroImage'] = $data['images'][0];
        }

        if ($data['heroImage'] === '' && array_key_exists('headerVideo', $data['metadata']) && ! empty($data['metadata']['headerVideo'])) {
            $videoId           = str_replace('https://www.youtube.com/embed/', '', $data['metadata']['headerVideo']);
            $data['heroImage'] = str_replace('{videoId}', $videoId, 'https://i3.ytimg.com/vi/{videoId}/maxresdefault.jpg');
        }

        if (! empty($data['heroImage']) && !str_starts_with($data['heroImage'], 'http')) {
            $data['useCdn'] = 1;
        }

        return ArrayKeysCamelCaseConverter::convertArrayKeys($data);
    }

    public function getContentBySlugForClient(string $slug): array
    {
        $slug = '/' . $slug;

        $sql       = <<<SQL
            SELECT C.id
              FROM public.contents C 
             WHERE C.metadata->>'slug' = :slug
             LIMIT 1
SQL;
        $statement = $this->connection->executeQuery($sql, ['slug' => $slug]);
        $data      = $statement->fetch();
        if ($data === false) {
            throw ContentNotFound::create('Content not found. It may be deleted.');
        }

        return $this->getContentByIdForClient($data['id']);
    }

    public function getContentsByCategory(string $categoryId, string $language, string $region, ?bool $withBody = false, ?int $offset = 0, ?int $limit = null): array
    {
        $slugify   = new Slugify(['rulesets' => ['default', 'turkish']]);
        $shortener = new ShortUuid();

        $criteria      = ['categoryId' => $categoryId, 'language' => $language, 'region' => $region];
        $returnData    = [];
        $withBodySql   = '';
        $additionalSql = '';
        $sql           = <<<SQL
            SELECT C.id, 
                   CD.title, 
                   CD.slug, 
                   C.cover_image_landscape,
                   REPLACE(C.cover_image_landscape, '.', '-mobile.') AS cover_image_landscape_mobile,
                   L.name as category_str, 
                   L.slug as category_slug,
                   C.category, 
                   C.created_at, 
                   C.updated_at, 
                   C.sort_order, 
                   C.redirect_url,
                   {withBodySql}
                   C.is_active
              FROM public.contents C
              LEFT JOIN content_details CD ON CD.content_id=C.id AND CD.language=:language AND CD.region=:region
              LEFT JOIN lookup_table L ON L.id=C.category
             WHERE C.category = :categoryId 
               AND C.is_deleted = 0
                   {additionalSql}
             ORDER BY C.sort_order DESC
SQL;
        if ($withBody === true) {
            $withBodySql   =  ' CD.body, ';
            $additionalSql = ' AND C.is_active=1 AND CD.is_active=1';
            if ($limit !== null) {
                $sql .= ' OFFSET ' . $offset . ' LIMIT ' . $limit;
            }
        }

        $sql       = str_replace(['{withBodySql}', '{additionalSql}'], [$withBodySql, $additionalSql], $sql);
        $statement = $this->connection->executeQuery($sql, $criteria);
        $data      = $statement->fetchAll();
        if ($data === false) {
            return $returnData;
        }

        foreach ($data as $datum) {
            if (array_key_exists('body', $datum)) {
                $datum['body'] = json_decode($datum['body'], true, 512, JSON_THROW_ON_ERROR);
                foreach ($datum['body'] as $key => $value) {
                    $datum['body'][$key] = str_replace('{cdnUrl}', $this->config['app']['cdn-url'], $value);
                }
            }

            $returnData[] = $datum;
        }

        return ArrayKeysCamelCaseConverter::convertArrayKeys($returnData);
    }

    public function getRandomExcludingOne(string $category, $contentId, $limit, ?bool $withBody = false): array
    {
        $postsData = $this->getContentsByCategoryForClient($category, 0, $limit + 1, $withBody);
        $posts     = [];
        for ($i = 0; $i < $limit; $i++) {
            if ($postsData[$i]['id'] === $contentId) {
                $i++;
            }

            $posts[] = $postsData[$i];
        }

        return $posts;
    }

    public function getContentsByCategoryForClient(string $category, int $offset, int $limit, ?bool $withBody = false): array
    {
        $slugify             = new Slugify(['rulesets' => ['default', 'turkish']]);
        $shortener           = Shortener::make(
            Dictionary::createUnmistakable() // or pass your own characters set
        );
        $now                 = new DateTimeImmutable();
        $nowLocale           = $now->setTimezone(new DateTimeZone('Europe/Istanbul'));
        $nowLocaleDateString = $nowLocale->format(DATE_ATOM);
        $returnData          = [];
        $withBodySql         = '';
        $sql                 = <<<SQL
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
             WHERE C.category = :category AND C.is_deleted = 0 AND  AND C.is_active = 1
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
            'offset' => $offset,
            'limit' => $limit,
            'nowLocaleDate' => $nowLocaleDateString,
        ]);
        $data      = $statement->fetchAll();
        if ($data === false) {
            return $returnData;
        }

        foreach ($data as $datum) {
            $datum['useCdn']      = 0;
            $datum['images']      = json_decode($datum['images'], true, 512, JSON_THROW_ON_ERROR);
            $datum['metadata']    = json_decode($datum['metadata'] ?? '[]', true, 512, JSON_THROW_ON_ERROR);
            $datum['heroImage']   =  '';
            $datum['publishDate'] = new CarbonImmutable($datum['created_at'], 'UTC');
            $datum['publishDate'] = $datum['publishDate']->setTimezone(new DateTimeZone('europe/istanbul'))
                ->format('d.m.Y');
            if (array_key_exists('publishDate', $datum['metadata'])) {
                $datum['publishDate'] = new CarbonImmutable($datum['metadata']['publishDate']);
                $datum['publishDate'] = $datum['publishDate']->format('d.m.Y');
            }

            if ((is_countable($datum['images']) ? count($datum['images']) : 0) > 0) {
                $datum['heroImage'] = $datum['images'][0];
            }

            if ($datum['heroImage'] === '' && array_key_exists('headerVideo', $datum['metadata']) && ! empty($datum['metadata']['headerVideo'])) {
                $videoId            = str_replace('https://www.youtube.com/embed/', '', $datum['metadata']['headerVideo']);
                $datum['heroImage'] = str_replace('{videoId}', $videoId, 'https://i3.ytimg.com/vi/{videoId}/maxresdefault.jpg');
            }

            $datum['slug'] = $slugify->slugify($datum['title']) . '-' . $shortener->reduce($datum['id']);
            if (! empty($datum['heroImage']) && !str_starts_with($datum['heroImage'], 'http')) {
                $datum['useCdn'] = 1;
            }

            $returnData[] = $datum;
        }

        return ArrayKeysCamelCaseConverter::convertArrayKeys($returnData);
    }

    public function getContentMenuByCategory(string $category): array
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

    public function getContentByModuleName(string $moduleName): array
    {
        $returnData = [];
        $sql        = <<<SQL
            SELECT C.*
              FROM public.contents C
             WHERE C.metadata->>'module' = :moduleName      
SQL;
        $statement  = $this->connection->executeQuery($sql, ['moduleName' => $moduleName]);
        $data       = $statement->fetch();
        if ($data === false) {
            return $returnData;
        }

        $data['images']   = json_decode($data['images'], true, 512, JSON_THROW_ON_ERROR);
        $data['metadata'] = json_decode($data['metadata'], true, 512, JSON_THROW_ON_ERROR);

        return ArrayKeysCamelCaseConverter::convertArrayKeys($data);
    }

    public function getMenuByCategory(string $category): array
    {
        $returnData = [];
        $sql        = <<<SQL
            SELECT C.id,CD.language, CD.region, CD.title, CD.slug, 
                   CD.body->>'redirect' as lang_redirect_url,
                   C.cover_image_landscape, C.redirect_url, C.sort_order, C.updated_at
                FROM contents C
                LEFT JOIN content_details CD ON CD.content_id=C.id
             WHERE C.is_deleted = 0
                 AND C.is_active = 1
                 AND CD.is_active = 1
                 AND C.publish_at <= NOW()
                 AND (C.expire_at IS NULL OR C.expire_at >= NOW())
                 AND C.category = (SELECT L.id from lookup_table L WHERE L.key=:category LIMIT 1)
             ORDER BY C.sort_order, CD.language DESC
SQL;

        $statement = $this->connection->executeQuery($sql, ['category' => $category]);
        $data      = $statement->fetchAll();
        if (empty($data)) {
            return [];
        }

        return ArrayKeysCamelCaseConverter::convertArrayKeys($data);
    }
}
