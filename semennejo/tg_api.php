<?php

namespace semennejo;

use CURLFile;

class tg_api
{

    private string $token;
    private $data;

    public function __construct(string $token)
    {
        $this->token = $token;
        $this->data = json_decode(file_get_contents('php://input'));
    }

    public function getFile(string $fileId):array{
        return $this->request("getFile", ["file_id" => $fileId]);
    }

    public function getFileFromServer(string $path):string{
        return "https://api.telegram.org/file/bot" . $this->token . "/".$path;
    }

    public function replyMessage(string $message, array $params = []): array
    {
        return $this->sendMessage($this->getChatId(), $message, $params);
    }

    public function replyVideo(string $dir, array $params = []): array
    {
        return $this->sendVideo($this->getChatId(), $dir, $params);
    }

    public function replyVideoNote($fileId, array $params = []): array
    {
        return $this->sendVideoNote($this->getChatId(), $fileId, $params);
    }

    public function sendMessage(int $chatId, string $message, array $params = []): array
    {
        return $this->request("sendMessage", ["chat_id" => $chatId, "text" => $message] + $params);
    }

    public function sendVideo(int $chatId, string $dir, array $params = []):array{
        return $this->request("sendVideo", ["chat_id" => $chatId, "video" => $dir] + $params);
    }

    public function sendVideoNote(int $chatId, $fileId, array $params = []):array
    {
        return $this->request("sendVideoNote", [
                "chat_id" => $chatId,
                "video_note" => $fileId,
            ] + $params);
    }

    public function getMessage(): string
    {
        return $this->data->message->text;
    }

    public function getCommand(): string
    {
        return mb_strtolower(str_replace(["/", "!", "."], "", (explode(" ", $this->getMessage())[0])));
    }

    public function getChatId():int{
        return (int)$this->data->message->chat->id;
    }

    public static function create(string $token): self
    {
        return new self($token);
    }

    public function request(string $method, array $params = []): array
    {
        //https://api.telegram.org/bot7457933076:AAGqAwiFqLPDhC4CIdo5tD8Hh4PEPDQ-UP4/sendVideo?chat_id=6233737307&video=СООБЩЕНИЕ
        $url = "https://api.telegram.org/bot" . $this->token . "/" . $method;

        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type:multipart/form-data"
            ]);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            $result = json_decode(curl_exec($ch), true);
            curl_close($ch);
        } else {
            $result = json_decode(file_get_contents($url, true, stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'content' => http_build_query($params)
                ]
            ])), true);
        }
        return $result;
    }

}