<?php

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler;

$options = $this->config['session'];

if (php_sapi_name() == "cli") {
    // In cli-mode
    $storage = new MockArraySessionStorage(new NullSessionHandler());
    $session = new Session($storage);
} else {
    // Not in cli-mode
    $storage = new NativeSessionStorage($options, new NativeFileSessionHandler());
    $session = new Session($storage);
    $session->start();
}

return $session;
