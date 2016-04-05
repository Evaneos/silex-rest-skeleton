<?php

namespace Evaneos\REST\ServiceProviders;

use Igorw\Silex\ChainConfigDriver;
use Igorw\Silex\ConfigDriver;
use Igorw\Silex\JsonConfigDriver;
use Igorw\Silex\PhpConfigDriver;
use Igorw\Silex\TomlConfigDriver;
use Igorw\Silex\YamlConfigDriver;
use Silex\Application;
use Silex\ServiceProviderInterface;

class ConfigServiceProvider implements ServiceProviderInterface
{
    /**
     * @var \Igorw\Silex\ConfigServiceProvider
     */
    protected $configService;

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var array
     */
    protected $replacements;

    /**
     * @var string|null
     */
    protected $prefix;

    /** @var ConfigDriver  */
    protected $driver;

    /**
     * ConfigServiceProvider constructor.
     *
     * @param string            $filename
     * @param array             $replacements
     * @param ConfigDriver|null $driver
     * @param string|null       $prefix
     */
    public function __construct($filename, array $replacements = array(), ConfigDriver $driver = null, $prefix = null)
    {
        $this->filename = $filename;
        $this->prefix = $prefix;

        if ($replacements) {
            foreach ($replacements as $key => $value) {
                $this->replacements['%' . $key . '%'] = $value;
            }
        }

        $this->driver = $driver ?: new ChainConfigDriver(array(
            new PhpConfigDriver(),
            new YamlConfigDriver(),
            new JsonConfigDriver(),
            new TomlConfigDriver(),
        ));
    }

    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        $file = new \SplFileInfo($this->filename);
        $cacheDir = $app['cache_dir'] . '/config';
        $cachedConfig = $cacheDir . '/' . $file->getFilename() . '.cache';
        $hash = md5(file_get_contents($this->filename));

        /* Require warmable cache system to do that, may be in the future
        if('prod' === $app['env']) {
            $config = require $cachedConfig;
            if($hash === $config['hash']){
                unset($config['hash']);
            }
            $this->merge($app, $config);
            return;
        }*/

        $isFresh = false;

        if (!file_exists($cacheDir)) {
            mkdir($cacheDir, 0777);
        }

        if (file_exists($cachedConfig)) {
            $config = require $cachedConfig;

            if ($hash === $config['hash']) {
                $isFresh = true;
                unset($config['hash']);
            }
        }

        if (!$isFresh) {
            $config = $this->readConfig();
            file_put_contents($cachedConfig, '<?php return ' . var_export($config + array('hash' => $hash), true) . ' ?>');
        }

        foreach ($config as $name => $value) {
            if ('%' === substr($name, 0, 1)) {
                $this->replacements[$name] = (string) $value;
            }
        }

        $this->merge($app, $config);
    }

    private function readConfig()
    {
        if (!$this->filename) {
            throw new \RuntimeException('A valid configuration file must be passed before reading the config.');
        }

        if (!file_exists($this->filename)) {
            throw new \InvalidArgumentException(
                sprintf("The config file '%s' does not exist.", $this->filename));
        }

        if ($this->driver->supports($this->filename)) {
            return $this->driver->load($this->filename);
        }

        throw new \InvalidArgumentException(
            sprintf("The config file '%s' appears to have an invalid format.", $this->filename));
    }

    public function boot(Application $app)
    {
    }

    /**
     * @param Application $app
     * @param array       $config
     */
    private function merge(Application $app, array $config)
    {
        if ($this->prefix) {
            $config = array($this->prefix => $config);
        }

        foreach ($config as $name => $value) {
            if (isset($app[$name]) && is_array($value)) {
                $app[$name] = $this->mergeRecursively($app[$name], $value);
            } else {
                $app[$name] = $this->doReplacements($value);
            }
        }
    }

    /**
     * @param array $currentValue
     * @param array $newValue
     *
     * @return array
     */
    private function mergeRecursively(array $currentValue, array $newValue)
    {
        foreach ($newValue as $name => $value) {
            if (is_array($value) && isset($currentValue[$name])) {
                $currentValue[$name] = $this->mergeRecursively($currentValue[$name], $value);
            } else {
                $currentValue[$name] = $this->doReplacements($value);
            }
        }

        return $currentValue;
    }

    /**
     * @param $value
     *
     * @return array|string
     */
    private function doReplacements($value)
    {
        if (!$this->replacements) {
            return $value;
        }

        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->doReplacements($v);
            }

            return $value;
        }

        if (is_string($value)) {
            return strtr($value, $this->replacements);
        }

        return $value;
    }
}
