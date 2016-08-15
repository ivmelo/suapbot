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
            'text' => 'O @suapbot foi desenvolvido por: *Ivanilson Melo*
http://github.com/ivmelo

Aluno do curso de Tecnologia em Análise e Desenvolvimento de Sistemas do IFRN.

Para ajuda, suporte, sugestões, ou para remover suas informações do bot, contate-me no telegram: @ivanilsonmelo.

Achou o bot útil? Compatilhe com seus os amigos.

Obrigado por usar o @suapbot.
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
