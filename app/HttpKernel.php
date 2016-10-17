<?php

namespace Evaneos\REST;

use Evaneos\JWT\Silex\Provider\SecurityJWTServiceProvider;
use Evaneos\REST\API\ControllerProviders\ApiControllerProvider;
use Evaneos\REST\API\Exceptions\BadRequestException;
use Evaneos\REST\Kernel\Kernel;
use Evaneos\REST\ServiceProviders\ControllersServiceProvider;
use Evaneos\REST\ServiceProviders\RestAPIServiceProvider;
use Monolog\Logger;
use Monolog\Processor\TagProcessor;
use Monolog\Processor\WebProcessor;
use Ramsey\Uuid\Uuid;
use Silex\Application;
use Silex\Provider\RoutingServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;

class HttpKernel extends Kernel implements HttpKernelInterface, TerminableInterface
{
    private static $header = 'X-Request-Id';

    protected function doBoot()
    {
        // Logging
        $this->app->extend('monolog', function (Logger $logger) {
            $webProcessor = new WebProcessor();
            $logger->pushProcessor($webProcessor);
            return $logger;
        });

        $this->app->extend('monolog.processor.tag', function (TagProcessor $processor) {
            $processor->addTags([
                'request_id' => $this->app['request_id']
            ]);
            return $processor;
        });

        // Security
        if ($this->app['config']['security.enabled']) {
            $this->app['security.firewalls'] = [
                'all' => [
                    'stateless' => true,
                    'pattern' => '^.*$',
                    'jwt' => [
                        'secret_key' => $this->app['config']['security.jwt_secret_key'],
                        'allowed_algorithms' => ['HS256'],
                        'retrieval_strategy' => 'chain',
                    ],
                ],
            ];

            $this->app->register(new SecurityServiceProvider());
            $this->app['security.voters'] = $this->app->extend('security.voters', function ($voters) {
                // add your voters here
                return $voters;
            });
        }

        // HTTP
        $this->app->register(new SecurityJWTServiceProvider());
        $this->app->register(new RoutingServiceProvider());
        $this->app->register(new RestAPIServiceProvider());

        $this->app->before(function (Request $request) {
            if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
                $data = @json_decode($request->getContent(), true);

                if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                    throw new \LogicException(sprintf('Failed to parse json string "%s", error: "%s"', $data, json_last_error_msg()));
                }

                $request->request->replace(is_array($data) ? $data : []);
            }
        });

        $this->app->register(new ServiceControllerServiceProvider());

        $this->registerRoutes();

        $this->app->error(function (BadRequestException $invalidRequest) {
            return $this->app['api.response.builder']->buildResponse($invalidRequest->getErrors(), Response::HTTP_BAD_REQUEST);
        });
    }

    /**
     * @param Request $request
     * @param int     $type
     * @param bool    $catch
     *
     * @return Response
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        if (null === $requestId = $request->headers->get(self::$header)) {
            $requestId = (string) Uuid::uuid4();
            $request->headers->set(self::$header, $requestId);
        }

        $this->app['request_id'] = $requestId;

        $this->boot();
        $response = $this->app->handle($request, $type, $catch);

        $response->headers->set(self::$header, $requestId);

        return $response;
    }

    /**
     * @param Request  $request
     * @param Response $response
     */
    public function terminate(Request $request, Response $response)
    {
        $this->app->terminate($request, $response);
    }

    private function registerRoutes()
    {
        $this->app->register(new ControllersServiceProvider());
        $this->app->mount('/', new ApiControllerProvider());

        // TODO add your other routes mounting here
    }
}
