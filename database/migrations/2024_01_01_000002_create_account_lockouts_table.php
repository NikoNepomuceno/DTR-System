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
        Schema::create('account_lockouts', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->index(); // email or IP address
            $table->string('type')->index(); // 'email' or 'ip'
            $table->string('reason')->default('failed_login_attempts');
            $table->integer('attempt_count')->default(0);
            $table->timestamp('locked_at')->useCurrent();
            $table->timestamp('locked_until')->nullable();
            $table->timestamp('unlocked_at')->nullable();
            $table->string('unlocked_by')->nullable(); // 'auto', 'admin', 'user'
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Composite indexes for efficient querying
            $table->index(['identifier', 'type', 'locked_until']);
            $table->index(['locked_until', 'unlocked_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_lockouts');
    }
};
