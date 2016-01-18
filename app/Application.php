<?php
namespace Evaneos\REST;

use Doctrine\Common\Cache\ApcuCache;
use Silex\Application as SilexApplication;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Silex\Provider\MonologServiceProvider;
use Igorw\Silex\ConfigServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Evaneos\REST\ServiceProviders\SecurityJWTServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Evaneos\REST\ServiceProviders\RestAPIServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Silex\Provider\ServiceControllerServiceProvider;
use Evaneos\REST\API\ControllerProviders\ApiControllerProvider;
use Evaneos\REST\API\Exceptions\BadRequestException;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Whoops\Provider\Silex\WhoopsServiceProvider;
use Symfony\Component\HttpFoundation\Response;
use Evaneos\REST\ServiceProviders\CommandServiceProvider;

class Application extends SilexApplication
{
    private $rootDir;
    
    /**
     * Constructor
     *
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        parent::__construct($values);
        
        $this->rootDir = __DIR__.'/..';
        $app = $this;
        $app['root_dir'] = $this->rootDir;

        $app->register(new ConfigServiceProvider($this->rootDir . '/config/config.yml'));

        $app['cache_dir'] = $this->rootDir.'/'.$app['config']['cache_dir'];
        $app['log_dir'] = $this->rootDir.'/'.$app['config']['log_dir'];
        $app['debug'] = $app['config']['debug'];

        // keep all in one place to avoid confusion
        $config = $app['config'];
        unset($config['cache_dir']);
        unset($config['log_dir']);
        unset($config['debug']);
        $app['config'] = $config;

        // Logger
        $app->register(new MonologServiceProvider(), array(
            'monolog.logfile' => $app['config']['log.file'],
            'monolog.name' => $app['config']['log.name']
        ));
        
        $app->register(new ValidatorServiceProvider());
        $app['validator.mapping.class_metadata_factory'] = $app->share(function ($app) {
            foreach (spl_autoload_functions() as $fn) {
                AnnotationRegistry::registerLoader($fn);
            }
            $reader = new AnnotationReader();
            $loader = new AnnotationLoader($reader);

            //@TODO improve this
            $cache  = extension_loaded('apc') ? new ApcuCache() : null;
            return new LazyLoadingMetadataFactory($loader, $cache);
        });
        
        $app->register(new DoctrineServiceProvider(), [
            'db.options' => [
                'driver' => $app['config']['database.driver'],
                'host' => $app['config']['database.host'],
                'dbname' => $app['config']['database.dbname'],
                'user' => $app['config']['database.user'],
                'password' => $app['config']['database.password']
            ]
        ]);

        $app->register(new DoctrineOrmServiceProvider(), [
            'orm.proxies_dir' => $app['cache_dir'].'/proxies',
            'orm.em.options' => [
                'mappings' => [] // add your mappings
            ]
        ]);
        
        // Domain services
        $app->addDomainServices();
    }
    
    public function bootHttpApi()
    {
        $app = $this;
        
        // Security
        if ($app['config']['security.enabled']) {
            $app['security.firewalls'] = [
                'all' => [
                    'stateless' => true,
                    'pattern' => '^.*$',
                    'jwt' => [
                        'secret_key' => $app['config']['security.jwt_secret_key'],
                        'allowed_algorithms' => ['HS256']
                    ]
                ]
            ];
            
            $app->register(new SecurityServiceProvider());
            $app['security.voters'] = $app->extend('security.voters', function($voters) use($app) {
                // add your voters here
                return $voters;
            });
        }
        
        // HTTP
        $app->register(new SecurityJWTServiceProvider());
        $app->register(new UrlGeneratorServiceProvider());
        $app->register(new RestAPIServiceProvider());
        
        $app->before(function (Request $request) {
            if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
                $data = json_decode($request->getContent(), true);
                $request->request->replace(is_array($data) ? $data : array());
            }
        });
        
        $app->register(new ServiceControllerServiceProvider());
        
        // Routes mounting
        $app->mountRoutes();
    
        $app->error(function(BadRequestException $invalidRequest) use ($app) {
            return $app['api.response.builder']->buildResponse($invalidRequest->getErrors(), Response::HTTP_BAD_REQUEST);
        });
    
        // Debug management
        if ($app['debug']) {
            $app->register(new WhoopsServiceProvider());
        }

        $app->run();
    }
    
    public function bootCLI()
    {
        $app = $this;
        
        $app->register(new CommandServiceProvider());
        $app->boot();
        
        $application = new \Symfony\Component\Console\Application();
        
        $application->add($app['command.default']);
        // TODO add your other commands here
        
        $application->run();
    }
    
    private function addDomainServices()
    {
        $app = $this;
        
        // TODO add your domain services here
    }
    
    private function mountRoutes()
    {
        $app = $this;
    
        $app->mount('/', new ApiControllerProvider());
        // TODO add your other routes mounting here
    }
}
