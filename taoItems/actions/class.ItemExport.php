<?php
/**
 * This controller provide the actions to export items 
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 * @subpackage action
 *
 */
class taoItems_actions_ItemExport extends tao_actions_Export {

	/**
	 * constructor used to override the formContainer
	 */
	public function __construct(){
		parent::__construct();
		
		$this->formContainer = new taoItems_actions_form_Export($this->formData);
	}
	
	/**
	 * action to perform to export items in XML
	 * @param array $formValues the posted data
	 */
	protected function exportXMLData($formValues){
		if($this->hasRequestParameter('filename')){
			$instances = $formValues['instances'];
			if(count($instances) > 0){
				
				$itemService = tao_models_classes_ServiceFactory::get('Items');
				
				$folder = $this->getExportPath();
				$fileName = $formValues['filename'].'_'.time().'.zip';
				$path = tao_helpers_File::concat(array($folder, $fileName));
				if(!tao_helpers_File::securityCheck($path, true)){
					throw new Exception('Unauthorized file name');
				}
				
				$zipArchive = new ZipArchive();
				if($zipArchive->open($path, ZipArchive::CREATE) !== true){
					throw new Exception('Unable to create archive at '.$path);
				}
				
				foreach($instances as $instance){
					$item = $itemService->getItem($instance);
					$className = $this->loadItemExporter($item);
					
					$exporter = new $className($item, $zipArchive);
					$exporter->export();
				}
				
				$zipArchive->close();
			}
		}
	}
	
	protected function exportIMSCPData($formValues){
		if($this->hasRequestParameter('filename')){
			$instances = $formValues['instances'];
			if(count($instances) > 0){
				
				$itemService = tao_models_classes_ServiceFactory::get('Items');
				
				$folder = $this->getExportPath();
				$fileName = $formValues['filename'].'_'.time().'.zip';
				$path = tao_helpers_File::concat(array($folder, $fileName));
				if(!tao_helpers_File::securityCheck($path, true)){
					throw new Exception('Unauthorized file name');
				}
				
				$zipArchive = new ZipArchive();
				if($zipArchive->open($path, ZipArchive::CREATE) !== true){
					throw new Exception('Unable to create archive at '.$path);
				}
				
				foreach($instances as $instance){
					$item = $itemService->getItem($instance);
					if($itemService->hasItemModel($item, array(TAO_ITEM_MODEL_QTI))){
						
						$exporter = new taoItems_models_classes_exporter_QTIPackedItemExporter($item, $zipArchive);
						$exporter->export();
					}
				}
				
				$zipArchive->close();
			}
		}
	}
	
	/**
	 * Load the ItemExporter class related to the model of a given Item. For instance, if you provide
	 * an $item with a item model named 'myItemModel', this method will try to load the class
	 * taoItems/models/ext/ItemExporter/class.myItemModel.php and add it to the global
	 * class loader.
	 *
	 * @param core_kernel_classes_Resource $item The item for which you want to load the ItemExporter.
	 * @return string the class name of the loaded ItemExporter.
	 */
	private function loadItemExporter(core_kernel_classes_Resource $item) {
		$returnValue = null;
	
		// Try to load a class into the Exporters directory. If it cannot be loaded, load the default one.
		// An item exporter class name is directly related to its item model label in the knwoledge base. If they are spaces (' '),
		// it should be replaced by *nothing* in the class name. e.g. 'My Item Model' becomes 'MyItemModel'. The class name and file
		// must be suffixed by ItemExporter. For a QTI Item Model we would have:
		// - file name: class.QTIItemExporter.php
		// - class name: QTIItemExporter
		$itemService = tao_models_classes_ServiceFactory::get('Items');
		$itemModelProperty = new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY);

		try {
			$itemModel = $item->getUniquePropertyValue($itemModelProperty);

			// As a reminder, str_replace is UTF-8 safe !
			$itemModelLabel = $itemModel->getLabel();
			$expectedClassName = 'taoItems_models_classes_exporter_'.str_replace(' ', '', $itemModelLabel) . 'ItemExporter';
			
			if (!class_exists($expectedClassName, true)) {
				throw new Exception ("No custom Item Exporter for '${itemModelLabel}' Item Model.");
			}
		}
		catch (Exception $e) {
			$expectedClassName = 'taoItems_models_classes_exporter_DefaultItemExporter';
		}
		
		$returnValue = $expectedClassName;
		
		return $returnValue;
	}
}
?>