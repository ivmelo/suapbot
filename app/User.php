<?php

namespace App;

use Ivmelo\SUAP\SUAP;
use Telegram;
use App\Telegram\Tools\Speaker;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'username', 'email', 'password', 'telegram_id', 'suap_id', 'suap_key', 'course_data', 'notify',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Refresh or create a new SUAP access token.
     *
     * @return String $token
     */
    public function refreshToken()
    {
        $suap = new SUAP();
        $data = $suap->autenticar($this->suap_id, $this->suap_key, true);
        $this->suap_token = $data['token'];
        $this->save();
        return $data['token'];
    }

    /**
     * Authorize access and store SUAP credentials.
     *
     * @param Integer $suap_id
     * @param String $suap_key
     */
    public function authorize($suap_id, $suap_key)
    {
        // Validate SUAP credentials.
        $client = new SUAP();
        $data = $client->autenticar($suap_id, $suap_key, true);
        $this->suap_token = $data['token'];

        $suap_data = $client->getMeusDados();

        // Save user credentials and Email.
        if ($suap_data) {
            $this->suap_id = $suap_id;
            $this->suap_key = $suap_key;
            $this->email = $suap_data['email'];

            // Get course data for the first access.
            $course_data = $client->getMeuBoletim(2017, 1);
            $course_data_json = json_encode($course_data);
            $this->course_data = $course_data_json;

            // Turn the notifications on.
            $this->notify = true;

            $this->save();
        }

        // Grab user info for display.
        $name = $suap_data['nome_usual'];
        $program = $suap_data['vinculo']['curso'];
        $situation = $suap_data['vinculo']['situacao'];

        // All set, message user.
        // And set up keyboard.
        Telegram::sendMessage([
            'chat_id'      => $this->telegram_id,
            'parse_mode'   => 'markdown',
            'text'         => Speaker::authorized($name, $program, $situation),
            'reply_markup' => Speaker::getReplyKeyboardMarkup(),
        ]);
    }
}
