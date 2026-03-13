<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique();
            }
            if (!Schema::hasColumn('users', 'lastname')) {
                $table->string('lastname')->nullable();
            }
            if (!Schema::hasColumn('users', 'middlename')) {
                $table->string('middlename')->nullable();
            }
            if (!Schema::hasColumn('users', 'accounttype')) {
                $table->string('accounttype')->nullable();
            }
            if (!Schema::hasColumn('users', 'pin')) {
                $table->string('pin')->nullable();
            }
            if (!Schema::hasColumn('users', 'pinstatus')) {
                $table->string('pinstatus')->nullable();
            }
            if (!Schema::hasColumn('users', 'usernumber')) {
                $table->string('usernumber')->nullable();
            }
            if (!Schema::hasColumn('users', 'code1')) {
                $table->string('code1')->nullable();
            }
            if (!Schema::hasColumn('users', 'code2')) {
                $table->string('code2')->nullable();
            }
            if (!Schema::hasColumn('users', 'code3')) {
                $table->string('code3')->nullable();
            }
            if (!Schema::hasColumn('users', 'assign_to')) {
                $table->unsignedBigInteger('assign_to')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username', 'lastname', 'middlename', 'accounttype',
                'pin', 'pinstatus', 'usernumber', 'code1', 'code2', 'code3', 'assign_to',
            ]);
        });
    }
}
