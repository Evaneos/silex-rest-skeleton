<?php

namespace Evaneos\REST\Tests\app\Kernel;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Evaneos\REST\Kernel\KernelInterface;
use Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmozart\PathUtil\Path;

abstract class AbstractKernel extends \PHPUnit_Framework_TestCase
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var string
     */
    protected $rootDir;

    public function setUp()
    {
        parent::setUp();
        $this->rootDir = Path::canonicalize(Path::join(__DIR__, '/../../../app/Kernel')).'/../..';
    }

    /**
     * @test
     */
    public function it_should_implement_kernel_interface()
    {
        $this->assertInstanceOf(KernelInterface::class, $this->kernel);
    }

    /**
     * @test
     */
    public function it_should_load_cache_dir_from_env()
    {
        putenv('SILEX_SKT_CACHE_DIR=/cache/test');
        $this->assertEquals($this->rootDir.'/cache/test', $this->kernel->getCacheDir());
        putenv('SILEX_SKT_CACHE_DIR');
    }

    /**
     * @test
     */
    public function it_should_load_log_dir_from_env()
    {
        putenv('SILEX_SKT_LOG_DIR=/log/test');
        $this->assertEquals($this->rootDir.'/log/test', $this->kernel->getLogDir());
        putenv('SILEX_SKT_LOG_DIR');
    }

    /**
     * @test
     */
    public function it_should_have_root_dir()
    {
        $this->assertEquals($this->rootDir, $this->kernel->getRootDir());
    }

    /**
     * @test
     */
    public function it_should_have_default_log_dir()
    {
        $this->assertEquals($this->rootDir.'/log', $this->kernel->getLogDir());
    }

    /**
     * @test
     */
    public function it_should_have_default_cache_dir()
    {
        $this->assertEquals($this->rootDir.'/cache', $this->kernel->getCacheDir());
    }

    /**
     * @test
     */
    public function it_should_have_version()
    {
        $this->assertEquals('1.0.0', $this->kernel->getVersion());
    }

    /**
     * @test
     */
    public function it_should_have_version_id()
    {
        $this->assertEquals('100000', $this->kernel->getVersion(true));
    }

    /**
     * @test
     */
    public function it_should_have_env()
    {
        $this->assertEquals('test', $this->kernel->getEnv());
    }

    /**
     * @test
     */
    public function it_should_boot()
    {
        $this->assertNotTrue(\PHPUnit_Framework_Assert::getObjectAttribute($this->kernel, 'booted'));
        $this->kernel->boot();
        $this->assertTrue(\PHPUnit_Framework_Assert::getObjectAttribute($this->kernel, 'booted'));
    }

    /**
     * @return \Silex\Application
     */
    protected function getBootedApp()
    {
        $this->kernel->boot();
        return $this->kernel->getApp();
    }

    /**
     * @test
     */
    public function it_should_have_application_configured()
    {
        $this->assertEquals($this->getBootedApp()->offsetGet('config'), array(
            'security.enabled' => true,
            'security.jwt_secret_key' => 'secret',
            'database.driver' => 'pdo_pgsql',
            'database.dbname' => 'common',
            'database.host' => 'postgres',
            'database.user' => 'postgres',
            'database.password' => 'postgres',
            'log.file' => 'php://stdout',
            'log.name' => 'APP',
            'api.max_pagination_limit' => 50,
            'api.default_pagination_limit' => 20
        ));
    }

    /**
     * @test
     */
    public function it_should_have_monolog_loaded()
    {
        $app = $this->getBootedApp();

        $this->assertInstanceOf(LoggerInterface::class, $app['logger']);
        $this->assertEquals($app['logger']->getName(), 'APP');
    }

    /**
     * @test
     */
    public function it_should_have_validator_loaded()
    {
        $app = $this->getBootedApp();

        /** @var ValidatorInterface $validator */
        $this->assertInstanceOf(ValidatorInterface::class, $app['validator']);
        $this->assertInstanceOf(MetadataFactoryInterface::class, $app['validator.mapping.class_metadata_factory']);
    }

    /**
     * @test
     */
    public function it_should_have_doctrine_dbal_loaded()
    {
        $app = $this->getBootedApp();

        $connection = $app['db'];

        $this->assertInstanceOf(Connection::class, $connection);
        $this->assertEquals($app['dbs.options'], array(
            'default' => array(
                'driver' => 'pdo_pgsql',
                'dbname' => 'common',
                'host' => 'postgres',
                'user' => 'postgres',
                'password' => 'postgres'
            )
        ));
    }

    /**
     * @test
     */
    public function it_should_have_doctrine_orm_loaded()
    {
        $app = $this->getBootedApp();

        $this->assertInstanceOf(EntityManagerInterface::class, $app['orm.em']);
    }

    public function tearDown()
    {
        $this->kernel = null;
        $this->rootDir = null;
        parent::tearDown();
    }
}
