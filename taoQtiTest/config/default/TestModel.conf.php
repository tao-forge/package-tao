<?php
/**
 * Default config header
 *
 * To replace this add a file taoQtiTest/conf/header/TestModel.conf.php
 */

return new \oat\taoQtiTest\models\TestModelService(array(
    'exportHandler' => array(
        new taoQtiTest_models_classes_export_TestExport(),
        new taoQtiTest_models_classes_export_TestExport22()
    ),
    'importHandler' => array(
        new taoQtiTest_models_classes_import_TestImport()
    )
));
