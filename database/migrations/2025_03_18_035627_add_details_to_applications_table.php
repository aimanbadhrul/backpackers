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
        Schema::table('applications', function (Blueprint $table) {
            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->timestamp('submission_date')->nullable();
            $table->timestamp('approval_date')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'waived'])->default('pending');
            $table->string('group')->nullable();
            $table->text('special_requests')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'full_name', 'email', 'phone', 'emergency_contact_name', 'emergency_contact_phone',
                'submission_date', 'approval_date', 'rejection_reason', 'payment_status',
                'group', 'special_requests'
            ]);
        });
    }
};
