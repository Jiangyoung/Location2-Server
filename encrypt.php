<?php


echo myLocationEncrypt('2','ANDROID-KEY-jiangyoungzhlocationapplication');

//加密
function myLocationEncrypt($str,$key)
{
    

    $base64Str = base64_encode($str);
    
    //32位原数据的md5 + base64编码的数据 + 32位base64编码数据的md5
    $md5base64Str = md5($base64Str);
    
    $s = md5($str).$base64Str.$md5base64Str;
    
    echo $s.'<hr/>';
    
    $j = 0;
    
    for($i=0;$i<strlen($s);$i++){

        $s[$i] = chr(ord($s[$i]) + 1);
        
        if(strlen($key) == $j)$j=0;
        
	}
    
    return $s;
    
}



/*

$s = 'abcdefghijklmnopqrstuvwxyz  !@#$^^%&(**)_++_(*()*(&*^&^$$%%^^&/music/G.E.M. 邓紫棋 - A.I.N.Y.(live版).mp3<';
echo $s.'<hr/>';
$s = base64_encode($s);
echo $s.'<hr/>';

$key = 'xmlakjglkegwenglwekjg';

//加密
$j = 0;
for($i=0;$i<strlen($s);$i++){

    $s[$i] = chr(ord($s[$i]) + ord($key[$j++]));
    
    if(strlen($key) == $j)$j=0;


}
echo $s.'<hr/>';

//解密
$j=0;
for($i=0;$i<strlen($s);$i++){

    $s[$i] = chr(ord($s[$i]) - ord($key[$j++]));
    
    if(strlen($key) == $j)$j=0;


}
echo $s.'<hr/>';
$s = base64_decode($s);
echo $s.'<hr/>';

*/


?>