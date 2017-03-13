<?php
require_once "BotResponse.php";

class Main extends BotResponse {

    public function mainBot() {

        foreach ($this->botEventsRequestHandler() as $event) {

            $this->saveLogEvent($event);
            $this->saveTextMessage($event);

            if ($this->botEventTypeIsFollowed($event)){

                $this->botSendText($event, "Hello Guys");
            }

            if ($this->botEventTypeIsJoinGroup($event)){

                $this->botSendText($event, "Hello Group...");
            }

            if ($this->botEventTypeIsJoinRoom($event)){

                $this->botSendText($event, "Hello Room...");
            }

            if ($this->botEventSourceIsUser($event)) {

                if ($this->botIsReceiveText($event)) {

                    if ($this->isContainCommand($event, '.s')){

                        $command_property = $this->getCommandProperties($event, '.s');
                        $data_result = $this->getChatsData('.s', $command_property);

                        $this->botSendText($event, $data_result);
                    }
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