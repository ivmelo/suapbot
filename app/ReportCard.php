<?php

namespace App;

use App\Telegram\Tools\Speaker;
use Bugsnag;
use Illuminate\Database\Eloquent\Model;
use Ivmelo\SUAP\SUAP;
use Telegram;
use View;

class ReportCard extends Model
{
    // Status for grades processing.
    const NO_CHANGES = 0;
    const UPDATED = 1;
    const COURSES_CHANGED = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['course_data'];

    /**
     * The user from which this report card belongs.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Update the report card of a student.
     *
     * @param bool $notify Wether to notify the user or not.
     *
     * @return string
     */
    public function doUpdate($notify = false)
    {
        $status = self::NO_CHANGES;

        try {
            $suap = new SUAP($this->user->suap_token);

            // Get old and new data.
            $currentDataJson = $this->course_data;
            $currentData = json_decode($currentDataJson, true);
            $newData = $suap->getMeuBoletim($this->user->school_year, $this->user->school_term);
            $newDataJson = json_encode($newData);

            // Compare new data with old data. If not the same, there were changes.
            if ($newDataJson != $currentDataJson) {

                // Data has changed. Save it.
                $this->course_data = $newDataJson;
                $this->save();

                if (count($newData) != count($currentData)) {
                    // Courses were added or removed. Notify user.
                    $status = self::COURSES_CHANGED;

                    // Notify user if set to do so.
                    if ($notify) {
                        Telegram::sendMessage([
                            'chat_id'      => $this->user->telegram_id,
                            'parse_mode'   => 'markdown',
                            'text'         => "ℹ️ Disciplinas foram adicionadas ou removidas do seu boletim. \n\nDigite /boletim para ver detalhes.",
                        ]);
                    }
                } else {
                    $updates = [];

                    // Compare course data.
                    for ($i = 0; $i < count($currentData); $i++) {
                        // Grab data for current course.
                        $currentCourseData = $currentData[$i];
                        $newCourseData = $newData[$i];

                        // Compare the old course data with the new course data.
                        if ($updatedData = $this->array_diff_assoc_recursive($newCourseData, $currentCourseData)) {
                            // Add the course name to the list of updated info, so it can be displayed.
                            $updatedData['disciplina'] = $currentCourseData['disciplina'];
                            array_push($updates, $updatedData);
                        }
                    }

                    // Check if there are updates (just to make sure...).
                    if (count($updates) > 0) {
                        $status = self::UPDATED;

                        // For debuging.
                        print_r($updates);

                        if ($notify && $this->shouldNotifyUser($updates, $this->user->settings)) {
                            $this->notifyUser($updates, $newData);
                        }
                    }
                }
            } else {
                // No changes.
                $status = self::NO_CHANGES;
            }
        } catch (\Exception $e) {
            Bugsnag::notifyException($e);

            return false;
        }

        return $status;
    }

    /**
     * Finds out if the user should be notified
     * according to their notification settings.
     *
     * @param array        $updates  The report card diff.
     * @param App\Settings $settings The Settings object of the user.
     *
     * @return bool Whether the user should be notified.
     */
    private function shouldNotifyUser($updates, $settings)
    {
        // Check if the user allowed notifications
        // for the kind of update that just happened.
        foreach ($updates as $update) {
            if (isset($update['carga_horaria_cumprida'])) {
                if ($settings->classes) {
                    return true;
                }
            }

            if (isset($update['numero_faltas'])) {
                if ($settings->attendance) {
                    return true;
                }
            }

            if (isset($update['media_final_disciplina'])) {
                if ($settings->grades) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Notifies a user about changes in his report card.
     */
    private function notifyUser($updates, $newData)
    {
        // Render report card.
        $reportCard = View::make('telegram.reportcard', [
            'grades' => $updates,
            'stats'  => self::calculateStats($newData),
            'update' => true,
        ])->render();

        // Notify user if set to do so.
        Telegram::sendMessage([
            'chat_id'      => $this->user->telegram_id,
            'parse_mode'   => 'markdown',
            'text'         => $reportCard,
            'reply_markup' => Speaker::getReplyKeyboardMarkup(),
        ]);
    }

    /**
     * Calculates the total course hours, attendance, classes given
     * and skipped classes of a studen, given their report card.
     *
     * @param array $reportCard The student's report card.
     *
     * @return array The calculated stats.
     */
    public static function calculateStats($reportCard)
    {
        $totalCargaHoraria = 0;
        $totalAulas = 0;
        $totalFaltas = 0;
        $attendance = 0;

        foreach ($reportCard as $grade) {
            // Add to stats.
            if (isset($grade['carga_horaria'])) {
                // code...
                $totalCargaHoraria += $grade['carga_horaria'];
                $totalAulas += $grade['carga_horaria_cumprida'];
                $totalFaltas += $grade['numero_faltas'];
            }
        }

        if ($totalCargaHoraria != 0) {
            // Calculate total attendance.
            if ($totalFaltas == 0) {
                $attendance = 100;
            } else {
                $attendance = 100 * ($totalAulas - $totalFaltas) / $totalAulas;
            }
        }

        $stats = [
            'total_carga_horaria' => $totalCargaHoraria,
            'total_aulas'         => $totalAulas,
            'total_faltas'        => $totalFaltas,
            'frequencia'          => $attendance,
        ];

        return $stats;
    }

    /**
     * Same as array_diff, but associative and recursive.
     * It's used to get a diff of the student report card.
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array $difference
     */
    private function array_diff_assoc_recursive($array1, $array2)
    {
        $difference = [];
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key]) || !is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = $this->array_diff_assoc_recursive($value, $array2[$key]);
                    if (!empty($new_diff)) {
                        $difference[$key] = $new_diff;
                    }
                }
            } elseif (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
                $difference[$key] = $value;
            }
        }

        return $difference;
    }
}
