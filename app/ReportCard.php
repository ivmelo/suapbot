<?php

namespace App;

use App\Telegram\Tools\Markify;
use Bugsnag;
use Illuminate\Database\Eloquent\Model;
use Ivmelo\SUAP\SUAP;
use Telegram;

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
                            'text'         => "ℹ️ Novas disciplinas foram adicionadas ou removidas do seu boletim. \n\nDigite /boletim para ver detalhes.",
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

                    // Check if there is updates (just to make sure...).
                    if (count($updates) > 0) {
                        $status = self::UPDATED;

                        // Notify user if set to do so.
                        if ($notify) {
                            Telegram::sendMessage([
                                'chat_id'      => $this->user->telegram_id,
                                'parse_mode'   => 'markdown',
                                'text'         => Markify::parseBoletim($updates),
                            ]);
                        }
                    }

                    print_r($updates);
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
