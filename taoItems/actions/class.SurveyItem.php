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
 * actions for managing survey items
 *
 * @author Melis Matteo
 * @package taoItems
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
class taoItems_actions_SurveyItem extends taoItems_actions_Items
{
	/**
	 *  PHP XSL transformation used for item render and qat ui render
	 */
	public function transformXSL()
	{
		$xml = html_entity_decode($this->getRequestParameter('xml')); // string XML
		$xsl = html_entity_decode($this->getRequestParameter('xsl')); // string XSL
//		var_dump($xml);die;
//		var_dump($xsl);die;
		$params = $this->getRequestParameter('params');
		$params = $params ? $params : array();
		// first get lang
		$service = tao_models_classes_ServiceFactory::get('tao_models_classes_UserService');
		$user = $service->getCurrentUser();
		$lang = (string) $user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_UILG))->getOnePropertyValue(new core_kernel_classes_Property(RDF_VALUE));
		// create the compilator and get the transformation
		$compilator = new taoItems_models_classes_Survey_Compilator($xsl, $lang, $params);
//		var_dump($compilator->compile($xml));die;
		echo $compilator->compile($xml);
	}

	/**
	 *  render an item
	 * @return type
	 */
	public function preview() {
		$dir = BASE_DATA . 'surveyItems/';
		if(!is_dir($dir)) {
			mkdir($dir);
		}
		if(is_null($this->getRequestParameter('xml'))) {
			//display preview generated
			$content = file_get_contents($dir . $this->getRequestParameter('file'));
			$this->setData('ITEM_DISPLAY_MODE', "preview");
			$this->setData('content', $content);
			$this->setData('basePreview', BASE_WWW);
			$this->setData('taoView', TAOBASE_WWW);
			$this->setView('previewSurvey.tpl');
			return;
		}
		// generate preview in file
		$xml = html_entity_decode($this->getRequestParameter('xml')); // string XML
		echo json_encode(taoItems_models_classes_Survey_Item::generatePreviewFile($xml));
	}
	
	/**
	 * exports a questionnaire to a PDF file
	 * from a xml
	 */
	public function exportPDF() {
		$dir = BASE_DATA . 'surveyItems/';
		$xml = $this->getRequestParameter('flow');
		$name = $this->getRequestParameter('name');
		$file = $this->getRequestParameter('file');
		if (!is_null($file)) {
			if (file_exists($file)) {
				header("Content-Type: application/text");
				header("Content-Transfer-Encoding: binary");
				header("Content-Disposition: attachment; filename=\"" . $name . ".pdf\"");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Pragma: no-cache");
				readfile($file);
			}
			exit;
		}
		
		if(is_null($xml)) {
			echo 'ko';
			return;
		}
		
		
		$hash = taoItems_models_classes_Survey_Item::generatePreviewFile(html_entity_decode($xml));
		$htmlFile = $dir . $hash;
		$content = file_get_contents($htmlFile);
		
		$content = '
			<style>
				#container {
					page-break-after: always;
					position: static !important;
				}
			</style>
' . $content;
		
		// retrieves the evaluated content of the preview base template
		ob_start();
		$this->setData('content', $content);
		$this->setData('basePreview', BASE_WWW);
		$this->setData('taoView', TAOBASE_WWW);
		include ROOT_PATH . '/taoItems/views/templates/previewSurvey.tpl';
        $content = ob_get_clean();
		
		
		$content = str_replace('"screen"', '"screen, print"', $content);
		
		// trick because wkhtmltopdf requires .html file
		$htmlFileFinal = $htmlFile . '.html';
		//--
		
		file_put_contents($htmlFileFinal, $content);
		
		$html2pdf = new taoQAT_models_classes_Html2Pdf();
		$html2pdf->setup(array(
			'-O' => 'Landscape',
			'--print-media-type' => ''
		));
		$html2pdf->load(array($htmlFileFinal));
		echo $html2pdf->getTmpFile();
		
		
	}

}
?>
