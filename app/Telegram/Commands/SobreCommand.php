<?php

namespace App\Telegram\Commands;

use App\User;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use \Ivmelo\SUAPClient\SUAPClient;

class SobreCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'sobre';

    /**
     * @var string Command Description
     */
    protected $description = 'Infos sobre o bot e como ajudar no desenvolvimento.';

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {

        $this->replyWithChatAction(['action' => Actions::TYPING]);


        $this->replyWithMessage([
            'text' => 'Desenvolvido por: *Ivanilson Melo*
http://github.com/ivmelo
',
            'parse_mode' => 'markdown'
        ]);

        //$this->replyWithChatAction(['action' => Actions::TYPING]);


/*

        $this->replyWithMessage([
            'text' => 'Este bot tem a finalidade de facilitar o dia-a-dia dos estudantes, proporcionando uma maneira mais rápida e fácil de consultar as suas notas.

Segurança: Sua matrícula e chave de acesso, são armazenados com o mais absoluto sigilo. Toda a comunicação entre o Telegram e o Bot é feita através de conexões criptografadas (HTTPS), e os dados são armazenados em servidores seguros.
Para nerds:

A biblioteca que.',
    'parse_mode' => 'markdown'
*/
    }

}
