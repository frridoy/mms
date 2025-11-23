<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW monthly_expenses_summary AS
            SELECT
                t.team_number,
                t.name AS team_name,
                me.year,
                me.month,
                SUM(me.amount) AS total_expenses
            FROM monthly_expenses me
            LEFT JOIN teams t ON t.id = me.team_id
            GROUP BY
                me.year,
                me.month,
                t.team_number,
                t.name
            ORDER BY
                me.year,
                me.month;
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_expenses_summary_view');
    }
};
