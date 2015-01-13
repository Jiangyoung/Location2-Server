<?php

$saeStorage = new SaeStorage();

$domain = 'locdomain';

$limit = 100;
$offset = 0;


$path = 'music';
$fold = true;//是否折叠目录

$list = $saeStorage->getListByPath($domain,$path,$limit,$offset,$fold);


header("Content-Type:text/html;charset=utf-8");
var_dump($list);

/*
//header("Content-Type:text/html;charset=utf-8");

//$json = json_encode($list);
//echo preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $json);
*/

//header("Content-Type:text/xml;charset=utf-8");

/*
echo '<meta http-equiv="Content-Type" content="text/xml;charset=utf-8"/>';
$str = '';

$str .= '<list>';

$str .= '<dirNum>'.$list['dirNum'].'</dirNum>';

$str .= '<fileNum>'.$list['fileNum'].'</fileNum>';
if($list['dirNum']>0)
{
    $str .= '<dirs>';
    
    foreach($list['dirs'] as $dir)
    {
        $str .= '<dirName>'.$dir['name'].'</dirName>';
    }

    $str .= '</dirs>';
}
if($list['fileNum']>0)
{
	$str .= '<files>';
    foreach($list['files'] as $file)
    {
        $str .= '<fileName>'.$file['Name'].'</fileName>';
    }
    $str .= '</files>';
}

$str .= '</list>';

echo str_replace('char','xxxx',$str);

*/


?>