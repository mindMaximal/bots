<?php

  /*
    https://www.browserling.com/tools/utf8-encode
 */
  include('vendor/autoload.php'); 
  use Telegram\Bot\Api; 
 
  $url=parse_url(getenv("CLEARDB_DATABASE_URL"));
  $server = $url["host"];
  $username = $url["user"];
  $password = $url["pass"];
  $db = substr($url["path"],1);
  // $server = 'localhost';
  // $username = 'root';
  // $password = '';
  // $db = 'test';

  $prices_array = array();

  class Prices {
    public $id ;
    public $name;
    public $price;

    public function __construct($row) {
      $this->id = $row['id'];
      $this->name = $row['name'];
      $this->price = $row['price'];
    }

    public function getPrice() {
      $str = "\xE2\xAD\x90 Вид: $this->name \r\n\xf0\x9f\x94\xa5 Цена: $this->price";
      return $str;
    }

  }

  $mysqli = new mysqli($server, $username, $password, $db);

  if ($mysqli->connect_errno) {
    echo "Не удалось подключиться к MySQL: <br>" . $mysqli->connect_error;
  } else echo "Подключение прошло успешно <br>";

  $result = $mysqli->query("SELECT * FROM prices");
  while ($row = $result->fetch_assoc()) {
    $prices_array[] = new Prices($row);
  }

  $token = '921483635:AAFQmYzCXCLcXQOH70WY5d0VKBVE6GtTZJI';
  $telegram = new Api($token); 

  if(!file_exists("registered.trigger")){ 
    $url = "https://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"].'/'.$token.'/token';
    $result = $telegram->setWebhook(['url' => $url]);
    if($result){
      file_put_contents("registered.trigger",time()); 
      echo "Токен зарегестрирован<br>";
    } else echo "Токен не зарегестрирован<br>";
  } else echo "Токен уже зарегестрирован<br>";

  $result = $telegram -> getWebhookUpdates(); 

  //$response = $telegram->getUpdates();

  $chid = $result['callback_query']['from']['id']; 
  //['callback_query']['data'];
  $telegram->sendMessage([ 
    'chat_id' => $chid, 
    'text' => $result
  } 
  
  $text = $result["message"]["text"];
  $chat_id = $result["message"]["chat"]["id"]; 
  $name = $result["message"]["from"]["username"];
  $keyboard = [["\xf0\x9f\x94\xa5 Цены"],["\xf0\x9f\x8e\x81 Акции"],["\xf0\x9f\x93\x86 Забронировать"],["\xf0\x9f\x93\x8c Как нас найти?"]]; 

  $inline_button1 = array("text"=>"Наш сайт","url"=>"http://google.com");
  $inline_button2 = array("text"=>"Адрес","callback_data"=>'address.show');
  $inline_keyboard = [[$inline_button1,$inline_button2]];
  $keyboard=array("inline_keyboard"=>$inline_keyboard);
  $reply_markup = json_encode($keyboard); 

  if($text){
    $text = mb_strtolower ($text);
    if ($text == "/start") {
      $reply = "Добро пожаловать в бота!";
      //$reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
      $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
    }elseif ($text == "/help") {
        $reply = "Информация с помощью.";
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
    }elseif ($text == "/price" or $text == "цены") {
      foreach ($prices_array as $value) {
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $value->getPrice() ]);
      }
    }
  }else{
    $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => "Отправьте текстовое сообщение." ]);
  }
?>