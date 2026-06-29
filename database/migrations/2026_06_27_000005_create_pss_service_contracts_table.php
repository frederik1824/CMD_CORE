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
        // 1. pss_service_contracts
        Schema::create('pss_service_contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pss_id'); // FK pss
            $table->unsignedBigInteger('pdss_service_id'); // FK pdss_services
            $table->decimal('contracted_amount', 12, 2)->default(0.00);
            $table->boolean('authorization_required')->default(true);
            $table->boolean('audit_required')->default(false);
            $table->integer('frequency_limit')->nullable();
            $table->string('frequency_period')->nullable(); // e.g. "Diario", "Anual", "Mensual"
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('pss_id')->references('id')->on('pss')->onDelete('cascade');
            $table->foreign('pdss_service_id')->references('id')->on('pdss_services')->onDelete('cascade');
            $table->unique(['pss_id', 'pdss_service_id']);
        });

        // 2. authorization_service_validations
        Schema::create('authorization_service_validations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('authorization_id'); // We'll link manually to keep compatibility or use constraint
            $table->unsignedBigInteger('pdss_service_id');
            $table->string('validation_type'); // e.g. "Afiliado", "PSS", "Servicio", "Nivel", "Monto"
            $table->string('status'); // Aprobado, Rechazado, Auditoria
            $table->text('message')->nullable();
            $table->text('metadata')->nullable(); // JSON metadata
            $table->timestamps();

            $table->foreign('pdss_service_id')->references('id')->on('pdss_services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authorization_service_validations');
        Schema::dropIfExists('pss_service_contracts');
    }
};
