<?php
$this->layout('view::Layout/layout.html.php', $this->data);

$this->push('assets');
//echo $this->assets(['view::User/user-edit.js'], ['inline' => false, 'name' => 'user-edit.js']);
//echo $this->assets(['view::User/user-edit.css'], ['inline' => false, 'name' => 'user-edit.css']);
$this->end();
?>
<div id="content" class="container">
    <div class="row">
        <div class="col-md-12">
            <h1><?= $this->e(__('Edit user: %s', $id)); ?></h1>
            Username: <?= $this->e($username); ?><br>
            Counter: <?= $this->e($counter); ?>
            <?php var_dump($files); ?>
        </div>
    </div>
</div>
