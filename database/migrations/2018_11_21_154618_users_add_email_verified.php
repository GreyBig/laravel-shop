<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersAddEmailVerified extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('email_verified')->default(false)->after('remember_token');
            // boolean('email_verified') 代表添加一个名为 email_verified 的布尔类型字段（在 Mysql 中是 tinyint(1) 类型）
            // default(false) 代表默认值是 false（在 Mysql 中是 0）
            // after('remember_token') 代表字段的位置是在 remember_token 后面
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
