<?php
$this->layout('view::Layout/layout.html.php', $this->data);

$this->push('assets');
echo $this->assets(['view::Index/index-index.js'], ['inline' => false, 'name' => 'index-index.js']);
echo $this->assets(['view::Index/index-index.css'], ['inline' => false, 'name' => 'index-index.css']);
$this->end();
?>
<div id="content" class="container">
    <div class="row">
        <div class="col-md-12">
            <h1><?= $this->e(__('Hello')); ?> <?= $this->e(__('World')); ?></h1>
            Users: <a href="users">users</a><br>
        </div>
    </div>
</div>
