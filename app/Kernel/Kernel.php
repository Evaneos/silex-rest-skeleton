<?php

namespace Evaneos\REST\Kernel;

use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Cache\ApcuCache;
use Igorw\Silex\ConfigServiceProvider;
use Igorw\Silex\YamlConfigDriver;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;

abstract class Kernel implements KernelInterface
{
    /** @var string */
    private $env;

    /** @var bool */
    private $debug;

    /** @var Application */
    protected $app;

    const VERSION = '1.0.0';
    const VERSION_ID = 100000;
    const NAME = 'Silex Skeleton';

    /**
     * Kernel constructor.
     *
     * @param $env
     * @param $debug
     */
    public function __construct($env, $debug)
    {
        $this->env = $env;
        $this->debug = $debug;

        $this->app = new Application(array(
            'root_dir' => __DIR__ . '/../..',
        ));
    }

    /**
     * @return string
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @param bool $id
     *
     * @return int|string
     */
    public function getVersion($id = false)
    {
        return true === $id ? static::VERSION_ID : static::VERSION;
    }

    abstract protected function doBoot();

    public function boot()
    {
        $this->app->register(new ConfigServiceProvider($this->app['root_dir'] . '/config/config_' . $this->getEnv() . '.yml', array(), new YamlConfigDriver()));

        $this->app['cache_dir'] = $this->app['root_dir'] . '/' . $this->app['config']['cache_dir'];
        $this->app['log_dir'] = $this->app['root_dir'] . '/' . $this->app['config']['log_dir'];
        $this->app['debug'] = $this->debug;
        $this->app['env'] = $this->env;

        // keep all in one place to avoid confusion
        $config = $this->app['config'];
        unset($config['cache_dir']);
        unset($config['log_dir;']);
        unset($config['env']);
        unset($config['debug']);
        $this->app['config'] = $config;

        if ('php://' !== substr($this->app['config']['log.file'], 0, 6)) {
            $logFile = $this->app['config']['log.file'] = $this->app['log_dir'] . '/' . $this->app['config']['log.file'];
        } else {
            $logFile = $this->app['config']['log.file'];
        }

        // Logger
        $this->app->register(new MonologServiceProvider(), array(
            'monolog.logfile' => $logFile,
            'monolog.name' => $this->app['config']['log.name'],
        ));

        $this->app->register(new ValidatorServiceProvider());
        $this->app['validator.mapping.class_metadata_factory'] = $this->app->share(function ($app) {

            foreach (spl_autoload_functions() as $fn) {
                AnnotationRegistry::registerLoader($fn);
            }

            $reader = new AnnotationReader();
            $loader = new AnnotationLoader($reader);

            //@TODO improve this
            $cache = extension_loaded('apc') ? new ApcuCache() : null;

            return new LazyLoadingMetadataFactory($loader, $cache);
        });

        $this->app->register(new DoctrineServiceProvider(), [
            'db.options' => [
                'driver' => $this->app['config']['database.driver'],
                'host' => $this->app['config']['database.host'],
                'dbname' => $this->app['config']['database.dbname'],
                'user' => $this->app['config']['database.user'],
                'password' => $this->app['config']['database.password'],
            ],
        ]);

        $this->app->register(new DoctrineOrmServiceProvider(), [
            'orm.proxies_dir' => $this->app['cache_dir'] . '/proxies',
            'orm.em.options' => [
                'mappings' => [], // add your mappings
            ],
        ]);

        $this->doBoot();

        $this->app->boot();
    }

    /**
     * @return Application
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return static::NAME;
    }
}
