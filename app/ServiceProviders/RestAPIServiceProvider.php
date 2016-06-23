<?php

namespace Evaneos\REST\ServiceProviders;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Evaneos\REST\API\Converters\ApiResponseBuilder;
use Evaneos\REST\API\Converters\PaginationConverter;
use Hateoas\HateoasBuilder;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Hateoas\UrlGenerator\CallableUrlGenerator;
use Hautelook\TemplatedUriRouter\Routing\Generator\Rfc6570Generator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;

class RestAPIServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $app
     */
    public function register(Container $app)
    {
        AnnotationRegistry::registerLoader('class_exists');

        $app['pagerFantaFactory'] = function () {
            return new PagerfantaFactory();
        };

        $app['templated_url_generator'] = function () use ($app) {
            return new Rfc6570Generator($app['routes'], $app['request_context']);
        };

        $app['api.converters.pagination'] = function () use ($app) {
            return new PaginationConverter(
                $app['config']['api.default_pagination_limit'],
                $app['config']['api.max_pagination_limit']
            );
        };

        $app['api.response.builder'] = function () use ($app) {
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
        };
    }
}
