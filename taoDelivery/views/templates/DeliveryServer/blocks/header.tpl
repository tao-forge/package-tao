<?php
use oat\tao\helpers\Layout;
use oat\tao\helpers\Template;
use oat\tao\model\theme\Theme;
?>
<?php Template::inc('blocks/careers.tpl', 'tao'); ?>
<header class="dark-bar clearfix">
    <?=Layout::renderThemeTemplate(Theme::CONTEXT_FRONTOFFICE, 'header-logo')?>
    <div class="lft title-box"></div>
    <nav class="rgt">
        <!-- snippet: dark bar left menu -->
        <div class="settings-menu">
            <ul class="clearfix plain">
                <li data-control="home">
                    <a id="home" href="<?=get_data('returnUrl')?>" tabindex="0" role="button">
                        <span class="icon-home"></span>
                        <span class="text hidden"><?= __("Tests Home"); ?></span>
                    </a>
                </li>
                <li class="infoControl sep-before">
                    <span class="a">
                        <span class="icon-test-taker"></span>
                        <span class="text"><?= get_data('userLabel'); ?></span>
                    </span>
                </li>
                <li class="infoControl sep-before" data-control="logout">
                    <a id="logout" class="" href="<?= get_data('logout') ?>" tabindex="0" role="button">
                        <span class="icon-logout"></span>
                        <span class="text"><?= __("Logout"); ?></span>
                    </a>
                </li>
                <li class="infoControl sep-before hidden" data-control="exit">
                    <a href="#" tabindex="0" role="button">
                        <span class="icon-logout"></span>
                        <span class="text"><?= __("Exit"); ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</header>
