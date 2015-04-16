<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoMediaManager\actions;

use oat\taoMediaManager\model\FileImporter;
use oat\taoMediaManager\model\MediaService;
use oat\taoMediaManager\model\SharedStimulusImporter;
use oat\taoMediaManager\model\ZipImporter;

/**
 * This controller provide the actions to import medias
 */
class MediaImport extends \tao_actions_Import {

    private $importHandlers;

    public function __construct(){

        parent::__construct();
        $this->service = MediaService::singleton();
    }

    /**
     * overwrite the parent index to add the import handlers
     * 
     * @requiresRight id WRITE
     * @see tao_actions_Import::index()
     */
    public function index()
    {
        $id = null;
        if($this->hasRequestParameter('instanceUri')){
            $id = \tao_helpers_Uri::decode($this->getRequestParameter('instanceUri'));
        }
        else if($this->hasRequestParameter('uri')){
            $id = \tao_helpers_Uri::decode($this->getRequestParameter('uri'));
        }

        $this->importHandlers = array(
            new FileImporter($id),
            new SharedStimulusImporter($id)
        );
        parent::index();

    }

    protected function getAvailableImportHandlers() {
        return $this->importHandlers;
    }



    /**
     * get the main class
     * @return \core_kernel_classes_Class
     */
    protected function getRootClass()
    {
        return new \core_kernel_classes_Class(MEDIA_URI);
    }
}
