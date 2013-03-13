<?php
/*  
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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);\n *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
/**
 * This controller provide the actions to export and manage exported data
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 * @subpackage action
 *
 */
class tao_actions_Import extends tao_actions_CommonModule {

	
	/**
	 * to be overriden if needed
	 * @var tao_actions_form_Import
	 */
	protected $formContainer;
	
	/**
	 * to set static data that will be used during the import
	 * @var array
	 */
	protected $staticData = array();
	
	/**
	 * to exclude some properties from the import process
	 * @var array
	 */
	protected $excludedProperties = array();
	
	/**
	 * add constant adapter options according to the class of import
	 * @var array
	 */
	protected $additionalAdapterOptions = array();
	
	
	/**
	 * initialize the formContainer
	 */
	public function __construct(){
		parent::__construct();
		$this->formContainer = new tao_actions_form_Import();
	}
	
	/**
	 * initialize the classUri and execute the upload action
	 * @return void
	 */
	public function index(){
		if($this->hasRequestParameter('classUri')){
			$this->setSessionAttribute('classUri', $this->getRequestParameter('classUri'));
		}
		$this->upload();
	}
	
	/**
	 * Main method, select the format and display the right form
	 * @return void
	 */
	public function upload(){
		
		$this->removeSessionAttribute('import');
		
		$myForm = $this->formContainer->getForm();
		
		//if the form is submited and valid
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				//import method for the given format
				if(!is_null($myForm->getValue('format'))){
					
					common_Logger::i('Starting import with format: '.$myForm->getValue('format'), array('TAO'));
					$importMethod = 'import'.strtoupper($myForm->getValue('format')).'File';
					if(method_exists($this, $importMethod)){
						
						//apply the matching method
						$this->$importMethod($myForm->getValues());
						
					}
				}
			}
		}
		$this->setData('myForm', $myForm->render());
		$this->setData('formTitle', __('Import'));
		$this->setView('form/import.tpl', 'tao');
	}
	
	/**
	 * action to perform on a posted RDF file
	 * @param array $formValues the posted data
	 */
	protected function importRDFFile($formValues){
		if(isset($formValues['source'])){
			
			//get the item parent class
			$uploadedFile = $formValues['source']['uploaded_file'];
			
			//validate the file to import
			$parser = new tao_models_classes_Parser($uploadedFile, array('extension' => 'rdf'));
			
			$parser->validate();
			if(!$parser->isValid()){
				$this->setData('importErrorTitle', __('Validation of the imported file has failed'));
				$this->setData('importErrors', $parser->getErrors());
			}
			else{
			
				//initialize the adapter
				$adapter = new tao_helpers_data_GenerisAdapterRdf();
				if($adapter->import($uploadedFile, null)){
					
					$this->removeSessionAttribute('classUri');
					$this->setData('message', __('Data imported successfully'));
					$this->setData('reload', true);
					
					@unlink($uploadedFile);
								
					return true;
				}		
				else{
					$this->setData('message', __('Nothing imported'));
				}	
			}
			
		}
	}
	
	/**
	 * action to perform on a posted CSV file. This is the first form
	 * a user sees when importing a CSV file.
	 * @param array $formValues the posted data
	 */
	protected function importCSVFile($formValues){
		
		//import for CSV
		$importData = array();
		$importData['options'] = array(
			'field_delimiter' 			=> $formValues['field_delimiter'],
			'field_encloser' 			=> $formValues['field_encloser'],
			'line_break' 				=> "\n",
			'multi_values_delimiter' 	=> $formValues['multi_values_delimiter'],
			'first_row_column_names' 	=> isset($formValues['first_row_column_names'][0])
		);
		if(!empty($formValues['column_order'])){
			$importData['options']['column_order'] = $formValues['column_order'];
		}
		$fileData = $formValues['source'];
		$importData['file'] = $fileData['uploaded_file'];
		
		$this->setSessionAttribute('import', $importData);
		$this->redirect(_url('mapping'));
	}
	
	
	/**
	 * display the mapping form, after a CSV file import. This is the second (and last) form
	 * the users see to import a CSV file.
	 * @return void
	 */
	public function mapping(){
		if(!$this->hasSessionAttribute('import')){
			$this->redirect(_url('upload'));
		}
		
		if($this->hasSessionAttribute('classUri')){
			
			//get the import options in the session (from the upload form)
			$importData = $this->getSessionAttribute('import');
			
			//initialize the adapter
			$adapterOptions = array_merge($this->additionalAdapterOptions, $importData['options']);
			$adapter = new tao_helpers_data_GenerisAdapterCsv($adapterOptions);
			
			$service = tao_models_classes_Service::getServiceByName(str_replace('tao', '',context::getInstance()->getExtensionName()));
			
			//get the current class of properties
			$clazz = new core_kernel_classes_Class(tao_helpers_Uri::decode($this->getSessionAttribute('classUri')));
			$properties = array(tao_helpers_Uri::encode(RDFS_LABEL) => __('Label'));
			$rangedProperties = array();
			
			$topLevelClass = new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE);
			$classProperties = $service->getClazzProperties($clazz, $topLevelClass);

			foreach($classProperties as $property){
				if(!in_array($property->uriResource, $this->excludedProperties)){
					//@todo manage the properties with range
					$range = $property->getRange();
					$properties[tao_helpers_Uri::encode($property->uriResource)] = $property->getLabel();
					
					if($range->uriResource != RDFS_LITERAL){
						$rangedProperties[tao_helpers_Uri::encode($property->uriResource)] = $property->getLabel();
					}
				}
			}
			
			//load the csv data from the file (uploaded in the upload form) to get the columns
			$csv_data = $adapter->load($importData['file']);
			
			//build the mapping form 
			if ($csv_data->count()) {
				
				// 'class properties' contains an associative array(str:'propertyUri' => 'str:propertyLabel') describing properties belonging to the target class.
				// 'ranged properties' contains an associative array(str:'propertyUri' => 'str:propertyLabel')  describing properties belonging to the target class and that have a range.
				// 'csv_column' contains an array(int:columnIndex => 'str:columnLabel') that will be used to create the selection of possible CSV column to map in views.
				// 'csv_column' might have NULL values for 'str:columnLabel' meaning that there was no header row with column names in the CSV file. 
				
				// Format the column mapping option for the form.
				$csvColMapping = array();
				if (true == $importData['options']['first_row_column_names'] && null != $csv_data->getColumnMapping()){
					// set the column label for each entry.
					// $csvColMapping = array('label', 'comment', ...)
					$csvColMapping = $csv_data->getColumnMapping();
				}
				else{
					// set an empty value for each entry of the array
					// to describe that column names are unknown.
					// $csvColMapping = array(null, null, ...)
					for ($i = 0; $i < $csv_data->getColumnCount(); $i++) {
						$csvColMapping[$i] = null;
					}
				}
				
				$myFormContainer = new tao_actions_form_CSVMapping(array(), array(
					'class_properties'  		=> $properties,
					'ranged_properties'			=> $rangedProperties,
					'csv_column'				=> $csvColMapping,
					'first_row_column_names'	=> $importData['options']['first_row_column_names']
				));
				
				$myForm = $myFormContainer->getForm();
				
				if($myForm->isSubmited()){
					
					if($myForm->isValid()){
						
						// set the mapping to the adapter
						// Clean "csv_select" values from form view.
						// Transform any "csv_select" in "csv_null" in order to
						// have the same importation behaviour for both because
						// semantics are the same.
						$map = $myForm->getValues('property_mapping');
						$newMap = array();
						
						foreach($map as $k => $m) {
							if ($m !== 'csv_select') {
								$newMap[$k] = $map[$k];
							}
							else {
								$newMap[$k] = 'csv_null';
							}
						}
						
						$adapter->addOption('map', $newMap);
						$adapter->addOption('staticMap', array_merge($myForm->getValues('ranged_property'), $this->staticData));
						
						//import it!
						if($adapter->import($importData['file'], $clazz)){
							$this->setData('message', __('Data imported successfully'));
							$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
							$this->removeSessionAttribute('classUri');
							$this->setData('reload', true);
							
							@unlink($importData['file']);
						}
					}
				}
				
				$this->setData('myForm', $myForm->render());
				$this->setData('formTitle', __('Import into ').$clazz->getLabel());
				$this->setView('form.tpl', 'tao');
			}
			else {
				// Nothing was retrieved.
				$this->redirect('index');
			}
		}
	}
}
?>
