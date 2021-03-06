<?php

namespace App;

use App\Telegram\Tools\Speaker;
use Bugsnag;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Ivmelo\SUAP\SUAP;
use Telegram;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'username', 'email',
        'password', 'telegram_id', 'suap_id', 'suap_key',
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
     * Scope a query to only include users with SUAP credentials.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasSuapCredentials($query)
    {
        return $query->where('suap_id', '!=', null)->where('suap_key', '!=', null);
    }

    /**
     * Refresh or create a new SUAP access token.
     *
     * @return string $token
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
     * Update school year or term for a student.
     *
     * @return string $token
     */
    public function updateSchoolYear()
    {
        $suap = new SUAP($this->suap_token);

        try {
            // Try to update the user's current school year.
            $data = $suap->getMeusPeriodosLetivos();
            $currentTerm = end($data);
            $this->school_year_term = $currentTerm['ano_letivo'].'.'.$currentTerm['periodo_letivo'];
            $this->save();

            return true;
        } catch (\Exception $e) {
            Bugsnag::notifyException($e);

            return false;
        }
    }

    /**
     * Acessor to get the school year of a student.
     *
     * @return string
     */
    public function getSchoolYearAttribute()
    {
        return explode('.', $this->school_year_term)[0];
    }

    /**
     * Acessor to get the school term (semestre/bimestre) of a student.
     *
     * @return string
     */
    public function getSchoolTermAttribute()
    {
        return explode('.', $this->school_year_term)[1];
    }

    /**
     * Increases the request count of a student.
     *
     * @return string
     */
    public function updateLastRequest($save = false)
    {
        $this->request_count++;
        $this->last_request = Carbon::now();
        if ($save) {
            $this->save();
        }
    }

    /**
     * The settings object of this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function settings()
    {
        return $this->hasOne(Settings::class);
    }

    /**
     * The report card object of this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function report_card()
    {
        return $this->hasOne(ReportCard::class);
    }

    /**
     * Authorize access and store SUAP credentials.
     *
     * @param int    $suap_id
     * @param string $suap_key
     */
    public function authorize($suap_id, $suap_key, $notify = true)
    {
        try {
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

                $this->updateSchoolYear();

                // Get course data for the first access.
                $course_data = $client->getMeuBoletim($this->school_year, $this->school_term);
                $course_data_json = json_encode($course_data);

                $this->save();

                $this->report_card()->create([
                    'course_data' => $course_data_json,
                ]);
            }

            // Grab user info for display.
            $name = $suap_data['nome_usual'];
            $program = $suap_data['vinculo']['curso'];
            $situation = $suap_data['vinculo']['situacao'];

            if ($notify) {
                // All set, message user.
                // And set up keyboard.
                Telegram::sendMessage([
                    'chat_id'      => $this->telegram_id,
                    'parse_mode'   => 'markdown',
                    'text'         => Speaker::authorized($name, $program, $situation),
                    'reply_markup' => Speaker::getReplyKeyboardMarkup(),
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Bugsnag::notifyException($e);

            return false;
        }
    }
}
