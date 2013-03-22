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

define(fontName, ROOT_PATH."/tao/lib/pChart/Fonts/pf_arma_five.ttf");
require_once('tao/lib/pChart/pData.class');
require_once ('tao/lib/pChart/pChart.class');

/**
 * TAO - taoResults/models/classes/class.ReportService.php
 *
 * $Id$
 *
 *
 * Automatically generated on 20.08.2012, 15:22:19 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Patrick Plichart, <patrick.plichart@taotesting.com>
 * @package taoResults
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}


class taoResults_models_classes_ReportService
extends taoResults_models_classes_StatisticsService
{
	protected $deliveryDataSet = null;
	
	protected $contextClass;
	
	public function setDataSet($deliveryDataSet) {
	$this->deliveryDataSet = $deliveryDataSet;
	}
	
	public function setContextClass($contextClass) {
	$this->contextClass = $contextClass;
	}
	/***
	 * builSimpleReport compute report data and graphs using the statistics dataset
	 */
	public function buildSimpleReport(){	
		$deliveryDataSet = $this->deliveryDataSet;
		$reportData['variablesAvgComparison'] = $this->computeRadarChartAverages();
		$reportData['variablesFreqComparison'] = $this->computeRadarChartFrequencies();
		$reportData['reportTitle'] = __('Statistical Report for the current selection').' : '.$this->contextClass->getLabel().'';
		$reportData['average'] =  $this->deliveryDataSet["statistics"]["avg"];
		$reportData['std'] =  $this->deliveryDataSet["statistics"]["std"];
		$reportData['nbExecutions'] =  $this->deliveryDataSet["nbExecutions"];
		$reportData['#'] =  $this->deliveryDataSet["statistics"]["#"];
		$reportData['numberVariables'] =  $this->deliveryDataSet["statistics"]["numberVariables"];	
		$reportData['numberOfDistinctTestTaker'] =  count($this->deliveryDataSet["statistics"]["distinctTestTaker"]);
		
		foreach ($this->deliveryDataSet["statisticsPerVariable"] as $variableIdentifier => $struct){
		$scoreVariableLabel = $struct["naturalid"];
		//compute every single distribution for each variable
		//$urlDeliveryVariablebarChartQuantiles = $this->computeBarChart($this->deliveryDataSet["statisticsPerVariable"][$variableIdentifier]["splitData"], "Average and Total Scores by deciles of the population (".$scoreVariableLabel.")");
		$urlDeliveryVariablebarChartScores = $this->computeBarChartScores($variableIdentifier, "Sorted Collected Scores for the variable : ".$scoreVariableLabel."");
		$urlDeliveryVariablebarChartScoresFequencies = $this->computebarChartScoresFrequencies($variableIdentifier, "Grouped Scores Frequencies (".$scoreVariableLabel.")");
		
		//build UX data structure		
		$listOfVariables[]= array("label" => $scoreVariableLabel, "urlFrequencies"=>$urlDeliveryVariablebarChartScoresFequencies, "urlScores"=> $urlDeliveryVariablebarChartScores, "urlQuantileDistrib" => $urlDeliveryVariablebarChartQuantiles, "infos" => array("#" => $struct["#"], "sum" => $struct["sum"], "avg" => $struct["avg"]));
		
		//build parallel arrays to maintain values for the graph computation showing all variables
		$labels[] = $scoreVariableLabel;
		$sums[] = $struct["sum"];
		$avgs[] = $struct["avg"];
		}
		$reportData['listOfVariables'] =  $listOfVariables;	
		//$urlDeliveryVariableRadarPlot = $this->computeRadarPlot($sums,$avgs,$labels, "Scores by variables");
		//$this->setData('compareVariablesPlot', $urlDeliveryVariableRadarPlot);
		return $reportData;
	}
	/**
	 * @author Patrick plichart
	 * @param array $dataSet array of scores 
	 * @param string $title
	 * @return string the url of the generated graph
	 */
	private function computebarChartScores($variableIdentifier, $title){
	    
	    $datay = $this->deliveryDataSet["statisticsPerVariable"][$variableIdentifier]["data"];
	    $datax = array(); for ($i=0; $i < count($this->deliveryDataSet["statisticsPerVariable"][$variableIdentifier]["data"]); $i++) {$datax[] = "#";}
	    $legendTitle = __("Score per Observation");
	    return $this->getChart($variableIdentifier."scores", $datax, array($legendTitle => $datay), $title, "Observations", "Score");
	}
	/**
	 * @author Patrick plichart
	 * @param array $dataSet array of scores 
	 * @param string $title
	 * @return string the url of the generated graph
	 */
	private function computebarChartScoresFrequencies($variableIdentifier, $title){
	     
	    $datax = array();
	    $datay = array();
	    //thanks php
	    $frequencies = array_count_values($this->deliveryDataSet["statisticsPerVariable"][$variableIdentifier]["data"]);
	    foreach ($frequencies as $value => $frequency){
		$datax[] = $value;
		$datay[] = $frequency;
	    }
	    $legendTitle = __("Frequency per Score");
	    return $this->getChart($variableIdentifier."f", $datax, array($legendTitle => $datay), $title, "Score","Frequency (#)");
	}
	
	private function computeRadarChartAverages(){
	    foreach ($this->deliveryDataSet["statisticsPerVariable"] as $variableIDentifier => $statistics){
		$sery1[] = $statistics["avg"];
		$xLabels[] = wordwrap($statistics["naturalid"], 18, "\n");
	    }
	    return $this->getRadar("varCompAvgs", __("Score average per Variable"), $xLabels, $sery1, __("score average"));
	}
	private function computeRadarChartFrequencies(){
	    foreach ($this->deliveryDataSet["statisticsPerVariable"] as $variableIDentifier => $statistics){
		$sery1[] = $statistics["#"];
		$xLabels[] = wordwrap($statistics["naturalid"], 18, "\n");
	    }
	     return $this->getRadar("varCompFreq", __("Data Collection per Variable"), $xLabels, $sery1, __("# data collected"));
	}
	
	/**
	 * @author Patrick plichart
	 * @param string localgraphid an identifier for the graph to be displayed
	 * @param array $datax	a flat sery of x labels
	 * @param array $setOfySeries	an array of y series to be drawn (needs to be consistent with xsery), keys indicates the legend title
	 * @param string $title the title of the graph
	 * @param string xAxisLabel label of the x Axis
	 * @param string yAxisLabel label of the y Axis
	 * @return string the url of the generated graph
	 */
	
	private function getChart($localGraphId, $datax, $setOfySeries, $title, $xAxisLabel = "", $yAxisLabel=""){
	
	
	  // Dataset definition 
	$dataSet = new pData;
	foreach ($setOfySeries as $legend => $ysery ){
	    $dataSet->AddPoint($ysery,$legend);
	    $dataSet->SetSerieName($legend,$legend);
	}
	$dataSet->AddAllSeries();
	$dataSet->AddPoint($datax,"xLabels");
	
	$dataSet->SetYAxisName($yAxisLabel);
	$dataSet->SetXAxisName($xAxisLabel);
	
	$dataSet->SetAbsciseLabelSerie("xLabels");
	// Initialise the graph
	$graph = new pChart(655,260);
	$graph->setFontProperties(fontName,10);
	
	$graph->setGraphArea(65,40,580,200);
	//draw the background rectangle
	$graph->drawFilledRoundedRectangle(7,7,655,253,5,240,240,240);
	
	$graph->drawRoundedRectangle(5,5,655,225,5,230,230,230);
	$graph->drawGraphArea(255,255,255,TRUE);
	$graph->drawScale($dataSet->GetData(),$dataSet->GetDataDescription(), SCALE_START0,150,150,150,TRUE,0,2,TRUE);   
	$graph->drawGrid(4,TRUE,230,230,230,50);

	// Draw the 0 line
	$graph->setFontProperties(fontName,6);
	$graph->drawTreshold(0,143,55,72,TRUE,TRUE);

	// Draw the bar graph
	$graph->drawBarGraph($dataSet->GetData(),$dataSet->GetDataDescription(),TRUE);

	// Finish the graph
	$graph->setFontProperties(fontName,8);
	$graph->drawLegend(480,220,$dataSet->GetDataDescription(),255,255,255);
	$graph->setFontProperties(fontName,10);
	$graph->drawTitle(50,30,$title,50,80,50,585);
	      $url = $this->getUniqueMediaFileName($localGraphId, "png");
	      $graph->Render(ROOT_PATH.$url);
	      return ROOT_URL.$url;
	}
	
	private function getRadar($localGraphId, $title, $xLabels, $sery1, $legend){
	    // Dataset definition   
	    $dataSet = new pData;  
	    $dataSet->AddPoint($xLabels,"Label");  
	    $dataSet->AddPoint($sery1,"Serie1");  
	    $dataSet->AddSerie("Serie1");
	    $dataSet->SetAbsciseLabelSerie("Label");  
	    $dataSet->SetSerieName($legend,"Serie1");  
	    
	    // Initialise the graph  
	    $graph = new pChart(600,500);  
	    $graph->setFontProperties(fontName,9);  
	    $graph->drawFilledRoundedRectangle(7,7,593,493,5,240,240,240);  
	    $graph->drawRoundedRectangle(5,5,593,493,5,230,230,230);  
	    $graph->setGraphArea(120,70,520,420);  
	    $graph->drawFilledRoundedRectangle(30,30,570,570,5,255,255,255);  
	    $graph->drawRoundedRectangle(30,30,570,570,5,220,220,220);  
	    // Draw the radar graph  
	    $graph->drawRadarAxis($dataSet->GetData(),$dataSet->GetDataDescription(),TRUE,20,120,120,120,125,125,125);  
	    $graph->drawFilledRadar($dataSet->GetData(),$dataSet->GetDataDescription(),50,20);  

	    // Finish the graph  
	    $graph->drawLegend(32,32,$dataSet->GetDataDescription(),255,255,255);  
	    $graph->setFontProperties(fontName,10);  
	    $graph->drawTitle(0,22,$title,50,50,50,400);  
	     $url = $this->getUniqueMediaFileName($localGraphId, "png");
	      $graph->Render(ROOT_PATH.$url);
	      return ROOT_URL.$url;
	}
	/**
	*TODO move to an helper, get a unique file name
	 * @param string localToReportId a consumer scriptlocal id
	 * @param string the extension of the media
	 * @return a file path and a filename within the results extension
	*/
	private function getUniqueMediaFileName($localToReportId, $fileExtension="")
		{	
			
			$fileName = base64_encode("sid_".session_id()."c_".$this->contextClass->getUri().$localToReportId).'.'.$fileExtension;
			return "taoResults/views/genpics/".$fileName;
		}
	

} /* end of class taoResults_models_classes_ResultsService */

?>