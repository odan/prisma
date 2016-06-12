<?php

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

$options = $this->config['session'];
$storage = new NativeSessionStorage($options, new NativeFileSessionHandler());
$session = new Session($storage);
$session->start();

return $session;
