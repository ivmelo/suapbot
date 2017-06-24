<?php

namespace App;

use Bugsnag;
use Ivmelo\SUAP\SUAP;
use Illuminate\Database\Eloquent\Model;

class ReportCard extends Model
{
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
     * @param boolean $notify Wether to notify the user or not.
     * @return string
     */
    public function updateReportCard($notify = false)
    {
        try {
            $suap = new SUAP($this->user->suap_token);

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
        } catch (\Exception $e) {
            Bugsnag::notifyException($e);
            return false;
        }
    }

    /**
     * Same as array_diff, but associative and recursive.
     * It's used to get a diff of the student report card.
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
