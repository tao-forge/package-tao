<?php

use oat\taoDeliveryRdf\model\assembly\AssemblyFilesReader;
use oat\taoDeliveryRdf\model\export\AssemblyExporterService;
use oat\tao\model\export\RdfExporter;

return new AssemblyExporterService([
    AssemblyExporterService::OPTION_ASSEMBLY_FILES_READER   => new AssemblyFilesReader(),
    AssemblyExporterService::OPTION_RDF_EXPORTER            => new RdfExporter()
]);
