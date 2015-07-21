<?php
use oat\tao\helpers\Template;
use oat\tao\helpers\Layout;
?>
<!doctype html>
<html class="no-js no-version-warning">
<head>
    <script src="<?= Template::js('lib/modernizr-2.8/modernizr.js', 'tao')?>"></script>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Layout::getTitle() ?></title>
    <link rel="shortcut icon" href="<?= Template::img('img/favicon.ico') ?>"/>

    <script id="amd-loader" src="<?=Template::js('lib/require.js', 'tao')?>" data-controller="<?=BASE_WWW.'js/controller/CompatibilityChecker/'?>"
            data-main="<?=BASE_WWW.'js/index'?>" data-config="<?=get_data('clientConfigUrl')?>"></script>
    <link rel='stylesheet' type='text/css' href="<?= Template::css('diagnostics.css') ?>" />
    <?= tao_helpers_Scriptloader::render() ?>
    <?php if (($themeUrl = Layout::getThemeUrl()) !== null): ?>
        <link rel="stylesheet" href="<?= $themeUrl ?>" />
    <?php endif; ?>

    <script>
        (function(){var p=[],w=window,d=document,e=f=0;p.push('ua='+encodeURIComponent(navigator.userAgent));e|=w.ActiveXObject?1:0;e|=w.opera?2:0;e|=w.chrome?4:0;
    e|='getBoxObjectFor' in d || 'mozInnerScreenX' in w?8:0;e|=('WebKitCSSMatrix' in w||'WebKitPoint' in w||'webkitStorageInfo' in w||'webkitURL' in w)?16:0;
    e|=(e&16&&({}.toString).toString().indexOf("\n")===-1)?32:0;p.push('e='+e);f|='sandbox' in d.createElement('iframe')?1:0;f|='WebSocket' in w?2:0;
    f|=w.Worker?4:0;f|=w.applicationCache?8:0;f|=w.history && history.pushState?16:0;f|=d.documentElement.webkitRequestFullScreen?32:0;f|='FileReader' in w?64:0;
    p.push('f='+f);p.push('r='+Math.random().toString(36).substring(7));p.push('w='+screen.width);p.push('h='+screen.height);var s=d.createElement('script');
    s.src='http://tao.dev/vendor/whichbrowser/detect.js?' + p.join('&');d.getElementsByTagName('head')[0].appendChild(s);})();
    </script>
</head>

<body>
<div id="requirement-check" class="feedback-error js-hide">
    <span class="icon-error"></span>
    <span id="requirement-msg-area"><?=__('You must activate JavaScript in your browser to run this application.')?></span>
</div>
<script src="<?= Template::js('requirement-check.js', 'taoClientDiagnostic')?>"></script>

<div class="content-wrap">

    <?php Template::inc('blocks/header.tpl', 'tao'); ?>

    <div class="diagnostics-main-area">

        <h1><?=__('Diagnostics tool')?></h1>
        <div class="intro">
            <?= __('This tool will run a number of tests in order to establish how well your current environment is suitable to run the TAO platform. Be aware that these tests will take up to several minutes.')?>
        </div>
        <div class="clearfix">
            <button data-action="launcher" class="btn-info small rgt"><?= __('Begin diagnostics')?></button>
        </div>

        <ul class="plain">
            <li data-result="browser"><?= __('Operating system and browser')?>
                <div class="feedback-success small">
                    <span class="icon-success"></span>
                    Firefox 41.0 / Windows 8.1
                </div>
            </li>
            <li data-result="bandwidth"><?= __('Bandwidth')?>
                <div>
                    <div class="quality-bar">
                        <div class="quality-indicator"></div>
                    </div>
                </div>
            </li>
            <li data-result="performance"><?= __('Performance')?>
                <div>
                    <div class="quality-bar">
                        <div class="quality-indicator"></div>
                    </div>
                </div>
            </li>
            <li data-result="total"><?= __('Total')?>
                <div>
                    <div class="quality-bar" data-result="total">
                        <div class="quality-indicator"></div>
                    </div>
                </div>
            </li>
        </ul>

    </div>

</div>

<footer class="dark-bar">
    <?php
    if (!$val = Layout::getCopyrightNotice()):
        ?>
        © 2013 - <?= date('Y') ?> · <span class="tao-version"><?= TAO_VERSION_NAME ?></span> ·
        <a href="http://taotesting.com" target="_blank">Open Assessment Technologies S.A.</a>
        · <?= __('All rights reserved.') ?>
    <?php else: ?>
        <?= $val ?>
    <?php endif; ?>
    <?php $releaseMsgData = Layout::getReleaseMsgData();
    if ($releaseMsgData['is-unstable'] || $releaseMsgData['is-sandbox']): ?>
        <span class="rgt">
            <?php if ($releaseMsgData['is-unstable']): ?>
                <span class="icon-warning"></span>

            <?php endif; ?>
            <?=$releaseMsgData['version-type']?> ·
        <a href="<?=$releaseMsgData['link']?>" target="_blank"><?=$releaseMsgData['msg']?></a></span>

    <?php endif; ?>
</footer>
<div class="loading-bar"></div>
</body>
</html>