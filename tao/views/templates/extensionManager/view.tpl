<?php
use oat\tao\helpers\Template;
?><link rel="stylesheet" type="text/css" href="<?= Template::css('extensionManager.css') ?>" />

<? if(isset($message)): ?>
<div id="message">
    <pre><?= $message; ?></pre>
</div>
<? endif; ?>
<div class="data-container-wrapper">
    <div class="grid-row">
        <div class="col-6">
            <h2><?= __('Installed Extensions') ?></h2>
            <div id="extensions-manager-container" class="form-content">
                <table summary="modules" class="matrix">
                    <thead>
                        <tr>
                            <th class="bordered"></th>
                            <th class="bordered author"><?= __('Author'); ?></th>
                            <th class="version"><?= __('Version'); ?></th>
                            <!-- <th><?= __('Loaded'); ?></th>  -->
                            <!-- <th><?= __('Loaded at Startup'); ?></th> -->
                        </tr>
                    </thead>
                    <tbody>
                    <? foreach(get_data('installedExtArray') as $extensionObj): ?>
                    <? if($extensionObj->getId() !=null): ?>
                        <tr>
                            <td class="ext-id bordered"><?= $extensionObj->getName(); ?></td>
                            <td class="author"><?= str_replace(',', '<br />', $extensionObj->getAuthor()) ; ?></td>
                            <td class="version"><?= $extensionObj->getVersion(); ?></td>
                        </tr>
                    <? endif; ?>
                    <? endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-6">
            <h2><?= __('Available Extensions') ?></h2>
            <div id="available-extensions-container">
                <? if (count(get_data('availableExtArray')) > 0): ?>
                <form action="<?= _url('install', 'ExtensionsManager'); ?>" metdod="post">
                    <table summary="modules" class="matrix">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="author"><?= __('Author'); ?></th>
                                <th class="version"><?= __('Version'); ?></th>
                                <th class="require"><?= __('Requires'); ?></th>
                                <th class="install"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <? foreach(get_data('availableExtArray') as $k => $ext): ?>
                            <tr id="<?= $ext->getId();?>">
                                <td class="ext-name"><?= $ext->getName(); ?></td>
                                <td class="author"><?= $ext->getAuthor(); ?></td>
                                <td class="version"><?= $ext->getVersion(); ?></td>
                                <td class="dependencies">
                                    <ul class="plain">
                                    <? foreach ($ext->getDependencies() as $req => $version): ?>
                                        <li class="ext-id ext-<?= $req ?><?= array_key_exists($req, get_data('installedExtArray')) ? ' installed' : '' ?>" rel="<?= $req ?>"><?= $req ?></li>
                                    <? endforeach; ?>
                                    </ul>
                                </td>
                                <td class="install">
                                    <input name="ext_<?= $ext->getId();?>" type="checkbox" />
                                </td>
                            </tr>
                            <? endforeach; ?>
                        </tbody>
                    </table>
                    <div class="actions">
                        <input class="install btn-info" id="installButton" name="install_extension" value="<?= __('Install') ?>" type="submit" disabled="disabled" />
                    </div>
                </form>
                <? else: ?>
                <div id="noExtensions" class="ui-state-highlight">
                    <?= __('No extensions available.') ?>
                </div>
                <? endif; ?>
            </div>

        </div>
    </div>

</div>
<div id="installProgress" title="<?= __('Installation...') ?>">
    <div class="progress"><div class="bar"></div></div>
    <p class="status">...</p>
    <div class="console"></div>
</div>
