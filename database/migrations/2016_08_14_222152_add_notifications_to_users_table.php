<?php

use Illuminate\Database\Migrations\Migration;

class AddNotificationsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add notify property to users table.
        Schema::table('users', function ($table) {
            $table->boolean('notify')->default(false)->after('course_data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove notify property from users table.
        Schema::table('users', function ($table) {
            $table->dropColumn('notify');
        });
    }
}
