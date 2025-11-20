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
        Schema::create('monthly_expenses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lookup_id')
                ->constrained('lookups')
                ->onDelete('cascade');

            $table->unsignedInteger('month')->index();
            $table->unsignedInteger('year')->index();

            $table->date('expense_date')->nullable();
            $table->decimal('amount', 10, 2);

            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('team_id')
                ->nullable()
                ->constrained('teams', 'id')
                ->onDelete('set null');

            $table->string('description')->nullable();

            $table->timestamps();
            $table->index(['month', 'year']);
            $table->index(['created_by', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_expenses');
    }
};
