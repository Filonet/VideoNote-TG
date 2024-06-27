<?php

declare(strict_types=1);

namespace semennejo;

class Loader
{

    public function __construct($data, tg_api $tgApi)
    {
        if(isset($data->message->video)){
            $fileId = $data->message->video->file_id;
            $filePath = ($tgApi->getFile($fileId))['result']['file_path'];
            $tgApi->replyVideoNote(new \CURLFile($tgApi->getFileFromServer($filePath)));
            //file_put_contents("serse.txt", print_r($data->message->video, true));
        }else{
            if ($tgApi->getCommand() === "ping") {
                $start_time = microtime(true);
                $tgApi->replyMessage("На месте и работаю! ответ: " . (number_format((microtime(true) - $start_time) * 1000, 4, ".", "")) . " мс!");
                return;
            }
            $tgApi->replyMessage(implode("\n", ["Отправь видео или GIF и я сделаю видеокружок",
                "",
                "💡 Для лучшего результата рекомендуется сделать видео квадратным перед отправкой, но это не обязательно."]));
        }
    }

}
