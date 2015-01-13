<?php
if(@strtolower($_SERVER['REQUEST_METHOD']) === 'post'){

    
	$locInfo = array();

	$locInfo['time']  = $_POST['time'];
	$locInfo['errorcode'] = intval($_POST['errorcode']);
	$locInfo['latitude'] = doubleval($_POST['latitude']);
	$locInfo['lontitude'] = doubleval($_POST['lontitude']);
	$locInfo['radius'] = floatval($_POST['radius']);
	$locInfo['speed'] = floatval($_POST['speed']);
	$locInfo['satellite'] = intval($_POST['satellite']);
	$locInfo['addr'] = $_POST['addr'];
    
    $case = $_GET['case'];
    echo $case;
    
    //var_dump($locInfo);

}else{
	echo "error";
}


?>