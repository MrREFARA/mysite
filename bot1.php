<?php

include "vk_api.php";



const VK_KEY = "b48c23e7e4aa9c7c02f61a51aff85b90e59a4c52567f8604e25074a9a82b2ccf8258f9b938095be8c5e9d"; //тот самый длинный ключ доступа сообщества
const ACCESS_KEY = "be20ea35"; //например c40b9566, введите свой
const VERSION = "5.81"; //ваша версия используемого api



$vk = new vk_api(VK_KEY, VERSION); // создание экземпляра класса работы с api, принимает ключ и версию api
$data = json_decode(file_get_contents('php://input')); //Получает и декодирует JSON пришедший из ВК
//print_r($data);
if ($data->type == 'confirmation') { //Если vk запрашивает ключ
    exit(ACCESS_KEY); //Завершаем скрипт отправкой ключа
}
$vk->sendOK(); //Говорим vk, что мы приняли callback
// Создаем необходиммые переменные
$peer_id = $data->object->peer_id; // Узнаем ИД беседы 2000000.....
$id = $data->object->from_id; // Узнаем ид пользователя который отправляет команду
$message = $data->object->text; // Текст самого сообщения
$is_admin = [87444494, 183657]; // создаем массив с ID's наших будущих админов через запятую
$chat_id = $peer_id - 2000000000;

if ($data->type == 'message_new') { // Если это новое сообщение то выполняем код указанный в условии


    if (mb_substr($message,0,5) == '/kick'){ // Образаем сообщение и сравниваем что получилось

            if (in_array($id, $is_admin)) { // С помощью in_array проверяем схожесть переменной $id с массивом с ID's

        $kick_id = mb_substr($message ,6); // еще раз обрезаем и получаем все что написано после /kick_
        $kick_id = explode("|", mb_substr($kick_id, 3))[0];

        if($kick_id == ""){
            $vk->sendMessage($peer_id, "Вы забыли указать аргумент");

        } else {

        $vk->request('messages.removeChatUser', ['chat_id' => $chat_id, 'member_id' => $kick_id]);
        $vk->sendMessage($peer_id, "id - {$kick_id} был исключен :-)");

    }
    } else {
            $vk->sendMessage($peer_id, "У Вас нет доступа к этой команде!");

        }
    }
}