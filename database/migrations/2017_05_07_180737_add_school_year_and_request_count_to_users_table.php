<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSchoolYearAndRequestCountToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Adds school year, term, and request count to users table.
        Schema::table('users', function ($table) {
            $table->string('school_year_term')->nullable()->after('course_data');
            $table->integer('request_count')->default(0)->after('remember_token');
            $table->timestamp('last_request')->nullable()->after('request_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Removes school year, term, and request count to users table.
        Schema::table('users', function ($table) {
            $table->dropColumn('last_request');
            $table->dropColumn('request_count');
            $table->dropColumn('school_year_term');
        });
    }
}
