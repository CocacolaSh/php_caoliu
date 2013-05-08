<?php
// 配置环境
//ignore_user_abort();//关掉浏览器，PHP脚本也可以继续执行.
error_reporting(7);
set_time_limit(0);
header('Content-Type:text/html;charset=utf8');
date_default_timezone_set('RPC');

// 注册参数
$interval = 0;// 每隔s运行

$normalTitle = "普通主題";
$titlePattern = "#<tr align=\"center\" class=\"tr3 t_one\" onMouseOver=\"this.className='tr3 t_two'\" onMouseOut=\"this.className='tr3 t_one'\">.*</tr>#";


// 发码地址 search in google
$ipAdressURL = array(
    'http://www.veryhuo.com/res/ip/page_1.php',
    'http://www.veryhuo.com/res/ip/page_2.php',
    'http://www.veryhuo.com/res/ip/page_3.php',
    'http://www.veryhuo.com/res/ip/page_4.php',
    'http://www.veryhuo.com/res/ip/page_5.php',
);
$portValueMap = array(
'z' => "3",
'm' => "4",
'a' >= "2",
'l' => "9",
'f' => "0",
'b' => "5",
'i' => "7",
'w' => "6",
'x' => "8",
'c' => "1",
);
//定时执行
do{
    start($ipAdressURL, $portValueMap);
    echo 'running'."\n";
    sleep($interval);
}while(true);

//start($adressCodes,$name, $email);

function start($ipAdressURL, $portValueMap){

		$url = 'c1521.biz.tm/index.php?u=274465';
		$cookieFile = dirname(__FILE__).'\cookie_daili.txt'; 
    foreach($ipAdressURL as $key => $url){
        // 抓取code
        $result = getCodes($url);
        //var_dump($result);
        
        //$result = iconv('gbk', 'utf-8', $result);
        //$result =  mb_convert_encoding($result, 'utf-8', ‘gbk’);
        //file_put_contents("1.txt", $result.PHP_EOL, LOCK_EX);
        $ipReg='/((([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\.){3}(([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))/';
				$portReg = '#((\+[a-z]){2,4})#';
				$portReg2 = '#\+([a-z])#';
//$preg = "#<td>".$preg."<SCRIPT type=text/javascript>document.write".'(":"[+a-z]{1-4})</SCRIPT></td>#';
        preg_match_all('#<td>(.*)</td>#', $result, $resultSet);
         //echo "Num:".count($resultSet[0]);
         for($i=0;$i<count($resultSet[0]);$i++){
        	$ipAddress = $resultSet[1][$i];
        	preg_match_all($ipReg, $ipAddress, $ipAddressSet);
        	preg_match_all($portReg, $ipAddress, $ipPortSet);
        	preg_match_all($portReg2, $ipPortSet[0][0], $ipPortSet2);
        	
        	$portValue = '';
     
        	for($j=0;$j<count($ipPortSet2[0]);$j++){
        		$portValue = $portValue.$portValueMap[$ipPortSet2[1][$j]];
        	}
        	//echo $ipAddressSet[0][0].":".$ipPortSet[0][0] .":".$portValue. "\n";
        	$proxyIP = $ipAddressSet[0][0].":".$portValue;
        	echo "IP:".$proxyIP."\n";
        	proxyVister($url, $proxyIP, $portValue, $cookieFile);
        }
        
    }
}


// 抓取code
function getCodes($url){
    // 抓取网页
    $result = array();
    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 5.1; rv:12.0) Gecko/20120101 Firefox/17.0',
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_POST => 0,
        CURLOPT_TIMEOUT => 10,
    );
    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
function proxyVister($url, $proxyIP, $portValue, $cookieFile){
    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 5.1; rv:12.0) Gecko/20120101 Firefox/17.0',
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_POST => 0,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_PROXY => $proxyIP,
        CURLOPT_PROXYPORT => $portValue,
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_HEADER => 1,
        CURLOPT_PROXYTYPE => CURLPROXY_HTTP,
        CURLOPT_PROXYAUTH => CURLAUTH_BASIC,
        
    );
    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    curl_close($ch);
    var_dump($result);
    
    $result = iconv('gbk', 'utf-8', $result);
    file_put_contents("daili.txt", $result.PHP_EOL, LOCK_EX);
    return $result;
}


?>