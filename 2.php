<?php
// ���û���
ignore_user_abort();//�ص��������PHP�ű�Ҳ���Լ���ִ��.
error_reporting(7);
set_time_limit(0);
header('Content-Type:text/html;charset=utf8');
date_default_timezone_set('RPC');

// ע�����
$interval = 2;// ÿ��s����
$name = '***';//ע�ⳤ�� ���������ó���12���ַ� a+time() = 11�ַ�
$email = '***';

// �����ַ search in google
$adressCodes = array(
    'http://c1521.biz.tm/read.php?tid=885556&fpage=0&toread=&page=22',// �㿴���ҡ���
     'http://c1521.biz.tm/read.php?tid=885556&fpage=0&toread=&page=23',
    'aHR0cDovL3RlY2guZ3JvdXBzLnlhaG9vLmNvbS9ncm91cC8xMDI0Lw=='
);

//��ʱִ��
do{
    start($adressCodes,$name, $email);
    echo 'running'."\n";
    sleep($interval);
}while(true);

//start($adressCodes,$name, $email);

function start($adressCodes,$name, $email){

    foreach($adressCodes as $key => $url){
        // ץȡcode
        $result = getCodes($url);
        // ƥ��code
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
                // ����Ƿ���ڼ�¼
                $codetxt = file_get_contents('code.txt'); // code log
                if(strpos($codetxt, $code) === false){
                    
                    // У��������
                    $result = checkRegister($code);
                    sleep(2); //Փ���O��:ˢ�²�Ҫ��� 2 ��
                    if(strpos($result, "parent.retmsg_invcode('1')") === false && strpos($result, "MySQL Server Error") === false){
                        register($name, $email, $code); // ע��
                    }else{// ��������Ч��д��¼
                        file_put_contents("code.txt", $code.PHP_EOL, FILE_APPEND|LOCK_EX);
                    }
                }
            }
        }
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

// У��������
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

// ע��
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
        if(strpos($result, "��Ո�a�e�`") === false && strpos($result, "MySQL Server Error") === false){
    		file_put_contents("caoliu.txt", $temp.PHP_EOL, FILE_APPEND|LOCK_EX);
        }
    }
    if(!$result || strpos($result, "MySQL Server Error") !== false){
        sleep(2);//Փ���O��:ˢ�²�Ҫ��� 2 ��
        register($name, $email, $code);
    }
}
?>