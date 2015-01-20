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
namespace oat\taoMediaManager\model;


use oat\tao\model\media\MediaBrowser;
use oat\taoMediaManager\model\fileManagement\FileManager;

class MediaManagerBrowser implements MediaBrowser{

    private $lang;
    private $rootClassUri;

    /**
     * get the lang of the class in case we want to filter the media on language
     * @param $data
     */
    public function __construct($data){
        \common_ext_ExtensionsManager::singleton()->getExtensionById('taoMediaManager');
        $this->lang = (isset($data['lang'])) ? $data['lang'] : '';
        $this->rootClassUri = (isset($data['rootClass'])) ? $data['rootClass'] : MEDIA_URI;
    }

    /**
     * @param string $relPath
     * @param array $acceptableMime
     * @param int $depth
     * @return array
     */
    public function getDirectory($relPath = '/', $acceptableMime = array(), $depth = 1)
    {
        if($relPath == '/'){
            $class = new \core_kernel_classes_Class($this->rootClassUri);
            $relPath = '';
        }
        else{
            if(strpos($relPath,'/') === 0){
                $relPath = substr($relPath,1);
            }
            $class = new \core_kernel_classes_Class($relPath);
        }

        if($class->getUri() !== $this->rootClassUri){
            $path = array($class->getLabel());
            foreach($class->getParentClasses(true) as $parent){
                if($parent->getUri() === $this->rootClassUri){
                    $path[] = 'mediamanager';
                    break;
                }
                $path[] = $parent->getLabel();
            }
            $path = array_reverse($path);
        }
        $data = array(
            'path' => 'mediamanager/'.$relPath,
            'relPath' => (isset($path))?implode('/',$path):'mediamanager/',
            'label' => $class->getLabel()
        );

        if ($depth > 0 ) {
            $children = array();
            foreach ($class->getSubClasses() as $subclass) {
                $children[] = $this->getDirectory($subclass->getUri(), $acceptableMime, $depth - 1);

            }

            //add a filter for example on language (not for now)
            $filter = array(
            );

            foreach($class->searchInstances($filter) as $instance){
                $thing = $instance->getUniquePropertyValue(new \core_kernel_classes_Property(MEDIA_LINK));
                $link = $thing instanceof \core_kernel_classes_Resource ? $thing->getUri() : (string)$thing;
                $file = $this->getFileInfo($link, $acceptableMime);
                if(!is_null($file)){
                    //add the alt text to file array
                    $altArray = $instance->getPropertyValues(new \core_kernel_classes_Property(MEDIA_ALT_TEXT));
                    if(count($altArray) > 0){
                        $file['alt'] = $altArray[0];
                    }
                    $children[] = $file;
                }

            }
            $data['children'] = $children;
        }
        else{
            $data['url'] = _url('files', 'ItemContent', 'taoItems', array('lang' => $this->lang, 'path' => $relPath));
        }
        return $data;


    }

    /**
     * @param string $relPath
     * @param array $acceptableMime
     * @return array
     */
    public function getFileInfo($relPath, $acceptableMime)
    {
        $file = null;
        $fileManagement = FileManager::getFileManagementModel();
        $filePath = $fileManagement->retrieveFile($relPath);
        $mime = \tao_helpers_File::getMimeType($filePath);

        if((count($acceptableMime) == 0 || in_array($mime, $acceptableMime)) && file_exists($filePath)){
            $file = array(
                'name' => basename($filePath),
                'identifier' => 'mediamanager/',
                'relPath' => $relPath,
                'mime' => $mime,
                'size' => filesize($filePath),
                'url' => _url('download', 'ItemContent', 'taoItems', array('path' => 'mediamanager/'.$relPath))
            );
        }
        return $file;

    }

    /**
     * @param string $filename
     * @return string path of the file to download
     */
    public function download($filename)
    {
        \tao_helpers_Http::returnFile($filename);
    }
}