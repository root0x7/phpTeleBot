<?php 
include 'vendor/autoload.php';

use Root0x7\Api;
use Root0x7\Command;
use Env\Env;

$api = new Api('5551662847:AAFK5dQLivBJLTacEA-XLUBy5NjbKFZ3E-s',[]);
$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$mid = $message->message_id;
$cid = $message->chat->id;
$tx = $message->text;




print_r("salom");

print_r(Env::get('test'));