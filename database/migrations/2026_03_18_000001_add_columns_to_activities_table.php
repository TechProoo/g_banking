<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToActivitiesTable extends Migration
{
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            if (!Schema::hasColumn('activities', 'user')) {
                $table->unsignedBigInteger('user')->nullable();
            }
            if (!Schema::hasColumn('activities', 'ip_address')) {
                $table->string('ip_address')->nullable();
            }
            if (!Schema::hasColumn('activities', 'device')) {
                $table->string('device')->nullable();
            }
            if (!Schema::hasColumn('activities', 'browser')) {
                $table->string('browser')->nullable();
            }
            if (!Schema::hasColumn('activities', 'os')) {
                $table->string('os')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['user', 'ip_address', 'device', 'browser', 'os']);
        });
    }
}
