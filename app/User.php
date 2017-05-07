<?php

namespace App;

use App\Telegram\Tools\Markify;
use Ivmelo\SUAP\SUAP;
use Telegram;
use Carbon\Carbon;
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
        'first_name', 'last_name', 'username',
        'email', 'password', 'telegram_id', 'suap_id',
        'suap_key', 'course_data', 'notify',
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasSuapCredentials($query)
    {
       return $query->where('suap_id', '!=', null)->where('suap_key', '!=', null);
    }

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
     * Update school year or term for a student.
     *
     * @return String $token
     */
    public function updateSchoolYear()
    {
        $suap = new SUAP($this->suap_token);

        try {
            // Try to update the user's current school term.
            $data = $suap->getMeusPeriodosLetivos();
            $currentTerm = end($data);
            $this->school_year_term = $currentTerm['ano_letivo'].'.'.$currentTerm['periodo_letivo'];
            $this->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getSchoolYearAttribute()
    {
        return explode('.', $this->school_year_term)[0];
    }

    public function getSchoolTermAttribute()
    {
        return explode('.', $this->school_year_term)[1];
    }

    public function updateLastRequest($save = false)
    {
        $this->request_count++;
        $this->last_request = Carbon::now();
        if ($save) {
            $this->save();
        }
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

    public function updateReportCard($notify = false)
    {
        $suap = new SUAP($this->suap_token);

        $currentDataJson = $this->course_data;
        $currentData = json_decode($currentDataJson, true);
        $newData = $suap->getMeuBoletim($this->school_year, $this->school_term);
        $newDataJson = json_encode($newData);

        if ($newDataJson != $currentDataJson) {
            // Data has changed. Save new data.
            $this->course_data = $newDataJson;

            echo "changes...\n";

            if (count($newData) != count($currentData)) {
                // TODO: Courses added/removed.
                echo "courses added/removed\n";

            } else {
                $updates = [];

                echo "comparing\n";


                // Compare course data.
                for ($i = 0; $i < count($currentData); $i++) {
                    // Grab data for current course.
                    $currentCourseData = $currentData[$i];
                    $newCourseData = $newData[$i];

                    // Compare the old course data with the new course data.
                    if ($updatedData = $this->array_diff_assoc_recursive($newCourseData, $currentCourseData)) {

                        echo "changes\n";

                        // Add the course name to the list of updated info, so it can be displayed.
                        $updatedData['disciplina'] = $currentCourseData['disciplina'];
                        array_push($updates, $updatedData);
                    }
                }

                echo "counting\n";


                if (count($updates) > 0) {
                    echo "replying\n";

                    $gradesResponse = Markify::parseBoletim($updates);

                    if ($notify) {
                        Telegram::sendMessage([
                            'chat_id'      => $this->telegram_id,
                            'parse_mode'   => 'markdown',
                            'text'         => $gradesResponse,
                        ]);
                    }

                } else {
                    echo "updates < 0\n";
                }

                print_r($updates);
            }

            // SAVE EVERYTHING...
            $this->save();


        } else {
            // TODO: No changes.
            echo "no changes\n";
        }



    }

    /**
     * Same as array_diff, but associative and recursive.
     *
     * @param array  $array1
     * @param array  $array2
     *
     * @return array $difference
     */
    private function array_diff_assoc_recursive($array1, $array2) {
        $difference = [];
        foreach($array1 as $key => $value) {
            if(is_array($value)) {
                if(!isset($array2[$key]) || !is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = $this->array_diff_assoc_recursive($value, $array2[$key]);
                    if(!empty($new_diff))
                        $difference[$key] = $new_diff;
                }
            } else if(!array_key_exists($key,$array2) || $array2[$key] !== $value) {
                $difference[$key] = $value;
            }
        }
        return $difference;
    }
}
