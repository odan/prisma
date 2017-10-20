<?php
$this->layout('view::Layout/layout.html.php', $this->data);

$this->push('assets');
echo $this->assets(['view::Home/home-index.js'], ['inline' => false, 'name' => 'home-index.js']);
echo $this->assets(['view::Home/home-index.css'], ['inline' => false, 'name' => 'home-index.css']);
$this->end();
?>
<div id="content" class="container">
    <div class="row">
        <div class="col-md-12">
            <h1><?= $this->e(__('Hello')); ?> <?= $this->e(__('World')); ?></h1>
            Users: <a href="users">users</a><br>
            URL: <?= $this->e($url); ?><br>
        </div>
    </div>
</div>
