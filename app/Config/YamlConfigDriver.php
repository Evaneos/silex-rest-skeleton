<?php

namespace Evaneos\REST\Config;

use Igorw\Silex\YamlConfigDriver as BaseYamlConfigDriver;
use Symfony\Component\Yaml\Yaml;

class YamlConfigDriver extends BaseYamlConfigDriver
{
    public function load($filename)
    {
        if (!class_exists('Symfony\\Component\\Yaml\\Yaml')) {
            throw new \RuntimeException('Unable to read yaml as the Symfony Yaml Component is not installed.');
        }

        $config = Yaml::parse(file_get_contents($filename));

        return $config ?: [];
    }
}
