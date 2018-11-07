<?php 
$appID = "5be0fbea";
$apiKey = "8a216e293c40b257ac956df8dcf4acee";

$audio_file = $_FILE["speechFile"]["tmp_name"];
// $audio_file = '.\16k.pcm';
// 语音听写
function voiceIat($audio_file,$appID,$apiKey)
{
	$param = [
		'engine_type' => 'sms16k',//16k采样率普通话音频
		'aue' => 'raw'//未压缩的pcm或wav格式
	];
	$cur_time = (string)time();
	$x_param = base64_encode(json_encode($param));

	$header_data = [
		'X-Appid:'.$appID,
		'X-CurTime:'.$cur_time,
		'X-Param:'.$x_param,
		'X-CheckSum:'.md5($apiKey.$cur_time.$x_param),
		'Content-Type:application/x-www-form-urlencoded; charset=utf-8'
	];

	// Body
	$audio = file_get_contents($audio_file);
	$body_data = 'audio='.urlencode(base64_encode($audio));

	// Request
	$url = "http://api.xfyun.cn/v1/service/v1/iat";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header_data);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $body_data);
	$res = curl_exec($ch);
	curl_close($ch);

	$response = json_decode($res,true);
	if (isset($response['coed']) && $response['code'] == 0){
    $res = array(
        'message' => $response['data'],
        'detail' => $response
    );
    exit(json_encode($res));
	}else{
	$res = array(
        'message' => '失败',
    );
    exit(json_encode($res));
	}

	// return $res;
}

voiceIat($audio_file,$appID,$apiKey);
echo $res;
?>