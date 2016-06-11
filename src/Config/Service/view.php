<?php

use League\Plates\Engine;

$engine = new Engine($this->config['view']['view_path'], null);

// Add folder shortcut (assets::file.js)
$engine->addFolder('assets', $this->config['view']['assets_path']);
$engine->addFolder('view', $this->config['view']['view_path']);

return $engine;
