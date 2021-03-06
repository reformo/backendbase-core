<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Contents;

use BackendBase\Shared\Factory\RequestHandlerFactory;
use BackendBase\Shared\Interfaces\MezzioHandlerConfigProvider;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider implements MezzioHandlerConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies'  => $this->getDependencies(),
        ];
    }

    public function registerRoutes(Application $app, MiddlewareFactory $factory): void
    {
        $app->get('/cms/categories', Handler\GetCategoryList::class, 'contents.categories');
        $app->get('/cms/{category}/contents', Handler\GetContentListByCategory::class, 'contents.by_category');
        $app->post('/cms/{category}/contents', Handler\AddNewContentToCategory::class, 'contents.by_category.create');
        $app->get('/cms/contents/{contentId}', Handler\GetContentDetails::class, 'contents.details');
        $app->patch('/cms/contents/{contentId}', Handler\ChangeContentMetadata::class, 'contents.update');
        $app->patch('/cms/contents/{contentId}/details/{contentDetailId}', Handler\ChangeContentDetails::class, 'content_details.update');
        $app->put('/cms/contents/{contentId}/images', Handler\UploadImages::class, 'contents.upload_image');
        $app->post('/cms/contents/{contentId}/file-images', Handler\UploadFileImages::class, 'contents.upload_file_image');

        $app->delete('/cms/contents/{contentId}/images/{index}', Handler\RemoveImage::class, 'contents.delete_image.');

        $app->delete('/cms/contents/{contentId}', Handler\RemoveContent::class, 'contents.delete');

        $app->put('/module/{moduleName}/images', Handler\GenericUploadImage::class, 'module.upload_image');

        $app->post('/files/new-form-data-file', Handler\UploadFileImageGeneral::class, 'module.upload_form_data_image_general');
        $app->put('/files/new-file', Handler\UploadImageGeneral::class, 'module.upload_image_general');
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [],
            'factories'  => [
                Handler\GetCategoryList::class => RequestHandlerFactory::class,
                Handler\GetContentListByCategory::class => RequestHandlerFactory::class,
                Handler\AddNewContentToCategory::class => RequestHandlerFactory::class,
                Handler\GetContentDetails::class => RequestHandlerFactory::class,
                Handler\ChangeContentDetails::class => RequestHandlerFactory::class,
                Handler\ChangeContentMetadata::class => RequestHandlerFactory::class,
                Handler\UploadImages::class => RequestHandlerFactory::class,
                Handler\UploadFileImages::class => RequestHandlerFactory::class,
                Handler\RemoveImage::class => RequestHandlerFactory::class,
                Handler\RemoveContent::class => RequestHandlerFactory::class,
                Handler\GenericUploadImage::class => RequestHandlerFactory::class,
                Handler\UploadFileImageGeneral::class => RequestHandlerFactory::class,
                Handler\UploadImageGeneral::class => RequestHandlerFactory::class,

            ],
        ];
    }
}
