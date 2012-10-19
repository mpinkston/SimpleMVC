<?php
define('LIB_PATH', __DIR__ . '/../library');

require LIB_PATH . '/Application.php';

$application = new Application();
$application->run();