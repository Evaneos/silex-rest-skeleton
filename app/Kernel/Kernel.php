<?php

namespace Evaneos\REST\Kernel;

use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Cache\ApcuCache;
use Evaneos\REST\Config\CacheConfigDriver;
use Igorw\Silex\ConfigServiceProvider;
use Igorw\Silex\YamlConfigDriver;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\TagProcessor;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;

class Kernel implements KernelInterface
{
    /** @var string */
    private $env;

    /** @var bool */
    private $debug;

    /** @var Application */
    protected $app;

    /** @var bool  */
    protected $booted;

    /** @var string */
    protected $rootDir;

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
        $this->booted = false;
        $this->rootDir = __DIR__ . '/../..';

        $this->app = new Application([
            'root_dir' => $this->rootDir,
            'cache_dir' => $this->getCacheDir(),
            'log_dir' => $this->getLogDir(),
            'env' => $env,
            'debug' => $debug,
        ]);
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

    protected function getLogDir()
    {
        if (false === $dir = getenv('SILEX_SKT_LOG_DIR')) {
            $dir = '/log';
        }

        return $this->rootDir . $dir;
    }

    protected function getCacheDir()
    {
        if (false === $dir = getenv('SILEX_SKT_CACHE_DIR')) {
            $dir = '/cache';
        }

        return $this->rootDir . $dir;
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

    protected function doBoot()
    {
    }

    public function boot()
    {
        //Avoid to boot many times
        if (true === $this->booted) {
            return;
        }

        $filename = $this->app['root_dir'] . '/config/config_' . $this->getEnv() . '.yml';

        if (true === $this->debug && !file_exists($filename)) {
            throw new \Exception('Unable to config file ' . $filename);
        }

        $this->app->register(new ConfigServiceProvider($filename, array(), new CacheConfigDriver(new YamlConfigDriver(), $this->app['cache_dir'])));

        if (true === $this->debug) {
            foreach ([$this->app['cache_dir'], $this->app['log_dir']] as $dir) {
                if (!file_exists($dir)) {
                    mkdir($dir, 0777);
                }

                if (!is_writable($dir)) {
                    throw new \Exception(sprintf(
                        'Directory "%s" is not writable',
                        $dir
                    ));
                }
            }
        }

        if (isset($this->app['config']['log.dir'])) {
            $this->app['log_dir'] = $this->app['config']['log.dir'];
        }

        //Prevent to prepend php stream
        if ('php://' !== substr($this->app['config']['log.file'], 0, 6)) {
            $logFile = $this->app['log_dir'] . '/' . $this->app['config']['log.file'];
        } else {
            $logFile = $this->app['config']['log.file'];
        }

        // Logger
        $this->app->register(new MonologServiceProvider(), [
            'monolog.logfile' => $logFile,
            'monolog.name' => $this->app['config']['log.name'],
            ''
        ]);

        $this->app['monolog.processor.tag'] = $this->app->share(function () {
            return new TagProcessor(['debug' => $this->debug]);
        });

        $this->app->extend('monolog', function (Logger $logger) {
            $introspectionProcessor = new IntrospectionProcessor(Logger::WARNING);
            $memoryProcess = new MemoryUsageProcessor();

            $logger->pushProcessor($this->app['monolog.processor.tag']);
            $logger->pushProcessor($introspectionProcessor);
            $logger->pushProcessor($memoryProcess);

            return $logger;
        });

        if (!$this->debug) {
            $this->app->extend('monolog.handler', function (HandlerInterface $handler) {
                $handler->setFormatter(new JsonFormatter());
                return $handler;
            });
        }

        $this->app->register(new ValidatorServiceProvider());
        $this->app['validator.mapping.class_metadata_factory'] = $this->app->share(function () {

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
                'mappings' => $this->getDoctrineMappings(),
            ],
        ]);

        $this->registerDomainServices();
        $this->doBoot();

        $this->app->boot();
        $this->booted = true;
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

    /**
     * @return void
     */
    protected function registerDomainServices()
    {
        // TODO add your domain services here
    }

    /**
     * @return array
     */
    protected function getDoctrineMappings()
    {
        return [];
    }
}
