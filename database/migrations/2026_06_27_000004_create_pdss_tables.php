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
        // 1. pdss_plans
        Schema::create('pdss_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_number')->unique();
            $table->string('name');
            $table->string('resolution')->nullable();
            $table->string('version')->nullable();
            $table->string('source_file')->nullable();
            $table->timestamp('imported_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. pdss_groups
        Schema::create('pdss_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pdss_plan_id');
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('pdss_plan_id')->references('id')->on('pdss_plans')->onDelete('cascade');
            $table->unique(['pdss_plan_id', 'code']);
        });

        // 3. pdss_subgroups
        Schema::create('pdss_subgroups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pdss_group_id');
            $table->string('code');
            $table->string('name');
            $table->string('amount_coverage')->nullable(); // e.g. "Ilimitada", "RD$ X", "80%"
            $table->string('copay_type')->nullable(); // e.g. "No", "RD$ X", "20%"
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('pdss_group_id')->references('id')->on('pdss_groups')->onDelete('cascade');
            $table->unique(['pdss_group_id', 'code']);
        });

        // 4. pdss_services
        Schema::create('pdss_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pdss_plan_id');
            $table->unsignedBigInteger('pdss_group_id');
            $table->unsignedBigInteger('pdss_subgroup_id');
            $table->string('simon_code');
            $table->string('coverage_type')->nullable(); // e.g. "Laboratorio", "Consultas"
            $table->text('coverage_description');
            $table->string('cups_code')->nullable();
            $table->string('level_1_covered')->default('N'); // S / N
            $table->string('level_2_covered')->default('N');
            $table->string('level_3_covered')->default('N');
            $table->string('amount_coverage')->nullable();
            $table->string('copay_type')->nullable();
            $table->boolean('requires_authorization')->default(true);
            $table->boolean('requires_medical_audit')->default(false);
            $table->boolean('is_high_cost')->default(false);
            $table->boolean('is_emergency')->default(false);
            $table->boolean('is_hospitalization')->default(false);
            $table->boolean('is_surgery')->default(false);
            $table->boolean('is_diagnostic_support')->default(false);
            $table->boolean('is_medicine')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('source_page')->nullable();
            $table->text('raw_text')->nullable();
            $table->timestamps();

            $table->foreign('pdss_plan_id')->references('id')->on('pdss_plans')->onDelete('cascade');
            $table->foreign('pdss_group_id')->references('id')->on('pdss_groups')->onDelete('cascade');
            $table->foreign('pdss_subgroup_id')->references('id')->on('pdss_subgroups')->onDelete('cascade');
            
            // Unicidad
            $table->unique(['pdss_plan_id', 'simon_code', 'pdss_subgroup_id', 'cups_code'], 'pdss_services_unique');
        });

        // 5. pdss_import_logs
        Schema::create('pdss_import_logs', function (Blueprint $table) {
            $table->id();
            $table->string('source_file');
            $table->integer('total_pages')->default(0);
            $table->integer('total_groups')->default(0);
            $table->integer('total_subgroups')->default(0);
            $table->integer('total_services')->default(0);
            $table->string('imported_by')->nullable();
            $table->string('status')->default('Pendiente'); // Completado, Error, Procesando
            $table->text('errors')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdss_import_logs');
        Schema::dropIfExists('pdss_services');
        Schema::dropIfExists('pdss_subgroups');
        Schema::dropIfExists('pdss_groups');
        Schema::dropIfExists('pdss_plans');
    }
};
