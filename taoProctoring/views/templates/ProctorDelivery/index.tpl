<div class="container <?= get_data('cls'); ?>"<?php
    foreach(get_data('data') as $name => $value) {
        echo ' data-' . $name . '="' .(is_string($value) ? $value : _dh(json_encode($value))) . '"';
    }
?>>
    <div class="header"></div>
    <div class="content">
        <h1><?= get_data('title'); ?></h1>
        <div class="list"></div>
    </div>
</div>
