<?php
// ���û���
//ignore_user_abort();//�ص��������PHP�ű�Ҳ���Լ���ִ��.
error_reporting(7);
set_time_limit(0);
header('Content-Type:text/html;charset=utf8');
date_default_timezone_set('RPC');

// ע�����
$interval = 1024;// ÿ��s����
//$name = '��ë�ʽ��';//ע�ⳤ�� ���������ó���12���ַ� a+time() = 11�ַ�
//$email = 'woshilaizicis@163.com';

$normalTitle = "��ͨ���}";
$titlePattern = "#<tr align=\"center\" class=\"tr3 t_one\" onMouseOver=\"this.className='tr3 t_two'\" onMouseOut=\"this.className='tr3 t_one'\">.*</tr>#";


// �����ַ search in google
$adressCodes = array(
    'http://c1521.biz.tm/thread0806.php?fid=7',// �㿴�����ҡ���
);

$cookie_file = dirname(__FILE__).'\cookie.txt'; 
$userName = '***';
$userPassword = '****';
sleep(2);
session_start();
login($userName, $userPassword, $cookie_file);

//��ʱִ��
do{
    start($adressCodes,$name, $cookie_file);
    echo 'running'."\n";
    sleep($interval);
}while(true);

//start($adressCodes,$name, $email);

function start($adressCodes,$name, $cookie_file){
	
	$froumPrex = 'http://c1521.biz.tm/';

    foreach($adressCodes as $key => $url){
        // ץȡcode
        $result = getCodes($url);
        
        //$result = iconv('gbk', 'utf-8', $result);
        //$result =  mb_convert_encoding($result, 'utf-8', ��gbk��);
        //file_put_contents("1.txt", $result.PHP_EOL, LOCK_EX);
        
        $startAddrPos = mb_strstr($result, "��ͨ���}");
        //file_put_contents("2.txt", $startAddrPos.PHP_EOL, LOCK_EX);
        // var_dump ($startAddrPos);
        //$index = mb_strpos($result, $normalTitle);
        //if ($index !== false){
        //	echo $index ."\n";
        //}
        
        $titleSets;
        $urlSets;
        if ($startAddrPos !== false){
        echo "here"."\n";
        	// ƥ��code
        //preg_match_all('#<tr align=\"center\" class=\"tr3 t_one\" onMouseOver=\"this.className=\'tr3 t_two\'\" onMouseOut=\"this.className=\'tr3 t_one\'\">.*</tr>#', $startAddrPos, $titleSets);
        //preg_match_all($titlePattern, $startAddrPos, $titleSets);
        preg_match_all('#<h3><a href=".*" target="_blank" id="">(.*)</a></h3>#', $startAddrPos, $titleSets);
        preg_match_all('#<h3><a href="(.*)" target="_blank" id="">#', $startAddrPos, $urlSets);
        $titleMaxIndex = count($urlSets[0]);
        
        $titleRandIndex = rand(0, $titleMaxIndex);
        $ContentVal = $urlSets[1][$titleRandIndex];
        preg_match_all('#htm_data/.*/.*/(.*).html#', $ContentVal, $tid);
        $tid = $tid[1][0];
        //echo 'content:'.$ContentVal.'tid:'.$tid."\n";
        
        $titleName = 'Re:'.$titleSets[1][$titleRandIndex];
        //preg_match_all('#<a href=".*"#', $ContentVal, $titleURL);
        //$titleURL = $titleURL[0][1];
        $titleURL = $froumPrex . $ContentVal;
        
        
        echo 'titleMaxIndex:'.$titleMaxIndex.' randIndex:'.$titleRandIndex.' title:'.$titleName.' URL:'.$titleURL."\n";
        post($titleURL, $titleName, $cookie_file, $tid);
      }
    }
}


// ץȡcode
function getCodes($url){
    // ץȡ��ҳ
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
function login($userName, $userPassword, $cookie_file){
    $postFields = array(
        'cktime' => '31536000',
        'forward'	=>'http://c1521.biz.tm/index.php',
        'hideid' => '0',
        'jumpurl' => 'http://c1521.biz.tm/index.php',
        'pwuser' => '***',
        'pwpwd' => '***',
        'step' => '2',
    );
    $options = array(
        CURLOPT_URL => 'http://c1521.biz.tm/login.php?',
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 5.1; rv:12.0) Gecko/20120101 Firefox/17.0',
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => http_build_query($postFields),
        CURLOPT_TIMEOUT => 10,
        CURLOPT_COOKIEJAR => $cookie_file,
    );
    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    curl_close($ch);
    //var_dump($result);
    
    if($result){
        $result = iconv('gbk', 'utf-8', $result);
        file_put_contents("error.txt", $result.PHP_EOL, LOCK_EX);
        if(strpos($result, "���ѽ�������") === true){
    		file_put_contents("error2.txt", $result.PHP_EOL, LOCK_EX);
        }
    }
    if(!$result || strpos($result, "MySQL Server Error") !== false){
        sleep(2);//Փ���O��:ˢ�²�Ҫ��� 2 ��
        //register($name, $email, $code);
    }
}
function post($titleURL, $titleName, $cookie_file, $tid){
		var_dump($cookie_file);
    $postFields = array(
         'action' =>	'reply',
        'atc_attachment' =>	'none',
        'atc_autourl' =>	'1',
        'atc_content' => '1024',
        'atc_content' =>'1024',
				'atc_convert' => '1',
				'atc_title' => $titleName,
				'atc_usesign' => '1',
				'fid' => '7',
				'step' => '2',
				'tid' => $tid,
				'verify' =>	'verify',
    );
    $options = array(
        CURLOPT_URL => 'http://c1521.biz.tm/post.php?',
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 5.1; rv:12.0) Gecko/20120101 Firefox/17.0',
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => http_build_query($postFields),
        CURLOPT_TIMEOUT => 10,
        CURLOPT_COOKIEFILE => $cookie_file,
    );
    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    curl_close($ch);
    
    if($result){
        $result = iconv('gbk', 'utf-8', $result);
        file_put_contents("error.txt", $result.PHP_EOL, FILE_APPEND|LOCK_EX);
        if(strpos($result, "��Ո�a�e�`") === false && strpos($result, "MySQL Server Error") === false){
    		file_put_contents("error.txt", $temp.PHP_EOL, FILE_APPEND|LOCK_EX);
        }
    }
    if(!$result || strpos($result, "MySQL Server Error") !== false){
        sleep(2);//Փ���O��:ˢ�²�Ҫ��� 2 ��
        //register($name, $email, $code);
    }
}

?>