<?php

namespace App\Telegram\Commands;

use App\User;
use App\Telegram\Tools\Speaker;
use Telegram\Bot\Keyboard\Keyboard;


/**
 * Settings Command.
 *
 * @author Ivanilson Melo <meloivanilson@gmail.com>
 */
class SettingsCommand extends Command
{
    /**
     * The name of the command.
     *
     * @var string
     */
    const NAME = 'ajustes';

    /**
     * The prefix for callback queries.
     *
     * @var string
     */
    const PREFIX = 'settings';

    /**
     * The description of the command.
     *
     * @var string
     */
    const DESCRIPTION = 'Mostra painel de ajustes.';

    const CLASSES_SETTINGS = 'settings.classes.toggle';
    const ATTENDANCE_SETTINGS = 'settings.attendance.toggle';
    const GRADES_SETTINGS = 'settings.grades.toggle';

    /**
     * Handles a command call.
     *
     * @param string $message
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
     * Handles a callback query.
     * This method MUST be implemented, even if it's not used.
     *
     * @param  string $callback_data
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
                case self::CLASSES_SETTINGS:
                    $user->settings->classes = ! $user->settings->classes;
                    break;
                case self::GRADES_SETTINGS:
                    $user->settings->grades = ! $user->settings->grades;
                    break;
                case self::ATTENDANCE_SETTINGS:
                    $user->settings->attendance = ! $user->settings->attendance;
                    break;
            }

            // Save and reply.
            $user->settings->save();

            $this->replyWithEditedMessage([
                'text' => Speaker::getSettingsMessage(),
                'reply_markup' => $this->getKeyboard($user->settings),
            ]);
        } else {
            $this->replyWithMessage(['text' => Speaker::userNotFound()]);
        }
    }

    private function getKeyboard($settings) {
        return Keyboard::make()->inline()
            ->row(Keyboard::inlineButton([
                'text' => $settings->classes ? '✅ Aulas' : '🚫 Aulas',
                'callback_data' => self::CLASSES_SETTINGS,
            ]))->row(Keyboard::inlineButton([
                'text' => $settings->grades ? '✅ Notas' : '🚫 Notas',
                'callback_data' => self::GRADES_SETTINGS,
            ]))->row(Keyboard::inlineButton([
                'text' => $settings->attendance ? '✅ Faltas' : '🚫 Faltas',
                'callback_data' => self::ATTENDANCE_SETTINGS,
            ]));
    }
}
