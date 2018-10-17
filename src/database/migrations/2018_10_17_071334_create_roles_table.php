<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->string('role')->unique();
        });
        
        Illuminate\Support\Facades\DB::table('roles')->insert([
            ['role' => 'master'],
            ['role' => 'admin']
        ]);
        
        Schema::table('users', function(Blueprint $table){
            $table->foreign("role")->references("role")->on("roles")->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $table){
            $table->dropForeign('users_role_foreign');
        });
        
        Schema::dropIfExists('roles');
    }
}
