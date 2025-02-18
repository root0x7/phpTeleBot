<?php 
namespace App;

use App\Keyboard;
use App\Files;

class Api
{
	public $token;
	public $keyboard;
	public $files;

	public function __construct($token)
	{
		$this->keyboard = new Keyboard();
		$this->files = new Files();
		$this->token = $token;
	}
	public function request($method,$datas=[])
	{
		$url = "https://api.telegram.org/bot".$this->token."/".$method;
	    $ch = curl_init();
	    curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
	    $res = curl_exec($ch);
	    if(curl_error($ch)){
	        var_dump(curl_error($ch));
	    }else{
	        return json_decode($res);
	    }
	}

	public  function sendMessage($chat_id,$text, $params = [])
	{
		$main = [
			'chat_id'=>$chat_id,
			'text'=>$text,
		];
		$this->request('sendMessage',array_merge($main,$params));
	}
	public function forwardMessage($chat_id,$from_chat_id,$message_id,$params = [])
	{
		$main = [
			'chat_id'=>$chat_id,
			'from_chat_id'=>$from_chat_id,
			'message_id'=>$message_id
		];
		$this->request('forwardMessage',array_merge($main,$params));
	}
	public function copyMessage($chat_id,$from_chat_id,$message_id,$params = [])
	{
		$main = [
			'chat_id'=>$chat_id,
			'from_chat_id'=>$from_chat_id,
			'message_id'=>$message_id
		];
		$this->request('copyMessage',array_merge($main,$params));
	}
	public function sendPhoto($chat_id,$photo,$caption,$params = [])
	{
		$main = [
			'chat_id'=>$chat_id,
			'photo'=>$photo,
			'caption'=>$caption
		];
		$this->request('sendPhoto',array_merge($main,$params));
	}
	public function sendAudio($chat_id,$audio,$caption,$params = [])
	{
		$main = [
			'chat_id'=>$chat_id,
			'audio'=>$audio,
			'caption'=>$caption
		];
		$this->request('sendAudio',array_merge($main,$params));
	}
	public function sendDocument($chat_id,$document,$caption,$params = [])
	{
		$main = [
			'chat_id'=>$chat_id,
			'document'=>$document,
			'caption'=>$caption
		];
		$this->request('sendDocument',array_merge($main,$params));
	}
	public function sendVideo($chat_id,$video,$params = [])
	{
		$main = [
			'chat_id'=>$chat_id,
			'video'=>$video,
		];
		$this->request('sendVideo',array_merge($main,$params));
	}		
	public function sendAnimation($chat_id,$animation,$params = [])
	{
		$main = [
			'chat_id'=>$chat_id,
			'animation'=>$animation,
		];
		$this->request('sendAnimation',array_merge($main,$params));
	}
	public function sendVoice($chat_id,$voice,$params = [])
	{
		$main = [
			'chat_id'=>$chat_id,
			'voice'=>$voice,
		];
		$this->request('sendVoice',array_merge($main,$params));
	}
	public function sendLocation($chat_id,$latitude,$longitude,$params = [])
	{
		$main = [
			'latitude'=>$latitude,
			'longitude'=>$longitude,
		];
		$this->request('sendLocation',array_merge($main,$params));
	}
	public function sendContact($chat_id,$phone_number,$first_name,$params = [])
	{
		$main = [
			'phone_number'=>$phone_number,
			'first_name'=>$first_name,
		];
		$this->request('sendContact',array_merge($main,$params));
	}
	public function sendChatAction($chat_id,$action)
	{
		$main = [
			'chat_id'=>$chat_id,
			'action'=>$action,
		];
		$this->request('sendChatAction',$main);
	}
}