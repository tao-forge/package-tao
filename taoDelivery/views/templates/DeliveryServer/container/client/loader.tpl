<?php
use oat\tao\helpers\Layout;
use oat\tao\helpers\Template;
?>

<?= Layout::getAmdLoader(
    Template::js('loader/qtiTestRunner.min.js', 'taoQtiTest'),
    'taoQtiTest/controller/runner/runner',
    [
        'exitUrl'              => get_data('returnUrl'),
        'testDefinition'       => get_data('testDefinition'),
        'testCompilation'      => get_data('testCompilation'),
        'serviceCallId'        => get_data('serviceCallId'),
        'deliveryServerConfig' => get_data('deliveryServerConfig'),
        'bootstrap'            => get_data('bootstrap'),
        'plugins'              => get_data('plugins')
    ]
); ?>
