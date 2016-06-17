<?php

// Add routes: httpMethod, route, handler
$routes = [];

// Default page
$routes[] = ['GET', '/', 'App\Controller\IndexController->index'];

// JSON-RPC 2.0 handler
$routes[] = ['POST', '/rpc', 'App\Controller\RpcController->index'];

// Login
$routes[] = ['GET', '/login', 'App\Controller\LoginController->login'];
$routes[] = ['POST', '/login', 'App\Controller\LoginController->loginSubmit'];
$routes[] = ['GET', '/logout', 'App\Controller\LoginController->logout'];

// Controller action
// Object method call with Class->method
$routes[] = ['GET', '/users', 'App\Controller\UserController->index'];

// {id} must be a number (\d+)
$routes[] = ['GET', '/user/{id:\d+}', 'App\Controller\UserController->edit'];

return $routes;
