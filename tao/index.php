<?php

/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
include dirname(__FILE__). '/includes/class.Bootstrap.php';

$bootStrap = new BootStrap('tao');
$bootStrap->start();
$bootStrap->dispatch();
?>