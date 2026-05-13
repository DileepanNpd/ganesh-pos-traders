<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->time('clock_in_time')->nullable()->default("09:30:00");
            $table->time('clock_out_time')->nullable()->default("18:00:00");
            $table->string('leave_start_month', 2)->default('01');
            $table->integer('late_mark_after')->nullable()->default(null);
            $table->integer('early_clock_in_time')->nullable()->default(null);
            $table->integer('allow_clock_out_till')->nullable()->default(null);
            $table->boolean('self_clocking')->default(1);
            $table->text('allowed_ip_address')->nullable()->default(null);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('clock_in_time');
            $table->dropColumn('clock_out_time');
            $table->dropColumn('leave_start_month');
            $table->dropColumn('late_mark_after');
            $table->dropColumn('self_clocking');
            $table->dropColumn('early_clock_in_time');
            $table->dropColumn('allow_clock_out_till');
            $table->dropColumn('allowed_ip_address');
        });
    }
};
