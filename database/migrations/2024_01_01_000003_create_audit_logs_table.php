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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type')->index(); // login, logout, failed_login, session_expired, etc.
            $table->string('user_type')->index(); // admin, employee, guest
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->string('ip_address', 45)->index();
            $table->string('user_agent')->nullable();
            $table->string('session_id')->nullable()->index();
            $table->string('status')->index(); // success, failure, warning, error
            $table->text('message')->nullable();
            $table->json('metadata')->nullable(); // Additional context data
            $table->string('risk_level')->default('low')->index(); // low, medium, high, critical
            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();

            // Indexes for efficient querying
            $table->index(['event_type', 'occurred_at']);
            $table->index(['user_id', 'occurred_at']);
            $table->index(['ip_address', 'occurred_at']);
            $table->index(['status', 'risk_level', 'occurred_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
