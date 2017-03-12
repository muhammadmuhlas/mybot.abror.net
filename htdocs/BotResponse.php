<?php

use \LINE\LINEBot\SignatureValidator as SignatureValidator;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class BotResponse{

	public $bot;
	public $request;

	function __construct() {

        $capsule = new Capsule;

        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => $_ENV['DATABASE'],
            'username'  => $_ENV['DATABASE_USERNAME'],
            'password'  => $_ENV['DATABASE_PASSWORD'],
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $capsule->setEventDispatcher(new Dispatcher(new Container));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

		$this->request = file_get_contents('php://input');

		/* Get Header Data */
		$signature = $_SERVER['HTTP_X_LINE_SIGNATURE'];

		/* Logging to Console*/
        $myfile = fopen("log.txt", "w") or die("Unable to open file!");
        $c_log = file_get_contents("log.txt");
        $txt = $c_log . PHP_EOL . 'Body: '.$this->request . PHP_EOL;
        fwrite($myfile, $txt);
        fclose($myfile);

		/* Validation */
		if (empty($signature)) {
			return "Siganature not Set";
		}

		if ($_ENV['PASS_SIGNATURE'] == false && !SignatureValidator::validateSignature($this->request, $_ENV['CHANNEL_SECRET'], $signature)) {
			return "Invalid Signature";
		}

		/* Initialize bot*/
		$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($_ENV['CHANNEL_ACCESS_TOKEN']);
		$this->bot  = new \LINE\LINEBot($httpClient, ['channelSecret' => $_ENV['CHANNEL_SECRET']]);

	}

	/* Bot Event Request Handler */

	public function botEventsRequestHandler() {

		$requestHandler = json_decode($this->request, true);
		return $requestHandler['events'];
	}

	/* Bot Usability | Every method can only be used trough foreach */

	/*==================================Mandatory==================================*/

	public function botDisplayName($userId = null) {

		$getProfile  = $this->bot->getProfile($userId);
		$profile     = json_decode($getProfile, true);
		$displayName = $profile['displayName'];
		return $displayName;
	}

	/*General*/

	public function botEventReplyToken($event) {

		return $event['replyToken'];
	}

	public function botEventType($event) {

		return $event['type'];
	}

	public function botEventTimestamp($event) {

		return $event['timestamp'];
	}

	/*Source*/

	public function botEventSourceType($event) {

		return $event['source']['type'];
	}

	public function botEventSourceUserId($event) {

		return $event['source']['userId'];
	}

	public function botEventSourceRoomId($event) {

		return $event['source']['roomId'];
	}

	public function botEventSourceGroupId($event) {

		return $event['source']['groupId'];
	}

	public function botEventSourceIsUser($event) {

		if ($event['source']['type'] == "user") {
			return true;
		}
	}

	public function botEventSourceIsRoom($event) {

		if ($event['source']['type'] == "room") {

			return true;
		}
	}

	public function botEventSourceIsGroup($event) {

		if ($event['source']['type'] == "group") {
			return true;
		}
	}

	/*Message*/

	public function botEventMessageId($event) {

		// text, image, video, audio, location, sticker
		return $event['message']['id'];
	}

	public function botEventMessageType($event) {

		// text, image, video, audio, location, sticker
		return $event['message']['type'];
	}

	public function botEventMessageText($event) {

		// text
		return $event['message']['text'];
	}

	public function botEventMessageTitle($event) {

		// location
		return $event['message']['title'];
	}

	public function botEventMessageAddress($event) {

		// location
		return $event['message']['address'];
	}

	public function botEventMessageLatitude($event) {

		// location
		return $event['message']['latitude'];
	}

	public function botEventMessageLongitude($event) {

		// location
		return $event['message']['longitude'];
	}

	public function botEventMessagePackadeId($event) {

		// sticker
		return $event['message']['packageId'];
	}

	public function botEventMessageStickerId($event) {

		// sticker
		return $event['message']['stickerId'];
	}

	/*Postback*/

	public function botEventPostbackData($event) {

		return $event['postback']['data'];
	}

	/*Beacon*/

	public function botEventBeaconkHwid($event) {

		return $event['beacon']['hwid'];
	}

	public function botEventBeaconType($event) {

		return $event['beacon']['type'];
	}

	/*================================================================*/

	/* Bot Action */

    public function botEventTypeIsJoinGroup($event){

        if ($this->botEventType($event) == 'join' && $this->botEventSourceIsGroup($event)){

            return true;
        }
	}

    public function botEventTypeIsJoinRoom($event){

        if ($this->botEventType($event) == 'join' && $this->botEventSourceIsRoom($event)){

            return true;
        }
    }

    public function botEventTypeIsFollowed($event){

        if ($this->botEventType($event) == 'follow' && $this->botEventSourceIsUser($event)){

            return true;
        }
    }

    public function botEventTypeIsUnfollowed($event){

        if ($this->botEventType($event) == 'unfollow' && $this->botEventSourceIsGroup($event)){

            return true;
        }
    }


	/*Leave*/
	public function botEventLeaveRoom($event) {

		return $this->bot->leaveRoom($this->botEventSourceRoomId($event));
	}

	public function botEventLeaveGroup($event) {

		return $this->bot->leaveRoom($this->botEventSourceGroupId($event));
	}

	/*Send Content*/
	public function botSendText($event, $text) {

		$input    = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text);
		$response = $this->bot->replyMessage($this->botEventReplyToken($event), $input);

		if ($response->isSucceeded()) {

			return true;
		}

	}

	public function botSendImage($event, $original, $preview) {

		$input    = new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($original, $preview);
		$response = $this->bot->replyMessage($this->botEventReplyToken($event), $input);

		if ($response->isSucceeded()) {

			return true;
		}
	}

	public function botSendVideo($event, $original, $preview) {

		$input    = new \LINE\LINEBot\MessageBuilder\VideoMessageBuilder($original, $preview);
		$response = $this->bot->replyMessage($this->botEventReplyToken($event), $input);

		if ($response->isSucceeded()) {

			return true;
		}
	}

	public function botSendAudio($event, $content, $duration) {

		$input    = new \LINE\LINEBot\MessageBuilder\AudioMessageBuilder($content, $duration);
		$response = $this->bot->replyMessage($this->botEventReplyToken($event), $input);

		if ($response->isSucceeded()) {

			return true;
		}
	}

	public function botSendLocation($event, $title, $address, $latitude, $longitude) {

		$input    = new \LINE\LINEBot\MessageBuilder\LocationMessageBuilder($title, $address, $latitude, $longitude);
		$response = $this->bot->replyMessage($this->botEventReplyToken($event), $input);

		if ($response->isSucceeded()) {

			return true;
		}
	}

	public function botSendSticker($event, $packageId, $stickerId) {

		$input    = new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder($packageId, $stickerId);
		$response = $this->bot->replyMessage($this->botEventReplyToken($event), $input);

		if ($response->isSucceeded()) {

			return true;
		}
	}

	public function botSendImagemap($event, $baseUrl, $altText, $baseSizeBuilder, array $imagemapActionBuilders) {

		$input    = new \LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder($baseUrl, $altText, $baseSizeBuilder, $imagemapActionBuilders);
		$response = $this->bot->replyMessage($this->botEventReplyToken($event), $input);

		if ($response->isSucceeded()) {

			return true;
		}
	}

	public function botSendTemplate($event, $altText, $templateBuilder) {

		$input    = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder($altText, $templateBuilder);
		$response = $this->bot->replyMessage($this->botEventReplyToken($event), $input);

		if ($response->isSucceeded()) {

			return true;
		}
	}

	/*Receive Content*/
	public function botReceiveText($event) {

		return $this->botEventMessageText($event);
	}

	public function botReceiveImage($event) {

		if ($this->botEventMessageType($event) == 'image') {

			$response = $this->bot->getMessageContent($this->botEventMessageId($event));

			if ($response->isSucceeded()) {

				$folder    = "image";
				$math      = mt_rand(1, 10000000000);
				$time      = time();
				$extension = ".jpg";
				$file      = $folder.'/'.$time.'-'.$math.$extension;
				$fp        = fopen($file, 'w');
				fwrite($fp, $response->getRawBody());
				fclose($fp);

				return "https://bot.abror.net/content/$file";
			}
		}
	}

	public function botReceiveAudio($event) {

		if ($this->botEventMessageType($event) == 'audio') {

			$response = $this->bot->getMessageContent($this->botEventMessageId($event));

			if ($response->isSucceeded()) {

				$folder    = "audio";
				$math      = mt_rand(1, 10000000000);
				$time      = time();
				$extension = ".jpg";
				$file      = $folder.'/'.$time.'-'.$math.$extension;
				$fp        = fopen($file, 'w');
				fwrite($fp, $response->getRawBody());
				fclose($fp);

				return "https://bot.abror.net/content/$file";
			}
		}
	}

	public function botReceiveVideo($event) {

		if ($this->botEventMessageType($event) == 'video') {

			$response = $this->bot->getMessageContent($this->botEventMessageId($event));

			if ($response->isSucceeded()) {

				$folder    = "video";
				$math      = mt_rand(1, 10000000000);
				$time      = time();
				$extension = ".mp4";
				$file      = $folder.'/'.$time.'-'.$math.$extension;
				$fp        = fopen($file, 'w');
				fwrite($fp, $response->getRawBody());
				fclose($fp);

				return "https://bot.abror.net/content/$file";
			}
		}
	}

	public function botReceiveSticker($event) {

		if ($this->botEventMessageType($event) == 'sticker') {

			$sticker   = array();
			$packageId = array(
				'packageId',
			);
			$stickerId = array(
				'stickerId',
			);

			array_push($packageId['packageId'], $this->botEventMessagePackadeId($event));
			array_push($stickerId['stickerId'], $this->botEventMessageStickerId($event));

			array_push($sticker, $packageId);
			array_push($sticker, $stickerId);

			return $sticker;
		}
	}

	public function botReceiveLocation($event) {

		if ($this->botEventMessageType($event) == 'location') {

			$location = array();
			$title    = array(
				'title',
			);
			$address = array(
				'address',
			);
			$latitude = array(
				'latitude',
			);
			$longitude = array(
				'longitude',
			);

			array_push($title['title'], $this->botEventMessageTitle($event));
			array_push($address['address'], $this->botEventMessageAddress($event));
			array_push($latitude['latitude'], $this->botEventMessageLatitude($event));
			array_push($longitude['longitude'], $this->botEventMessageLongitude($event));

			array_push($location, $title);
			array_push($location, $address);
			array_push($location, $latitude);
			array_push($location, $longitude);

			return $location;
		}
	}

	/*Is Receive Content*/
	public function botIsReceiveText($event) {

		if ($this->botEventMessageType($event) == 'text') {

			return true;
		}
	}

	public function botIsReceiveImage($event) {

		if ($this->botEventMessageType($event) == 'image') {

			return true;
		}
	}

	public function botIsReceiveAudio($event) {

		if ($this->botEventMessageType($event) == 'audio') {

			return true;
		}
	}

	public function botIsReceiveVideo($event) {

		if ($this->botEventMessageType($event) == 'video') {

			return true;
		}
	}

	public function botIsReceiveSticker($event) {

		if ($this->botEventMessageType($event) == 'sticker') {

			return true;
		}
	}

	public function botIsReceiveLocation($event) {

		if ($this->botEventMessageType($event) == 'location') {

			return true;
		}
	}

    public function saveLogEvent($event){

	    $logTable = Capsule::table('logs');
	    $logTable->insert([
            'json' => json_encode($event)
        ]);
	}

	public function saveTextMessage($event){

        if ($this->botEventSourceIsGroup($event)){

            $chats = Capsule::table('chats');
            $chats->insert([
                'source_id' => $this->botEventSourceGroupId($event),
                'source_type' => $this->botEventSourceType($event),
                'timestamp' => $this->botEventTimestamp($event),
                'text' => $this->botEventMessageText($event)
            ]);
        }

        if ($this->botEventSourceIsRoom($event)){

            $chats = Capsule::table('chats');
            $chats->insert([
                'source_id' => $this->botEventSourceRoomId($event),
                'source_type' => $this->botEventSourceType($event),
                'timestamp' => $this->botEventTimestamp($event),
                'text' => $this->botEventMessageText($event)
            ]);
        }

        if ($this->botEventSourceIsUser($event)){

            $chats = Capsule::table('chats');
            $chats->insert([
                'source_id' => $this->botEventSourceUserId($event),
                'source_type' => $this->botEventSourceType($event),
                'timestamp' => $this->botEventTimestamp($event),
                'text' => $this->botEventMessageText($event)
            ]);
        }
    }

    public function getChatsData($command, $query){

	    $chats = Capsule::table('chats')
            ->where('text', 'LIKE', '%' . $query . '%')
            ->where('text', 'NOT LIKE', '%' . $command . '%')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();
        $text = "";
        date_default_timezone_set('Asia/Jakarta');
        foreach ($chats as $chat){

            $text = $text . \Carbon\Carbon::parse($chat->timestamp/1000)->toDayDateTimeString();
            $text = $text . "\r\n";
            $text = $text . $chat->text;
            $text = $text . "\r\n";
            $text = $text . "\r\n";
        }
        return $text;
    }

    function isContainCommand($event, $command){

        if (substr($this->botReceiveText($event), 0, strlen($command)) === $command){

            return true;
        }

    }

    public function getCommandProperties($event, $command, $reply = "Perintah tidak lengkap"){

        if (strlen($this->botReceiveText($event)) != strlen($command)){

            return substr($this->botReceiveText($event), strlen($command)+1, strlen($this->botReceiveText($event)));
        }

        return $reply;
    }
}