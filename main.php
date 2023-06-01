<?php

/**
 * send_batch_email 負責整理要edm的資料後透過 send() 寄出
 * 因為第三方發送email的服務一個request最多只接收10筆資料
 * 需要請你幫忙調整 send_batch_email() 的程式碼讓他可以批次發送
 * send() 如果收到超過10筆資料會印error模擬第三方服務會response錯誤
*/

const FAKE_DATA_AMOUNT = 15;
const LIMIT_LENGTH = 10;

function generateFakeData()
{
	$original_input = [
		'to' => [],
		'sub' => [],
		'from' => 'recommend@buy123.com.tw',
	    'from_name' => '生活市集嚴選'
	];
	
	for ($i=0; $i<FAKE_DATA_AMOUNT; $i++) {
		$original_input['to'][] = sprintf("user_%s@test.com", $i);
		$original_input['sub']['%name%'][] = sprintf("user_%s", $i);
		$original_input['sub']['%item_name%'][] = sprintf("user_%s_item_name", $i);
	}
	
	return $original_input;
}


function send_batch_email($send_data)
{
    $personalizations = [];
    $template = [
        'to' => [],
        'from' => [
            'email' => '',
            'name' => ''
        ],
        'substitutions' => []
    ];
    foreach ($send_data['to'] as $key => $value) {
        $userMail = $template;
        // 收件人
        $userMail['to'] = $value;
        // 寄信人
        $userMail['from'] = [
            'email' => $send_data['from'],
            'name' => $send_data['from_name']
        ];
        // 取代文字
        $userMail['substitutions'] = [
            '%name%' => isset($send_data['sub']['%name%'][$key]) ? $send_data['sub']['%name%'][$key] : '',
            '%item_name%' => isset($send_data['sub']['%item_name%'][$key]) ? $send_data['sub']['%item_name%'][$key] : '',
        ];
        $personalizations[] = $userMail;
    }

    // 最多存放10份
    $data = [];
    // 預計送出次數
    $send_count = floor(count($personalizations)/10) + 1;

    for($j = 0; $j < $send_count; $j++){
        for($i = $j*10+1; $i <= count($personalizations); $i++){
            array_push($data, $personalizations[$i-1]);
            if(count($data)%10===0){
                break;
            }
        }
        send($data);
        $data = [];
    }
    
}

function send(array $data)
{
	if (sizeof($data) > LIMIT_LENGTH) {
		echo "error\n";
		exit(255);
	}
	print_r($data);
}

$original_input = generateFakeData();

send_batch_email($original_input);