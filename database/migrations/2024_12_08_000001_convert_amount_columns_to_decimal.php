<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ConvertAmountColumnsToDecimal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Convert deposits.amount to decimal
        DB::statement('ALTER TABLE deposits ALTER COLUMN amount TYPE NUMERIC(20,2) USING CASE WHEN amount IS NULL OR amount = \'\' THEN NULL ELSE amount::numeric END');
        
        // Convert withdrawals.amount to decimal
        DB::statement('ALTER TABLE withdrawals ALTER COLUMN amount TYPE NUMERIC(20,2) USING CASE WHEN amount IS NULL OR amount = \'\' THEN NULL ELSE amount::numeric END');
        
        // Convert withdrawals.to_deduct to decimal
        DB::statement('ALTER TABLE withdrawals ALTER COLUMN to_deduct TYPE NUMERIC(20,2) USING CASE WHEN to_deduct IS NULL OR to_deduct = \'\' THEN NULL ELSE to_deduct::numeric END');
        
        // Convert user_plans.amount to decimal
        DB::statement('ALTER TABLE user_plans ALTER COLUMN amount TYPE NUMERIC(20,2) USING CASE WHEN amount IS NULL OR amount = \'\' THEN NULL ELSE amount::numeric END');
        
        // Convert tp__transactions.amount to decimal
        DB::statement('ALTER TABLE tp__transactions ALTER COLUMN amount TYPE NUMERIC(20,2) USING CASE WHEN amount IS NULL OR amount = \'\' THEN NULL ELSE amount::numeric END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert deposits.amount to string
        DB::statement('ALTER TABLE deposits ALTER COLUMN amount TYPE VARCHAR(255)');
        
        // Revert withdrawals.amount to string
        DB::statement('ALTER TABLE withdrawals ALTER COLUMN amount TYPE VARCHAR(255)');
        
        // Revert withdrawals.to_deduct to string
        DB::statement('ALTER TABLE withdrawals ALTER COLUMN to_deduct TYPE VARCHAR(255)');
        
        // Revert user_plans.amount to string
        DB::statement('ALTER TABLE user_plans ALTER COLUMN amount TYPE VARCHAR(255)');
        
        // Revert tp__transactions.amount to string
        DB::statement('ALTER TABLE tp__transactions ALTER COLUMN amount TYPE VARCHAR(255)');
    }
}
