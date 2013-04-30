<?php
// 配置环境
ignore_user_abort();//关掉浏览器，PHP脚本也可以继续执行.
error_reporting(7);
set_time_limit(0);
header('Content-Type:text/html;charset=utf8');
date_default_timezone_set('RPC');

// 注册参数
$interval = 2;// 每隔s运行
$name = '***';//注意长度 加起来不得超过12个字符 a+time() = 11字符
$email = '***';

// 发码地址 search in google
$adressCodes = array(
    'http://c1521.biz.tm/read.php?tid=885556&fpage=0&toread=&page=22',// 你看不我。。
     'http://c1521.biz.tm/read.php?tid=885556&fpage=0&toread=&page=23',
    'aHR0cDovL3RlY2guZ3JvdXBzLnlhaG9vLmNvbS9ncm91cC8xMDI0Lw=='
);

//定时执行
do{
    start($adressCodes,$name, $email);
    echo 'running'."\n";
    sleep($interval);
}while(true);

//start($adressCodes,$name, $email);

function start($adressCodes,$name, $email){

    foreach($adressCodes as $key => $url){
        // 抓取code
        $result = getCodes($url);
        // 匹配code
        preg_match_all('#<div class="tpc_content" >.*</div>#', $result, $result);
        //echo $result[0][0];
        //print_r($result);
        //echo count($result[0]).':'.$result[1][2];
        for($i=0;$i<count($result[0]);$i++){
        //foreach ($result as $ContentVal) {
        	$ContentVal = $result[0][$i];
        	
    			preg_match_all('#[a-f0-9]{16}#', $ContentVal, $codes);
    			if($codes[0]){
    				echo $ContentVal."\n";
            foreach($codes[0] as $k => $code){
            	echo "code:".$code."\n";
                // 检测是否存在记录
                $codetxt = file_get_contents('code.txt'); // code log
                if(strpos($codetxt, $code) === false){
                    
                    // 校验邀请码
                    $result = checkRegister($code);
                    sleep(2); //論壇設置:刷新不要快於 2 秒
                    if(strpos($result, "parent.retmsg_invcode('1')") === false && strpos($result, "MySQL Server Error") === false){
                        register($name, $email, $code); // 注册
                    }else{// 邀请码无效，写记录
                        file_put_contents("code.txt", $code.PHP_EOL, FILE_APPEND|LOCK_EX);
                    }
                }
            }
        }
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

// 校验邀请码
function checkRegister($code){
    $result = array();
    $postFields = array(
        'action' => 'reginvcodeck',	
        'reginvcode' => $code
    );
    $options = array(
        CURLOPT_URL => 'http://c1521.biz.tm/register.php?',
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 5.1; rv:12.0) Gecko/20120101 Firefox/17.0',
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => http_build_query($postFields),
        CURLOPT_TIMEOUT => 10,
    );
    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

// 注册
function register($name, $email, $code){
    $temp = $name;
    $postFields = array(
        'forward' => '',
        'invcode' => $code,
        'regemail' => $email,
        'regname' => $temp,
        'regpwd' => '123456',
        'regpwdrepeat' => '123456',
        'step' => '2'
    );
    $options = array(
        CURLOPT_URL => 'http://c1521.biz.tm/register.php?',
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 5.1; rv:12.0) Gecko/20120101 Firefox/17.0',
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => http_build_query($postFields),
        CURLOPT_TIMEOUT => 10,
    );
    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    curl_close($ch);
    
    if($result){
        $result = iconv('gbk', 'utf-8', $result);
        file_put_contents("register.txt", $result.PHP_EOL, FILE_APPEND|LOCK_EX);
        if(strpos($result, "邀請碼錯誤") === false && strpos($result, "MySQL Server Error") === false){
    		file_put_contents("caoliu.txt", $temp.PHP_EOL, FILE_APPEND|LOCK_EX);
        }
    }
    if(!$result || strpos($result, "MySQL Server Error") !== false){
        sleep(2);//論壇設置:刷新不要快於 2 秒
        register($name, $email, $code);
    }
}
?>