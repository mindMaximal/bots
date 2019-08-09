<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

/**
 * Start command
 *
 * Gets executed when a user first starts using the bot.
 */
class StartCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'start';

    /**
     * @var string
     */
    protected $description = 'Start command';

    /**
     * @var string
     */
    protected $usage = '/start';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();

        $chat_id = $message->getChat()->getId();
        $text    = 'Добро пожаловать в Пилот \xf0\x9f\x9b\xa9' . PHP_EOL . 'Используйте /help Чтобы увидеть все команды!';
        $keyboard = [
            ["\xf0\x9f\x94\xa5 Цены"],
            ["\xf0\x9f\x8e\x81 Акции"],
            ["\xf0\x9f\x93\x86 Забронировать"],
            ["\xf0\x9f\x93\x8c Как нас найти?"]
        ]; 
        
        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
            'reply_markup' => $keyboard,
        ];

        return Request::sendMessage($data);
    }
}
