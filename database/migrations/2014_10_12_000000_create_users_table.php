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
            $table->id();
            $table->string('username');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->integer('age')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->foreignId('group_id')->constrained('permissions');
            $table->string('avatar_url')->nullable();
        });

        //Admin default user
        DB::table('users')->insert(
            array(
                'username' => 'admin',
                'email' => 'admin123@gmail.com',
                'password' => bcrypt('admin123'),
                'first_name' => 'Admin',
                'last_name' => 'Admin',
                'age' => '0',
                'city' => 'Kaunas',
                'country' => 'Lietuva',
                'group_id' => '2',
            )
        );
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
