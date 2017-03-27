<?php 
set_time_limit(0);
$mes="";
if (!function_exists('curl_file_create')) {
    function curl_file_create($filename, $mimetype = '', $postname = '') {
        return "@$filename;filename="
            . ($postname ?: basename($filename))
            . ($mimetype ? ";type=$mimetype" : '');
    }
}
function generateRandomSelection($min, $max, $count)
{
    $result=array();
    if($min>$max) return $result;
    $count=min(max($count,0),$max-$min+1);
    while(count($result)<$count) {
        $value=rand($min,$max-count($result));
        foreach($result as $used) if($used<=$value) $value++; else break;
        $result[]=dechex($value);
        sort($result);
    }
    shuffle($result);
    return $result;
}

function isMember($groupId, $user_id)
{
	$user_info = file_get_contents("https://api.vk.com/method/groups.isMember?group_id={$groupId}&user_id={$user_id}&v=5.62");
	return $user_info;
}
function yandexTranslate($text, $key, $lang='en') {
    $uuid=generateRandomSelection(0,30,64);
    $uuid=implode($uuid);    $uuid=substr($uuid,1,32);
    $curl = curl_init();
    $url = 'https://translate.yandex.net/api/v1.5/tr.json/translate?'.http_build_query(array(
        'key'=>$key,
        'text' => $text,
		'lang' => $lang
    ));
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_VERBOSE, true);
    $response = curl_exec($curl);
    //$err = curl_errno($curl);
    curl_close($curl);
    //if ($err)
        //throw new exception("curl err $err");
    return $response;
}
/*$request_params = array( 
      'client_id' => "5846311",
      'client_secret' => "de92c499e74c1fbc68",
	  'redirect_url' => "http://befyg.ru/audd",
      'code' => "7b8b4b5385349f4efa", 
      'v' => '5.62'
    );
	$get_params = http_build_query($request_params);*/
	//$url = urlencode('https://oauth.vk.com/access_token?client_id=5846311&client_secret=de92c499e74c1fbc68&code=5846311&redirect_uri=http://befyg.ru/audd');
	/*$homepage = file_get_contents("https://oauth.vk.com/access_token?client_id=5846311&client_secret=LJyFxtyBkZqIp5PQfYKb&code=72c926e284a95795cc&redirect_uri=http://befyg.ru/audd/");
echo $homepage;*/
if (!isset($_REQUEST)) { 
  return; 
} 

//Строка для подтверждения адреса сервера из настроек Callback API 
$confirmation_token = 'f9f479e8'; 

//Ключ доступа сообщества 
$token = 'a0630928aa99e064631abfbb646c306f78f4037c2bd41e14'; 

//Получаем и декодируем уведомление 
$data = json_decode(file_get_contents('php://input')); 
$group_id = $data->group_id;
//Проверяем, что находится в поле "type" 
switch ($data->type) { 

 case 'group_leave': 
	ob_start();
	echo 'ok';
	$length = ob_get_length();
	// magic
	header('Connection: close');
	header("Content-Length: " . $length);
	header("Content-Encoding: none");
	header("Accept-Ranges: bytes");
	ob_end_flush();
	ob_flush();
	flush();
	$user_id = $data->object->user_id; 
	$reply="Упс, ты отписался
Чтобы заново воспользоваться ботом, подпишись на меня";
		$request_params = array(  
				'message' => $reply, 
				'user_id' => $user_id,
				'access_token' => $token, 
				'v' => '5.62' 
				); 
	$get_params = http_build_query($request_params); 
	file_get_contents('https://api.vk.com/method/messages.send?'. $get_params); 
	break;
  case 'group_join': 
	ob_start();
	echo 'ok';
	$length = ob_get_length();
	// magic
	header('Connection: close');
	header("Content-Length: " . $length);
	header("Content-Encoding: none");
	header("Accept-Ranges: bytes");
	ob_end_flush();
	ob_flush();
	flush();
	$user_id = $data->object->user_id;
	$reply="Осталось только написать что-нибудьй";
		$request_params = array(  
				'message' => $reply, 
				'user_id' => $user_id,
				'access_token' => $token, 
				'v' => '5.62' 
				); 
	$get_params = http_build_query($request_params); 
	file_get_contents('https://api.vk.com/method/messages.send?'. $get_params); 
	break;
	
  //Если это уведомление для подтверждения адреса сервера... 
  case 'confirmation': 
    //...отправляем строку для подтверждения адреса 
    echo $confirmation_token; 
    break; 

//Если это уведомление о новом сообщении... 
  case 'message_new': 
	ob_start();
	echo 'ok';
	$length = ob_get_length();
	header('Connection: close');
	header("Content-Length: " . $length);
	header("Content-Encoding: none");
	header("Accept-Ranges: bytes");
	ob_end_flush();
	ob_flush();
	flush();
	
	
	
    //...получаем id его автора 
    $user_id = $data->object->user_id; 
    //затем с помощью users.get получаем данные об авторе 
    $user_info = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$user_id}&v=5.0")); 
//и извлекаем из ответа его имя 
    $user_name = $user_info->response[0]->first_name; 
	$mes = $data->object->body;
	$dataobj = $data->object;
	while(isset($dataobj->fwd_messages))
		$dataobj = $dataobj->fwd_messages[0];
	$mes = $data->object->body;
	//if(!(strlen($audiolink) > 0))
		//$audiolink = $dataobj->attachments[0]->audio->url;
	if (strlen($mes)==0) { 
  $mes="Бот переводит только текст";
} 
$group_id = $data->group_id;
  if(json_decode(isMember($group_id, $user_id))->response == 0)
	{
		$reply="Привет, $user_name! Я переведу твои сообщения на выбранный тобой язык, но мною пользуются только подписчики"; //<<<---- текст, который отправляется
		$request_params = array(  
				'message' => $reply, 
				'user_id' => $user_id,
				'access_token' => $token, 
				'v' => '5.62' 
				); 

$get_params = http_build_query($request_params); 

file_get_contents('https://api.vk.com/method/messages.send?'. $get_params); 
break;
	}
if (!isset($dataobj->attachments[0]->doc->preview->audio_msg->link_mp3)) { 
  $audiolink="nu11";
}
$tmpfname="";
$saveFile="";
$result='{"status":{"msg":"No result","code":1001,"version":"1.0"}}';
$audata = json_decode($result);
//С помощью messages.send и токена сообщества отправляем ответное сообщение 
if($user_id != 149192198)
{
	$test_messss = "";
}
//$translatelang = $row['translatelang'];
$translatelang = 'en';
if($mes[0]=='/')
{
	$translatelang = 'q'.$mes;
	$translatelang = explode("/", $translatelang)[1];
	$translatelang = explode(" ", $translatelang)[0];
	$mes = 'q'.$mes;
	$mes = explode($translatelang, $mes)[1];
}
$translatelang_full=$lang_types[$translatelang];
$yandexTranslateKey = "КЛЮЧ API Яндекс.Переводчика";

$transresponse = yandexTranslate("$mes", $yandexTranslateKey, $translatelang);
$jtransresponse = json_decode($transresponse);
$upRes="";
$uploadServerUrl="";
if($jtransresponse->code==200)
{
	$trmes = $jtransresponse->text[0];
}
elseif($jtransresponse->code==413)
	$trmes = " Превышен максимально допустимый размер текста для перевода.";
elseif($jtransresponse->code==422)
	$trmes = " Текст не может быть переведен.";
else
	$trmes = " Во время перевода на $translatelang произошла ошибка. $transresponse ";
			    $request_params = array(  
				'message' => "{$trmes}", 
				'user_id' => $user_id,
				'attachment' => $attachaudio,
				'access_token' => $token, 
				'v' => '5.62' 
				); 

$get_params = http_build_query($request_params); 

file_get_contents('https://api.vk.com/method/messages.send?'. $get_params); 

break; 

} 
?> 
