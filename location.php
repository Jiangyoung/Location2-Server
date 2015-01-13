<?php

require_once 'StatusID.php';

if(@strtolower($_SERVER['REQUEST_METHOD']) === 'post'){
    //设置时区
    date_default_timezone_set('PRC');
    /*获取操作参数
     * 1 、 插入新数据
     * 2 、 取出最新数据
     */
    $case = @intval($_GET['case']);
    //初始化数据库连接
    require_once 'SqlTools.php';
    $sqlTools = new SqlTools();
    //根据操作参数做出对应操作
    switch($case){
        
        //把从android上传来的位置信息存入数据库
        case 1:
            $locInfo = array();
    
            $locInfo['time']  = htmlspecialchars($_POST['time']);
            $locInfo['errorcode'] = intval(htmlspecialchars($_POST['errorcode']));
            $locInfo['latitude'] = htmlspecialchars($_POST['latitude']);
            $locInfo['lontitude'] = htmlspecialchars($_POST['lontitude']);
            $locInfo['radius'] = htmlspecialchars($_POST['radius']);
            $locInfo['speed'] = htmlspecialchars($_POST['speed']);
            $locInfo['satellite'] = intval(htmlspecialchars($_POST['satellite']));
            $locInfo['addr'] = htmlspecialchars($_POST['addr']);
        	$verifyCode = htmlspecialchars($_POST['verifyCode']);
        
        	
			if(empty($locInfo['latitude']) || empty($locInfo['lontitude']) ||empty($locInfo['radius']))exit(json_encode(array("isvalid"=>POWER_INVALID)));
        
        	//验证数据真实性 认证数据来源
        	if(md5($locInfo['time'].$locInfo['latitude'].$locInfo['lontitude'].$locInfo['radius'].ANDROID_KEY) != $verifyCode)exit(json_encode(array("isvalid"=>POWER_INVALID)));
        	
            $sql = "INSERT INTO `location`(`time`,`errorcode`,`latitude`,`lontitude`,`radius`,`speed`,`satellite`,`addr`) 
                            VALUES('%s','%d','%s','%s','%s','%s','%d','%s')";
        
            $sql = sprintf($sql,
                           $locInfo['time'],
                           $locInfo['errorcode'],
                           $locInfo['latitude'],
                           $locInfo['lontitude'],
                           $locInfo['radius'],
                           $locInfo['speed'],
                           $locInfo['satellite'],
                           $locInfo['addr']
                          
                          );
        	$res = $sqlTools->execute_dml($sql);

        	$limitInfo = array();
        
            $sql = 'select `latitude`,`lontitude`,`radius` from `limitrange` order by id desc limit 0,1';
            $res = $sqlTools->execute_dql($sql);
            
            if($row=mysql_fetch_array($res)){

                $limitInfo['latitude'] = $row['latitude'];
                $limitInfo['lontitude'] = $row['lontitude'];
                $limitInfo['radius'] = $row['radius'];

            }
        	mysql_free_result($res);
        	if(!empty($limitInfo)){
            	$distance = calcDistance($locInfo['latitude'],$locInfo['lontitude'],$limitInfo['latitude'],$limitInfo['lontitude']);
                $sumRadius = $locInfo['radius'] + $limitInfo['radius'];
                if($distance <= $sumRadius){
                    
                    $tokenSend = md5($locInfo['time'].SERVER_KEY.$locInfo['lontitude'].$locInfo['latitude'].$locInfo['radius'].'0');
                    
                	echo json_encode(array("isvalid"=>POWER_ISVALID,"token"=>$tokenSend));
                }else{
                	echo json_encode(array("isvalid"=>POWER_INVALID));
                }
            
            }
        
        break;
        
        //取出最新的位置信息
        case 2:
            $locInfo = array();
            
            $sql = 'select `time`,`errorcode`,`latitude`,`lontitude`,`radius`,`speed`,`satellite`,`addr` from `location` order by id desc limit 0,1';
            $res = $sqlTools->execute_dql($sql);
        
            if($row=mysql_fetch_array($res)){
                    $locInfo['time']  = $row['time'];
                    $locInfo['errorcode'] = intval($row['errorcode']);
                    $locInfo['latitude'] = $row['latitude'];
                    $locInfo['lontitude'] = $row['lontitude'];
                    $locInfo['radius'] = $row['radius'];
                    $locInfo['speed'] = $row['speed'];
                    $locInfo['satellite'] = intval($row['satellite']);
                    $locInfo['addr'] = $row['addr'];
            }
        	mysql_free_result($res);
        	if(!empty($locInfo)){
        		echo json_encode($locInfo);
        	}
        break;
        
        //把圈定的范围信息存入数据库
        case 3:
        	$limitInfo = array();
        
        	$limitInfo['time']  = date("Y-m-d H:i:s");
            $limitInfo['latitude'] = htmlspecialchars($_POST['latitude']);
            $limitInfo['lontitude'] = htmlspecialchars($_POST['lontitude']);
            $limitInfo['radius'] = htmlspecialchars($_POST['radius']);
            $limitInfo['remark'] = htmlspecialchars($_POST['remark']);
        
        	$sql = "INSERT INTO `limitrange`(`time`,`latitude`,`lontitude`,`radius`,`remark`) 
            			VALUES('%s','%s','%s','%s','%s')";
        	$sql = sprintf($sql,
                          $limitInfo['time'],
                          $limitInfo['latitude'],
                          $limitInfo['lontitude'],
                          $limitInfo['radius'],
                          $limitInfo['remark']
                          );
        	$res = $sqlTools->execute_dml($sql);
        
			if($res == 1){
        		echo "范围圈定成功！";
        	}        
        break;
        
        //取出最新的范围信息
		case 4:
        	$limitInfo = array();
        
        	$sql = 'select `time`,`latitude`,`lontitude`,`radius`,`remark` from `limitrange` order by id desc limit 0,1';
        	$res = $sqlTools->execute_dql($sql);
        
            if($row=mysql_fetch_array($res)){
                $limitInfo['time']  = $row['time'];
                $limitInfo['latitude'] = $row['latitude'];
                $limitInfo['lontitude'] = $row['lontitude'];
                $limitInfo['radius'] = $row['radius'];
                $limitInfo['remark'] = $row['remark'];
            }
        	mysql_free_result($res);
        	if(!empty($limitInfo)){
        		echo json_encode($limitInfo);
        	}
        
        break;
        
        //获取文件列表
        case 5:
        	$tokenAccept = $_POST['token'];
        	$dirAccept = $_POST['dir'];
        
        	$sql = 'select `time`,`latitude`,`lontitude`,`radius`,`abandoned` from `location` order by id desc limit 0,1';
            $res = $sqlTools->execute_dql($sql);
        	$locInfo =array();
            if($row=mysql_fetch_array($res)){
                    $locInfo['time']  = $row['time'];
                    $locInfo['latitude'] = $row['latitude'];
                    $locInfo['lontitude'] = $row['lontitude'];
                    $locInfo['radius'] = $row['radius'];
                	$locInfo['abandoned'] = $row['abandoned'];
            }
        	mysql_free_result($res);
        	if(!empty($locInfo)){
                
                $tokenCalc = md5($locInfo['time'].SERVER_KEY.$locInfo['lontitude'].$locInfo['latitude'].$locInfo['radius'].$locInfo['abandoned']);
                
                if($tokenCalc === $tokenAccept){
                	$fileListStr = getFileList($dirAccept);                    
                }else{
                	$fileListStr = getFileList('-----');
                }
                $ciphertext = myLocationEncrypt($fileListStr,ANDROID_KEY);
                echo $ciphertext;            
            }
        	
        	
        
        break;
        //弃用查看文件token
        case 6:
        	$sql = "update location set abandoned=1 where id in (select location.id from (select * from location order by id desc limit 0,1) as location)";
        	
        	$res = $sqlTools->execute_dml($sql);
        	
        	echo $res;
        
        break;
        default:
        	echo 'error';
        
    	}
    
}else{
	echo 'error';
}

/**
 * 通过两点经纬度计算距离
 */
function calcDistance($x1,$y1,$x2,$y2){

	@$x1 = doubleval($x1);
    @$y1 = doubleval($y1);
    @$x2 = doubleval($x2);
    @$y2 = doubleval($y2);
    
    $pk = 180 / 3.1415926;
    $a1 = $x1 / $pk;
    $a2 = $y1 / $pk;
    $b1 = $x2 / $pk;
    $b2 = $y2 / $pk;
    $t1 = cos($a1) * cos($a2) * cos($b1) * cos($b2);
    $t2 = cos($a1) * sin($a2) * cos($b1) * sin($b2);
    $t3 = sin($a1) * sin($b1);
    $tt = acos($t1 + $t2 + $t3); 
    return 6366000 * $tt;
    
        //return sqrt(($x1-$x2)*($x1-$x2) + ($y1-$y2)*($y1-$y2));

}

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
    
    $md5base64Str = md5($base64Str);
    
    //32位原数据的md5 + base64编码的数据 + 32位base64编码数据的md5
    $s = md5($str).$base64Str.$md5base64Str;
    
    $j = 0;
    
    $sLen = strlen($s);
    $keyLen = strlen($key);
    
    for($i=0;$i<$sLen;$i++){

        $s[$i] = chr( ord($s[$i]) + ord($key[$j++])%4);
        
        if($keyLen == $j)$j=0;
        
	}
    
    
    return $s;
    
}

?>