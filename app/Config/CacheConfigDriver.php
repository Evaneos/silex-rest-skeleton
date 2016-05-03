<?php
namespace Evaneos\REST\Config;

use Igorw\Silex\ConfigDriver;

final class CacheConfigDriver implements ConfigDriver
{
    /**
     * @var ConfigDriver
     */
    private $driver;
    /**
     * @var
     */
    private $cacheDir;

    /**
     * CacheConfigDriver constructor.
     * @param ConfigDriver $driver
     * @param $cacheDir
     */
    public function __construct(ConfigDriver $driver, $cacheDir)
    {
        $this->driver = $driver;
        $this->cacheDir = $cacheDir;
    }

    function load($filename)
    {
        $file = new \SplFileInfo($filename);
        $cacheDir = $this->cacheDir.'/config';
        $cachedConfig = $cacheDir.'/'.$file->getFilename().'.cache';
        $hash = md5(file_get_contents($filename));

        if(!file_exists($cacheDir)){
            mkdir($cacheDir, 0750);
        }

        if(file_exists($cachedConfig)){
            $config = require $cachedConfig;

            if($hash === $config['hash']){
                unset($config['hash']);
                return $config;
            }
        }

        $config = $this->driver->load($filename);
        file_put_contents($cachedConfig, '<?php return '.var_export($config + array('hash' => $hash), true).' ?>');

        return $config;
    }

    function supports($filename)
    {
        return $this->driver->supports($filename);
    }
}