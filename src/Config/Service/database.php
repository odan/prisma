<?php

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;

return new Connection(['driver' => new Mysql($this->config['db'])]);
