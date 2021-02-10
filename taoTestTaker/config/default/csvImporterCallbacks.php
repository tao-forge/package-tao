<?php

use oat\generis\model\GenerisRdf;
use oat\tao\model\LanguageService;
use oat\tao\model\import\CsvImporter;

return new oat\oatbox\config\ConfigurationService([
    'config' => [
        'callbacks' => [
            '*' => ['trim'],
            GenerisRdf::PROPERTY_USER_PASSWORD => [CsvImporter::class.'::taoSubjectsPasswordEncode'],
            GenerisRdf::PROPERTY_USER_DEFLG => [LanguageService::class.'::filterLanguage'],
        ],
        'use_properties_for_event' => false
    ]
]);
