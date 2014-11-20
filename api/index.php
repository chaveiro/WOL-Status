<?php

require 'RestServer/RestServer.php';
require 'APIController.php';

spl_autoload_register(); // don't load our classes unless we use them

$server = new RestServer('debug'); // 'debug' or 'production'
// $server->refreshCache(); // uncomment momentarily to clear the cache if classes change in production mode

$server->addClass('APIController');
//$server->addClass('ProductsController', '/products'); // adds this as a base to all the URLs in this class
$server->handle();
