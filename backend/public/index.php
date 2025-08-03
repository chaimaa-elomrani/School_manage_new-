<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

use Core\Router;
use App\Middleware\CorsMiddleware;

// Handle CORS 
$corsMiddleware = new CorsMiddleware();
$corsMiddleware->handle();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Initialize router
$router = new Router();
require_once __DIR__ . '/../routes/web.php';

// Remove /backend from request URI if present
$requestUri = str_replace('/backend', '', $_SERVER['REQUEST_URI']);

// Dispatch the route
$router->dispatch($requestUri, $_SERVER['REQUEST_METHOD']);