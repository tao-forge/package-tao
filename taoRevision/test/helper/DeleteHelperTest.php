<?php
/**
 * Created by Antoine on 24/02/15
 * at 13:22
 */

namespace oat\taoRevision\test\helper;


use oat\taoRevision\helper\DeleteHelper;

class DeleteHelperTest extends \PHPUnit_Framework_TestCase {

    public function testDeepDelete(){
        //create resources
        $repository = \tao_models_classes_FileSourceService::singleton()->addLocalSource("Label Test", \tao_helpers_File::createTempDir());
        $file = $repository->createFile("myTestfile.txt");

        //delete resource
        DeleteHelper::deepDelete($file);
        DeleteHelper::deepDelete($repository);

        //see if all is deleted
        //try to get the resource
        $resourceTest = new \core_kernel_classes_Resource($repository->getUri());
        $fileTest = new \core_kernel_classes_Resource($file->getUri());

        $this->assertCount(0, $resourceTest->getRdfTriples());
        $this->assertCount(0, $fileTest->getRdfTriples());
    }

    public function testDeepDeleteTriples(){
        //create resources
        $repository = \tao_models_classes_FileSourceService::singleton()->addLocalSource("Label Test", \tao_helpers_File::createTempDir());
        $file = $repository->createFile("myTestfile.txt");

        //delete resource
        DeleteHelper::deepDeleteTriples($file->getRdfTriples());
        DeleteHelper::deepDeleteTriples($repository->getRdfTriples());

        //see if all is deleted
        //try to get the resource
        $resourceTest = new \core_kernel_classes_Resource($repository->getUri());
        $fileTest = new \core_kernel_classes_Resource($file->getUri());

        $this->assertCount(0, $resourceTest->getRdfTriples());
        $this->assertCount(0, $fileTest->getRdfTriples());
    }
}
 