<?php
require_once "BotResponse.php";
use Carbon\Carbon;

print_r(Carbon::now());
class Main extends BotResponse {

    public function mainBot() {

        foreach ($this->botEventsRequestHandler() as $event) {

            $this->saveLogEvent($event);
            $this->saveTextMessage($event);

            if ($this->botEventSourceIsUser($event)) {

                if ($this->botIsReceiveText($event)) {

                    if ($this->botReceiveText($event) == "halo") {

                        $this->botSendText($event, json_encode($this->generateMeme($event)));
                    }

//                    if (strpos($this->botReceiveText($event), 'tugas') !== false){
//
//                        $this->botSendText($event, $this->getChatsData('tugas'));
//                    }

                    if ($this->IsTextRegexMatchCompare($event, 'tugas')){

                        $this->botSendText($event, $this->getChatsData('tugas'));
                    }
                }

                if ($this->botIsReceiveSticker($event)) {

//                    $data = $this->botReceiveSticker($event);
                    $this->botSendSticker($event, 4, 630);
                }
            }

            if ($this->botEventSourceIsGroup($event)) {

                if ($this->botIsReceiveText($event)) {

                    $text      = str_replace(' ', '+', htmlentities($this->botReceiveText($event)));
                    $url       = "https://dummyimage.com/1024x1024/1abe9c/ffff.jpg&text=$text";
                    $prevUrl   = "https://dummyimage.com/240x240/1abe9c/ffff.jpg&text=$text";
                    $this->botSendImage($event, $url, $prevUrl);
                }

                if ($this->botIsReceiveSticker($event)) {

                    $this->botSendSticker($event, 1, mt_rand(100, 139));
                }

                if ($this->botIsReceiveImage($event)) {

                    $this->botSendText($event, "Gambar apaan tuh ?");
                }
            }
        }
    }

}