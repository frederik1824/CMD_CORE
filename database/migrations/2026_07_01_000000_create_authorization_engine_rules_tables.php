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
        Schema::create('authorization_engine_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('process'); // Core, Portal, Farmacia, Laboratorio, etc.
            $table->string('service_group')->nullable();
            $table->string('service_subgroup')->nullable();
            $table->string('pss_type')->nullable();
            $table->unsignedBigInteger('pss_id')->nullable();
            $table->unsignedBigInteger('health_plan_id')->nullable();
            $table->string('origin')->default('Core ARS');
            $table->text('condition_json')->nullable();
            $table->text('action_json')->nullable();
            $table->integer('priority')->default(1);
            $table->string('severity')->default('info'); // info, warning, blocking, audit_required
            $table->string('status')->default('Activa'); // Activa, Inactiva
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->unsignedBigInteger('created_by')->default(1);
            $table->timestamps();
        });

        Schema::create('authorization_engine_rule_tests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rule_id');
            $table->text('test_payload')->nullable();
            $table->text('result_payload')->nullable();
            $table->unsignedBigInteger('executed_by')->default(1);
            $table->timestamp('executed_at')->useCurrent();
            $table->timestamps();

            $table->foreign('rule_id')->references('id')->on('authorization_engine_rules')->onDelete('cascade');
        });

        Schema::create('authorization_engine_rule_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rule_id');
            $table->unsignedBigInteger('authorization_id')->nullable();
            $table->string('result')->nullable(); // approved, rejected, audit_required, warning
            $table->text('message')->nullable();
            $table->text('metadata')->nullable();
            $table->timestamps();

            $table->foreign('rule_id')->references('id')->on('authorization_engine_rules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authorization_engine_rule_logs');
        Schema::dropIfExists('authorization_engine_rule_tests');
        Schema::dropIfExists('authorization_engine_rules');
    }
};
