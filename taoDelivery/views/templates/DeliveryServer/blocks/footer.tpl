<?php use oat\tao\helpers\Template; ?>
<footer class="dark-bar">
    © 2013 - <?= date('Y') ?> · <span class="tao-version"><?= TAO_VERSION_NAME ?></span> ·
    Open Assessment Technologies S.A.
    · <?= __('All rights reserved.') ?>
</footer>
<?php Template::inc('blocks/careers-js.tpl', 'tao'); ?>
