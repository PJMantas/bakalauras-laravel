<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('group_name');
            
            $table->boolean('video_create')->default('0');
            $table->boolean('video_edit')->default('0');
            $table->boolean('video_delete')->default('0');

            $table->boolean('reaction_create')->default('0');
            $table->boolean('comment_create')->default('0');
            $table->boolean('comment_edit')->default('0');
            $table->boolean('comment_delete')->default('0');

            //administratoriaus teisės
            $table->boolean('is_admin')->default('0');
            $table->boolean('manage_users')->default('0');
            $table->boolean('manage_permissions')->default('0');
            $table->boolean('manage_genres')->default('0');

            $table->timestamps();
        });

        // Numatytosios naudotojo teisės
        DB::table('permissions')->insert(
            array(
                'group_name' => 'Registered User',
                'video_create' => '1',
                'video_edit' => '1',
                'video_delete' => '1',

                'reaction_create' => '1',
                'comment_create' => '1',
                'comment_edit' => '1',
                'comment_delete' => '1',

                // administratoriaus teisės
                'is_admin' => '0',
                'manage_users' => '0',
                'manage_permissions' => '0',
                'manage_genres' => '0',
            )
          
        );

        // Numatytosios naudotojo teisės
        DB::table('permissions')->insert(
            array(
                'group_name' => 'Admin',
                'video_create' => '1',
                'video_edit' => '1',
                'video_delete' => '1',

                'reaction_create' => '1',
                'comment_create' => '1',
                'comment_edit' => '1',
                'comment_delete' => '1',

                // administratoriaus teisės
                'is_admin' => '1',
                'manage_users' => '1',
                'manage_permissions' => '1',
                'manage_genres' => '1',
            )
          
        );

        // Tušia teisės grupė
        DB::table('permissions')->insert(
            array(
                'group_name' => 'Unregistered User',
                'video_create' => '0',
                'video_edit' => '0',
                'video_delete' => '0',

                'reaction_create' => '0',
                'comment_create' => '0',
                'comment_edit' => '0',
                'comment_delete' => '0',

                // administratoriaus teisės
                'is_admin' => '0',
                'manage_users' => '0',
                'manage_permissions' => '0',
                'manage_genres' => '0',
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
        Schema::dropIfExists('permissions');
    }
}
