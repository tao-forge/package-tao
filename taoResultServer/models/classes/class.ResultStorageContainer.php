<?php

/**
 * todo move it in the taoResults
 * Implements the Services for the storage of item and test variables,
 * This implementations depends on results for the the physical storage
 * TODO : move the impl to results services
 * @author plichart
 */
class taoResultServer_models_classes_ResultStorageContainer
    extends tao_models_classes_GenerisService
    implements taoResultServer_models_classes_ResultStorage {

    private $implementations =array(); //array

    public function __construct($implementations =array()){
		parent::__construct();
        foreach ($implementations as $key => $implementationParams) {
            $implementationClassName = $implementationParams["className"];
            $implementations[$key]["object"] =new $implementationClassName();
        }
        $this->implementations = $implementations;
        //retrieve implementations
    }
    
    public function storeRelatedTestTaker($deliveryResultIdentifier, $testTakerIdentifier) {
         foreach ($this->implementations as $implementation) {
             $implementation["object"]->storeRelatedTestTaker($deliveryResultIdentifier, $testTakerIdentifier);
         }
    }
   
    public function storeRelatedDelivery($deliveryResultIdentifier, $deliveryIdentifier) {
         foreach ($this->implementations as $implementation) {
             $implementation["object"]->storeRelatedDelivery($deliveryResultIdentifier, $deliveryIdentifier);
         }
    }

    public function storeItemVariable($deliveryResultIdentifier, $test, $item, taoResultServer_models_classes_Variable $itemVariable, $callIdItem){
        foreach ($this->implementations as $implementation) {
             $implementation["object"]->storeItemVariable($deliveryResultIdentifier, $test, $item, $itemVariable, $callIdItem);
         }
    }
 
    public function storeTestVariable($deliveryResultIdentifier, $test, taoResultServer_models_classes_Variable $testVariable, $callIdTest){
        foreach ($this->implementations as $implementation) {
             $implementation["object"]->storeTestVariable($deliveryResultIdentifier, $test, $testVariable, $callIdTest);
         }
    }

    public function configure(core_kernel_classes_Resource $resultServer, $callOptions = array()) {
        foreach ($this->implementations as $implementation) {
             if (isset($implementation["params"])) {
             $implementation["object"]->configure($resultServer, $implementation["params"]);
             }
         }
    }

    public function spawnResult(){
            //should be improved by changing the interface,
            //currently the first found implementation will generate an Id
            // to be used as a result identifier across all implementations,
            foreach ($this->implementations as $implementation) {
            $spawnedIdentifier = $implementation["object"]->spawnResult();
            if ((!is_null($spawnedIdentifier))and $spawnedIdentifier != "") {return $spawnedIdentifier;}
            }
    }

}
?>