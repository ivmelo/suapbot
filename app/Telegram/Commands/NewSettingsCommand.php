<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Keyboard\Keyboard;

use App\User;

/**
 * New Settings Command.
 */
class NewSettingsCommand extends BotCommand
{
    const NAME = 'settings';
    const DESCRIPTION = 'This is a description for a command.';

    const CLASS_SETTINGS = 'settings.class.toggle';
    const ATTENDANCE_SETTINGS = 'settings.attendance.toggle';
    const GRADES_SETTINGS = 'settings.grades.toggle';

    protected function handleCommand()
    {
        $this->replyWithMessage([
            'text' => json_encode($this->message),
            'reply_markup' => $this->getKeyboard(),
        ]);
    }

    protected function handleCallback($callback_data)
    {
        $user = User::with('settings')->where(
            'telegram_id',
            $this->update['callback_query']['from']['id']
        )->first();

        if (! $user) {
            // This should never happen.
        } else {

            // Find out which setting the user is toggling.
            switch ($callback_data) {
                case self::CLASS_SETTINGS:
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
                'text' => $callback_data,
                'reply_markup' => $this->getKeyboard($user->settings),
            ]);
        }
    }


    private function getKeyboard($settings) {
        return Keyboard::make()->inline()
            ->row(Keyboard::inlineButton([
                'text' => $settings->classes ? '✅ Aulas' : '🚫 Aulas',
                'callback_data' => self::CLASS_SETTINGS,
            ]))->row(Keyboard::inlineButton([
                'text' => $settings->grades ? '✅ Notas' : '🚫 Notas',
                'callback_data' => self::GRADES_SETTINGS,
            ]))->row(Keyboard::inlineButton([
                'text' => $settings->classes ? '✅ Faltas' : '🚫 Faltas',
                'callback_data' => self::ATTENDANCE_SETTINGS,
            ]));
    }
}
