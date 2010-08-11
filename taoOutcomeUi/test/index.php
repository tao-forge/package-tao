<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';

//get the test into each extensions
$tests = TestRunner::getTests(array('taoResults'));

//create the test sutie
$testSuite = new TestSuite('TAO Result unit tests');
foreach($tests as $testCase){
	$testSuite->addTestFile($testCase);
}    

//add the reporter regarding the context
if(PHP_SAPI == 'cli'){
	$reporter = new XmlTimeReporter();
}
else{
	$reporter = new HtmlReporter();
}
//run the unit test suite
$testSuite->run($reporter);
?>