<?php

//header("Content-Type:text/xml;charset=utf-8");
header("Content-Type:text/html;charset=utf-8");
//require 'StatusID.php';
//echo REQUEST_POWINFO_SUCC;

//echo json_encode(array("name1"=>"value1","arr"=>array("namex1"=>"valuex1","namex2"=>"valuex2")));
//$ar = array();
//echo empty($ar); // 1


//$saeStorage = new SaeStorage();

//$domain = 'locdomain';
//$filename = NULL."/music/G.E.M. 邓紫棋 - A.I.N.Y.(live版).mp3";

//$fileurl = $saeStorage->getCDNUrl($domain,$filename);

//echo $fileurl;
$s = 'Abc';
$key = '!23';
echo chr(ord($s[0]) + ord($key[0]));
/*
@$path = $_GET['path'];

$list =  getFileList($path);

echo myLocationEncrypt($list,'ANDROID-KEY-jiangyoungzhlocationapplication');
*/
/*
加密解密
$s = 'abcdefghijklmnopqrstuvwxyz  !@#$^^%&(**)_++_(*()*(&*^&^$$%%^^&/music/G.E.M. 邓紫棋 - A.I.N.Y.(live版).mp3<';
echo $s.'<hr/>';
$s = base64_encode($s);
echo $s.'<hr/>';

$key = 'xmlakjglkegwenglwekjg';
$j = 0;
for($i=0;$i<strlen($s);$i++){

    $s[$i] = chr(ord($s[$i]) + ord($key[$j++]));
    
    if(strlen($key) == $j)$j=0;


}
echo $s.'<hr/>';
$j=0;
for($i=0;$i<strlen($s);$i++){

    $s[$i] = chr(ord($s[$i]) - ord($key[$j++]));
    
    if(strlen($key) == $j)$j=0;


}
echo $s.'<hr/>';
$s = base64_decode($s);
echo $s.'<hr/>';
*/








/*


for($i=0;$i<strlen($s_e);$i++)
{
    echo ord($s[$i]).'||';
    
}
echo '<hr/>';

echo $s_e.'<hr/>';

$s_d = base64_decode($s_e);

echo $s_d.'<hr/>';



$key = 'xljlkekcojiefglw';




echo $s.'<hr/>';

for($i=0;$i<strlen($s);$i++)
{
    $s[$i] = chr($s[$i] ^ ($key >> 333));
    
}

echo $s.'<hr/>'.$key;
*/

//读取文件列表
function getFileList($path =NULL, $limit = 100,$offset = 0){
    
    
    $saeStorage = new SaeStorage();

    $domain = 'locdomain';
    
    //$limit = 100;
    //$offset = 0;

    $fold = true;//是否折叠目录
    
    $list = $saeStorage->getListByPath($domain,$path,$limit,$offset,$fold);

    
    $str = '<?xml version="1.0" encoding="utf-8" ?>';
    
    $str .= '<contentList>';
    
    $str .= '<dirNum>'.$list['dirNum'].'</dirNum>';
    
    $str .= '<fileNum>'.$list['fileNum'].'</fileNum>';
    if($list['dirNum']>0)
    {
        $str .= '<dirs>';
        
        foreach($list['dirs'] as $dir)
        {	
            $str .= '<dir>';
            $str .= '<dirName>'.$dir['name'].'</dirName>';
            $str .= '<dirFullName>'.$dir['fullName'].'</dirFullName>';
            $str .= '</dir>';
        }
    
        $str .= '</dirs>';
    }
    if($list['fileNum']>0)
    {
        $str .= '<files>';
        foreach($list['files'] as $file)
        {
            $str .= '<file>';
            $str .= '<fileName>'.$file['Name'].'</fileName>';
            $str .= '<fileFullName>'.$file['fullName'].'</fileFullName>';            
            $str .= '<fileCDNUrl>'.$saeStorage->getCDNUrl($domain,$file['Name']).'</fileCDNUrl>';
            $str .= '<fileLength>'.$file['length'].'</fileLength>';
            $str .= '<fileUploadTime>'.$file['uploadTime'].'</fileUploadTime>';
            $str .= '</file>';
        }
        $str .= '</files>';
    }
    
    $str .= '</contentList>';
    
    return $str;
    
}

//加密
function myLocationEncrypt($str,$key)
{
    

    $base64Str = base64_encode($str);
    
    //32位原数据的md5 + base64编码的数据 + 32位base64编码数据的md5
    $md5base64Str = md5($base64Str);
    
    $s = md5($str).$base64Str.$md5base64Str;
    
    $j = 0;
    
    for($i=0;$i<strlen($s);$i++){

        $s[$i] = chr(ord($s[$i]) + ord($key[$j++]));
        
        if(strlen($key) == $j)$j=0;
        
	}
    
    return $s;
    
}



?>
