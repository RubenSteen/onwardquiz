<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('discord_id')->unique();
            $table->string('token')->unique();
            $table->string('email');
            $table->string('username');
            $table->string('locale');
            $table->string('discriminator');
            $table->string('avatar')->nullable();
            $table->boolean('hide_avatar')->default(false);
            $table->boolean('editor')->default(false);
            $table->boolean('super_admin')->default(false);
            $table->boolean('banned')->default(false);
            $table->boolean('confirmed')->default(false);
            $table->dateTime('last_activity')->nullable();
            $table->dateTime('last_discord_sync');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
