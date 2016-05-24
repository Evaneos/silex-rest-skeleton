<?php

namespace Evaneos\REST\ServiceProviders;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Evaneos\REST\API\Converters\ApiResponseBuilder;
use Evaneos\REST\API\Converters\PaginationConverter;
use Hateoas\HateoasBuilder;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Hateoas\UrlGenerator\CallableUrlGenerator;
use Hautelook\TemplatedUriRouter\Routing\Generator\Rfc6570Generator;
use Silex\Application;
use Silex\ServiceProviderInterface;

class RestAPIServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app
     */
    public function register(Application $app)
    {
        AnnotationRegistry::registerLoader('class_exists');

        $app['pagerFantaFactory'] = $app->share(function () {
            return new PagerfantaFactory();
        });

        $app['templated_url_generator'] = $app->share(function () use ($app) {
            return new Rfc6570Generator($app['routes'], $app['request_context']);
        });

        $app['api.converters.pagination'] = $app->share(function () use ($app) {
            return new PaginationConverter(
                $app['config']['api.default_pagination_limit'],
                $app['config']['api.max_pagination_limit']
            );
        });

        $app['api.response.builder'] = $app->share(function () use ($app) {
            $hateoas = HateoasBuilder::create()
                ->setUrlGenerator(null, new CallableUrlGenerator(function ($route, array $parameters, $absolute) use ($app) {
                    return $app['url_generator']->generate($route, $parameters, $absolute);
                }))
                ->setUrlGenerator('templated', new CallableUrlGenerator(function ($route, array $parameters, $absolute) use ($app) {
                    return $app['templated_url_generator']->generate($route, $parameters, $absolute);
                }))
                ->setCacheDir($app['cache_dir'] . '/serializer')
                ->setDebug($app['debug'])
                ->build();

            return new ApiResponseBuilder($hateoas);
        });
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     *
     * @param Application $app
     */
    public function boot(Application $app)
    {
    }
}
