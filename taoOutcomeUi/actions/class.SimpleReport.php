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

/**
 * @author Patrick Plichart, <patrick.plichart@taotesting.com>
 * @package taoResults
 * @subpackage actions
 */
require_once('tao/lib/jpgraph/jpgraph.php');
require_once ('tao/lib/jpgraph/jpgraph_bar.php');
//require_once ('tao/lib/jpgraph/jpgraph_radar.php');


class taoResults_actions_SimpleReport extends tao_actions_TaoModule {
    protected $reportService = null;
    public function __construct() {

        parent::__construct();
        $this->service = taoResults_models_classes_StatisticsService::singleton();
        $this->defaultData();
	//TODO define a hook for implemeitng differently the report structure with an interface
	$this->reportService = taoResults_models_classes_ReportService::singleton();
    }
    /**
     * get the main class
     * @return core_kernel_classes_Classes
     */
	protected function getRootClass() {
        return new core_kernel_classes_Class(RESULT_ONTOLOGY . "#" . "TAO_DELIVERY_RESULTS");
    }
	public function build($view = "simple_form"){
	
	$selectedDeliveryClass = $this->getCurrentClass();
	//extract statistics using the statistics service 
	$deliveryDataSet = $this->service->extractDeliveryDataSet($selectedDeliveryClass);
	
	//add the required graphics computation and textual information for this particular report using reportService
	$reportData = $this->reportService->buildSimpleReport($deliveryDataSet, $selectedDeliveryClass->getlabel());
	foreach ($reportData as $dataIdentifier => $value){
		    $this->setData($dataIdentifier, $value);
	}
	//and select the corresponding view structure, could be (?) switched to something different		
	$this->setView('simple_form.tpl');
    }  
	
	
 }

?>