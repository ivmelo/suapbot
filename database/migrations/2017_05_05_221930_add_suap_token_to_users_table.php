<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSuapTokenToUsersTable extends Migration
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
            $table->text('suap_token')->nullable()->after('is_admin');
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
            $table->dropColumn('suap_token');
        });
    }
}
