<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
        CREATE OR REPLACE VIEW month_expenses_view AS
        SELECT
            me.id AS month_expense_id,       -- Expense ID
            me.lookup_id,                    -- Lookup ID
            l.name AS lookup_name,           -- Lookup name
            me.month,                        -- Month of expense
            me.year,                         -- Year of expense
            t.name AS team_name,             -- Team name
            t.team_number,                   -- Team number
            u_manager.id AS manager_id,      -- Manager's user ID
            u_manager.name AS manager_name,  -- Manager's name
            me.created_by,                   -- Who created the expense
            u_creator.name AS created_by_name, -- Creator's name
            me.created_at,                   -- Expense creation timestamp
            me.updated_at                    -- Expense last update timestamp
        FROM monthly_expenses me
        LEFT JOIN lookups l ON me.lookup_id = l.id
        LEFT JOIN teams t ON me.team_id = t.id
        LEFT JOIN users u_manager ON t.user_id = u_manager.id AND t.is_manager = 1
        LEFT JOIN users u_creator ON me.created_by = u_creator.id
    ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS month_expenses_view");
    }
};
