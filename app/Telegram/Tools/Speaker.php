<?php

namespace App\Telegram\Tools;

use Telegram;

/**
 * This class provides strings for the most common messages used through the application.
 * It avoids duplicating them, and helps keep the code clean.
 */
class Speaker
{
    /**
     * Message for when suap is offline, user has provided bad credentials,
     * or if IF changed the design of SUAP.
     *
     * @return string The message.
     */
    public static function suapError()
    {
        return '❌ Houve um erro ao conectar-se ao SUAP. Por favor, verifique se o SUAP está online e tente novamente mais tarde.';
    }

    /**
     * Message for when the user nas no SUAP credentials stored.
     *
     * @return string The message.
     */
    public static function noCredentials()
    {
        return '⚠️ Você ainda não autorizou o acesso ao SUAP. Por favor, digite /autorizar <suap_id> <chave_de_acesso> e tente novamente. Para saber como obter a sua chave de acesso, use o comando /start.';
    }

    /**
     * Message for when the user has somehow deleted their account in the bot.
     *
     * @return string The message.
     */
    public static function userNotFound()
    {
        return '⚠️ Houve um erro ao recuperar suas credenciais de acesso. Por favor, digite /start e tente novamente.';
    }

    /**
     *  Infos about the bot and the development.
     *
     * @return string The message.
     */
    public static function about()
    {
        return "ℹ️ O @suapbot foi desenvolvido por: *Ivanilson Melo*\n".
        "http://github.com/ivmelo\n\n".
        "Aluno do curso de Tecnologia em Análise e Desenvolvimento de Sistemas do IFRN.\n\n".
        "Para ajuda, suporte, sugestões, ou para remover suas informações do bot, contate-me no telegram: @ivanilsonmelo.\n\n".
        "Achou o bot útil? Compatilhe com seus os amigos.\n".
        "Obrigado por usar o @suapbot.\n\n";
    }

    /**
     * Error while trying to authorize an account on suap.
     *
     * @return string The message.
     */
    public static function authorizationError()
    {
        return '❌ Ocorreu um erro ao autorizar o seu acesso. Por favor, verifique suas credenciais e tente novamente. Caso precise de ajuda, digite /start e siga o tutorial.';
    }

    /**
     * Missing matricula or access key from /autorizar command.
     *
     * @return string The message.
     */
    public static function authorizationCredentialsMissing()
    {
        return '⚠️ Por favor, envie suas credenciais no formato: /autorizar <matricula> <chave-de-acesso>. Caso precise de ajuda, digite /start e siga o tutorial.';
    }

    /**
     * When user authorizes SUAP access.
     *
     * @return string The message.
     */
    public static function authorized($name, $program, $situation)
    {
        return "✅ Pronto, sua conta foi autorizada com sucesso. 🙂\n\n".
                "*Nome:* $name \n".
                "*Curso:* $program \n".
                "*Situação:* $situation \n\n".
                'ℹ️ Digite /notas para ver suas notas, /aulas para ver as aulas de hoje ou /help para ver uma lista de comandos disponíveis.';
    }

    /**
     * Tutorial on how to get the access key.
     *
     * @return string The message.
     */
    public static function tutorial()
    {
        return "Primeiro, preciso de autorização para acessar o seu boletim no SUAP.\n\n".
               "Para isso, preciso de sua matrícula e chave de acesso *(não confundir com senha do SUAP)*. A chave de acesso é *somente leitura* e não permite alterar no seus dados no SUAP.\n\n".
               "Para pegar a sua chave de acesso siga os seguintes passos:\n\n".
               "1 - Faça login no SUAP. https://suap.ifrn.edu.br;\n".
               "2 - Clique em “Meus Dados”;\n".
               "3 - Acesse a aba “Dados Pessoais”;\n".
               "4 - Na ultima linha da tabela de “Dados Gerais” procure pela “Chave de Acesso” (Vai ser algo parecido com 5e8h9);\n".
               "5 - Copie ou anote a sua chave de acesso.\n\n".
               "Pronto! Agora basta digitar:\n\n".
               "/autorizar <sua-matricula> <chave-de-acesso>\n";
    }

    /**
     * Return a keyboard for the bot.
     *
     * @return string The message.
     */
    public static function getReplyKeyboardMarkup()
    {
        // keyboard system...
        $keyboard = [
            ['/aulas', '/notas'],
            ['/aulas amanhã', '/calendario'],
            // ['/help', '/sobre']
        ];

        $reply_markup = Telegram::replyKeyboardMarkup([
          'keyboard'          => $keyboard,
          'resize_keyboard'   => true,
          'one_time_keyboard' => false,
        ]);

        // $reply_markup = Telegram::replyKeyboardHide();

        return $reply_markup;
    }

    /**
     * Get the writen form of the day of the week.
     *
     * @return string The message.
     */
    public static function getDayOfTheWeek($day, $with_article = false)
    {
        $day_name = '';

        if ($with_article) {
            $day_name = 'a ';
            if ($day == 7 || $day == 1) {
                $day_name = 'o ';
            }
        }

        switch ($day) {
            case 1:
                $day_name .= 'domingo';
                break;
            case 2:
                $day_name .= 'segunda';
                break;
            case 3:
                $day_name .= 'terça';
                break;
            case 4:
                $day_name .= 'quarta';
                break;
            case 5:
                $day_name .= 'quinta';
                break;
            case 6:
                $day_name .= 'sexta';
                break;
            case 7:
                $day_name .= 'sábado';
                break;
            default:
                $day_name = 'o dia';
                break;
        }

        return $day_name;
    }
}
