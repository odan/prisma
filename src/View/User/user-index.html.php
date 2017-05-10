<?php
$this->layout('view::Layout/layout.html.php', $this->data);

$this->push('assets');
//echo $this->assets(['view::User/user-index.js'], ['inline' => false, 'name' => 'user-index.js']);
//echo $this->assets(['view::User/user-index.css'], ['inline' => false, 'name' => 'user-index.css']);
$this->end();
?>
<div id="content" class="container">
    <div class="row">
        <div class="col-md-12">
            <h1><?= $this->e(__('Users index')); ?></h1>

            User 1234: <a href="users/1234">users/1234/</a><br>
            User 1234 Reviews: <a href="users/1234/reviews">users/1234/reviews</a><br>
        </div>
    </div>
</div>
