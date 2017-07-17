<?php

namespace App\Telegram\Commands;

use App\User;
use App\Telegram\Tools\Speaker;
use Telegram\Bot\Keyboard\Keyboard;


/**
 * Shows the settings panel, and handles settings updates.
 *
 * @author Ivanilson Melo <meloivanilson@gmail.com>
 */
class SettingsCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    const NAME = 'ajustes';

    /**
     * {@inheritDoc}
     */
    const ALIASES = [
        'configurações', 'configuracoes', 'notificações',
        'notificacoes', 'notificar', 'config', 'ajustar'
    ];

    /**
     * {@inheritDoc}
     */
    const PREFIX = 'settings';

    /**
     * {@inheritDoc}
     */
    const DESCRIPTION = 'Mostra painel de ajustes.';

    /**
     * {@inheritDoc}
     */
    protected function handleCommand($message)
    {
        $this->replyWithChatAction([
            'action' => 'typing',
        ]);

        $user = User::with('settings')->where(
            'telegram_id',
            $this->update['message']['from']['id']
        )->first();

        if ($user) {
            $this->replyWithMessage([
                'text' => Speaker::getSettingsMessage(),
                'parse_mode' => 'markdown',
                'reply_markup' => $this->getKeyboard($user->settings),
            ]);
        } else {
            $this->replyWithMessage(['text' => Speaker::userNotFound()]);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function handleCallback($callback_data)
    {
        $user = User::with('settings')->where(
            'telegram_id',
            $this->update['callback_query']['from']['id']
        )->first();

        if ($user) {
            // Find out which setting the user is toggling.
            switch ($callback_data) {
                case self::PREFIX . '.classes':
                    $user->settings->classes = ! $user->settings->classes;
                    break;
                case self::PREFIX . '.grades':
                    $user->settings->grades = ! $user->settings->grades;
                    break;
                case self::PREFIX . '.attendance':
                    $user->settings->attendance = ! $user->settings->attendance;
                    break;
            }

            // Save and reply.
            $user->settings->save();

            $this->replyWithEditedMessage([
                'text' => Speaker::getSettingsMessage(),
                'parse_mode' => 'markdown',
                'reply_markup' => $this->getKeyboard($user->settings),
            ]);
        } else {
            $this->replyWithMessage(['text' => Speaker::userNotFound()]);
        }
    }

    /**
     * Return an updated version of the keyboard
     * according to the settings object passed.
     *
     * @param  App\settings $settings The settings object.
     * @return \Telegram\Bot\Keyboard\Keyboard The keyboard object.
     */
    private function getKeyboard($settings) {
        return Keyboard::make()->inline()
            ->row(Keyboard::inlineButton([
                'text' => $settings->classes ? '✅ Aulas' : '🚫 Aulas',
                'callback_data' => self::PREFIX . '.classes',
            ]))->row(Keyboard::inlineButton([
                'text' => $settings->grades ? '✅ Notas' : '🚫 Notas',
                'callback_data' => self::PREFIX . '.grades',
            ]))->row(Keyboard::inlineButton([
                'text' => $settings->attendance ? '✅ Faltas' : '🚫 Faltas',
                'callback_data' => self::PREFIX . '.attendance',
            ]));
    }
}
