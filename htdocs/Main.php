<?php
require_once "BotResponse.php";

class Main extends BotResponse {

    public function mainBot() {

        foreach ($this->botEventsRequestHandler() as $event) {

            $this->saveLogEvent($event);
            $this->saveTextMessage($event);

            if ($this->botEventTypeIsJoinGroup($event)){

                $this->botSendText($event, "Hello Group...");
            }

            if ($this->botEventTypeIsJoinRoom($event)){

                $this->botSendText($event, "Hello Room...");
            }

            if ($this->botEventSourceIsUser($event)) {

                if ($this->botIsReceiveText($event)) {

                    if ($this->isContainCommand($event, '@@tugas')){

                        $this->botSendText($event, $this->getCommandProperties($event, '@@tugas'));
                    }

                    $this->botSendText($event, 'lho...');

                }

                if ($this->botIsReceiveSticker($event)) {

                }

                if ($this->botIsReceiveImage($event)) {

                }

                if ($this->botIsReceiveLocation($event)){

                }

                if ($this->botIsReceiveAudio($event)){

                }

                if($this->botIsReceiveVideo($event)){

                }
            }

            if ($this->botEventSourceIsRoom($event)) {

                if ($this->botIsReceiveText($event)) {

                }

                if ($this->botIsReceiveSticker($event)) {

                }

                if ($this->botIsReceiveImage($event)) {

                }

                if ($this->botIsReceiveLocation($event)){

                }

                if ($this->botIsReceiveAudio($event)){

                }

                if($this->botIsReceiveVideo($event)){

                }
            }

            if ($this->botEventSourceIsGroup($event)) {

                if ($this->botIsReceiveText($event)) {

                }

                if ($this->botIsReceiveSticker($event)) {

                }

                if ($this->botIsReceiveImage($event)) {

                }

                if ($this->botIsReceiveLocation($event)){

                }

                if ($this->botIsReceiveAudio($event)){

                }

                if($this->botIsReceiveVideo($event)){

                }
            }
        }
    }

}