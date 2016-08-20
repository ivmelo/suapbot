<?php

namespace App\Telegram\Tools;

/**
 *  This class provides strings for the most common messages in the application.
 */
class Speaker
{
    //  Suap might be offline, the credentials can be wrong (although they were validated before) or they changed the design of their pages.
    public static function suapError() {
        return 'Houve um erro ao conectar-se ao SUAP. Por favor, verifique se o SUAP está online e tente novamente mais tarde.';
    }

    // User has no suap credentials stored.
    public static function noCredentials() {
        return 'Você ainda não autorizou o acesso ao SUAP. Por favor, digite /autorizar <suap_id> <chave_de_acesso> e tente novamente. Para saber como obter a sua chave de acesso, use o comando /help.';
    }

    // User has somehow deleted their account in the bot.
    public static function userNotFound() {
        return 'Houve um erro ao recuperar suas credenciais de acesso. Por favor, digite /start e tente novamente.';
    }

    // Infos about the bot and the development.
    public static function about() {
        return "O @suapbot foi desenvolvido por: *Ivanilson Melo*\n" .
        "http://github.com/ivmelo\n\n" .
        "Aluno do curso de Tecnologia em Análise e Desenvolvimento de Sistemas do IFRN.\n\n" .
        "Para ajuda, suporte, sugestões, ou para remover suas informações do bot, contate-me no telegram: @ivanilsonmelo.\n\n" .
        "Achou o bot útil? Compatilhe com seus os amigos.\n" .
        "Obrigado por usar o @suapbot.\n\n";
    }

    // Error while trying to authorize an account on suap.
    public static function authorizationError() {
        return 'Ocorreu um erro ao autorizar o seu acesso. Por favor, verifique suas credenciais e tente novamente. Caso precise de ajuda, digite /start e siga o tutorial.';
    }

    // Missing matricula or access key.
    public static function authorizationCredentialsMissing() {
        return 'Por favor, envie suas credenciais no formato: /autorizar <matricula> <chave-de-acesso>. Caso precise de ajuda, digite /start e siga o tutorial.';
    }
}
