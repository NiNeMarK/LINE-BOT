<?php
//test
$strAccessToken = "gRrNhYQ1R5oTopHwq5CFBFnpli2cz7VJqDtEGfEy+bQBwkQQekx62OnUGXISeASSWrh8VoD0VUqWreuQLA+LuA9smzcjH+l+CDv9rpPFzplRJ7733sWba/U0QWaaj4xY2IJANOlsuf4N+NHi0ocYkwdB04t89/1O/w1cDnyilFU=";

$content = file_get_contents('php://input');
$arrJson = json_decode($content, true);

$strUrl = "https://api.line.me/v2/bot/message/reply";

$arrHeader = array();
$arrHeader[] = "Content-Type: application/json";
$arrHeader[] = "Authorization: Bearer {$strAccessToken}";
$_msg = $arrJson['events'][0]['message']['text'];
$_userId = $arrJson['events'][0]['source']['userId'];


$api_key="Ev4hruRG0Ibo1k5KlKI11mRGY-nbEWRl";
$url = 'https://api.mlab.com/api/1/databases/ninemark_bot/collections/linebot?apiKey='.$api_key.'';
$json = file_get_contents('https://api.mlab.com/api/1/databases/ninemark_bot/collections/linebot?apiKey='.$api_key.'&q={"question":"'.$_msg.'"}');
$data = json_decode($json);
$isData=sizeof($data);

if (strpos($_msg, 'สอน') !== false) {
  if (strpos($_msg, 'สอน') !== false) {
    $x_tra = str_replace("สอน","", $_msg);
    $pieces = explode("|", $x_tra);
    $_question=str_replace("[","",$pieces[0]);
    $_answer=str_replace("]","",$pieces[1]);
    //Post New Data
    $newData = json_encode(
      array(
        'question' => $_question,
        'answer'=> $_answer,
        'userid'=> $_userId
      )
    );
    $opts = array(
      'http' => array(
          'method' => "POST",
          'header' => "Content-type: application/json",
          'content' => $newData
       )
    );
    $context = stream_context_create($opts);
    $returnValue = file_get_contents($url,false,$context);
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = 'ขอบคุณที่สอน';
  }
}else{
  if($isData >0){
   foreach($data as $rec){
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = $rec->answer;
   }
  }else{
    //Post New Data
    $etcData = json_encode(
      array(
        'etc' => $_msg,
        'userid'=> $_userId
      )
    );
    $etcopts = array(
      'http' => array(
          'method' => "POST",
          'header' => "Content-type: application/json",
          'content' => $etcData
       )
    );
    $etccontext = stream_context_create($etcopts);
    $etcreturnValue = file_get_contents($url,false,$etccontext);


    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = 'คุณสามารถสอนให้ฉลาดได้เพียงพิมพ์: สอน[คำถาม|คำตอบ]';
  }
}


$channel = curl_init();
curl_setopt($channel, CURLOPT_URL,$strUrl);
curl_setopt($channel, CURLOPT_HEADER, false);
curl_setopt($channel, CURLOPT_POST, true);
curl_setopt($channel, CURLOPT_HTTPHEADER, $arrHeader);
curl_setopt($channel, CURLOPT_POSTFIELDS, json_encode($arrPostData));
curl_setopt($channel, CURLOPT_RETURNTRANSFER,true);
curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($channel);
curl_close ($channel);
?>
