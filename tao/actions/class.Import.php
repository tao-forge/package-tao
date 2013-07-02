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
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
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
	 * initialize the formContainer
	 */
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * initialize the classUri and execute the upload action
	 * @return void
	 */
	public function index(){
		
		$importer = $this->getCurrentImporter();
		$formContainer = new tao_actions_form_Import(
			$importer,
			$this->getAvailableImportHandlers(),
			$this->getCurrentClass()
		);
		$myForm = $formContainer->getForm();
		
		//if the form is submited and valid
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$report = $importer->import($this->getCurrentClass(), $myForm->getValues());
				if ($report->containsSuccess()) {
				    $this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($this->getCurrentClass()->getUri()));
				    $this->setData('message', $report->getTitle());
				    $this->setData('reload', true);
				}
				// not mutualy exclusiv
				if ($report->containsError()) {
    				$this->setData('importErrorTitle', $report->getTitle());
    				$this->setData('importErrors', $report->getErrors());
				}
			}
		}
		$this->setData('myForm', $myForm->render());
		$this->setData('formTitle', __('Import'));
		$this->setView('form/import.tpl', 'tao');
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @return tao_models_classes_import_ImportHandler
	 */
	private function getCurrentImporter() {
		if ($this->hasRequestParameter('importHandler')) {
			$importHandlerClass = $this->getRequestParameter('importHandler');
			foreach ($this->getAvailableImportHandlers() as $importHandler) {
				if (get_class($importHandler) == $importHandlerClass) {
					return $importHandler;
				}
			}
		}
		return current($this->getAvailableImportHandlers());
	}
	
	protected function getAvailableImportHandlers() {
		return array(
			new tao_models_classes_import_RdfImporter(),
			new tao_models_classes_import_CsvImporter()
		);
	}
	
	protected function getCurrentClass() {
		return new core_kernel_classes_Class(tao_helpers_Uri::decode($this->getRequestParameter('classUri')));
	}

}
?>
