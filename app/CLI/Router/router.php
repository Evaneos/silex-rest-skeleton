<?php

if (is_file($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $_SERVER['SCRIPT_NAME'])) {
    return false;
}

$_SERVER = array_merge($_SERVER, $_ENV);
$_SERVER['SCRIPT_FILENAME'] = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'index.php';

require 'index.php';
