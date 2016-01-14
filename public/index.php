<?php
use Evaneos\REST\Application;

require __DIR__ . '/../vendor/autoload.php';

$app = new Application();
$app->bootHttpApi();