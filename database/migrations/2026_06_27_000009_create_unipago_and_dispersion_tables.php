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
        // 1. Logs de Peticiones Unipago Mock
        Schema::create('unipago_mock_requests', function (Blueprint $table) {
            $table->id();
            $table->string('service_code');
            $table->string('service_name');
            $table->string('endpoint_mock');
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->string('status')->default('Processed');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->dateTime('processed_at')->nullable();
            $table->timestamps();
        });

        // 2. Lotes de Afiliación
        Schema::create('affiliation_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number')->unique();
            $table->string('batch_type'); // titulares, dependientes, novedades
            $table->string('unipago_lote_id')->nullable();
            $table->string('status')->default('VE'); // VE, PC, PE, RE, EV
            $table->integer('total_records')->default(0);
            $table->integer('total_ok')->default(0);
            $table->integer('total_pending')->default(0);
            $table->integer('total_rejected')->default(0);
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('processed_at')->nullable();
            $table->timestamps();
        });

        // 3. Detalles del Lote de Afiliación (Validaciones Unipago)
        Schema::create('affiliation_batch_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliation_batch_id')->constrained('affiliation_batches')->onDelete('cascade');
            $table->unsignedBigInteger('afiliado_id')->nullable(); // Relación a afiliados
            $table->unsignedBigInteger('dependiente_id')->nullable(); // Relación a dependientes
            $table->string('request_number')->nullable();
            $table->string('contract_number')->nullable();
            $table->string('status')->default('PE64'); // OK, PE64, PE75, PE10036, RE
            $table->string('reason_code')->nullable();
            $table->text('reason_description')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });

        // 4. Notificaciones de Cápitas
        Schema::create('capitation_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('notification_number')->unique();
            $table->unsignedBigInteger('afiliado_id'); // Titular o dependiente
            $table->string('period'); // YYYYMM (ej: 202606)
            $table->decimal('capitation_amount', 12, 2);
            $table->string('individualization_type')->default('Capita Normal');
            $table->string('status')->default('NT'); // IN, NT, IC, IR, DI, BL, RS, PE, RE
            $table->dateTime('notified_at')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamps();
        });

        // 5. Cortes de Dispersión
        Schema::create('dispersion_cuts', function (Blueprint $table) {
            $table->id();
            $table->string('cut_number')->unique();
            $table->string('period'); // YYYYMM
            $table->string('cut_type'); // primer corte, segundo corte, operativo
            $table->string('status')->default('Programado'); // Programado, En proceso, Generado, Certificado, Dispersado, Cerrado
            $table->integer('total_affiliates')->default(0);
            $table->integer('total_holders')->default(0);
            $table->integer('total_dependents')->default(0);
            $table->integer('total_capitations')->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->dateTime('generated_at')->nullable();
            $table->dateTime('certified_at')->nullable();
            $table->dateTime('dispersed_at')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->timestamps();
        });

        // 6. Detalles del Corte de Dispersión
        Schema::create('dispersion_cut_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispersion_cut_id')->constrained('dispersion_cuts')->onDelete('cascade');
            $table->foreignId('capitation_notification_id')->constrained('capitation_notifications')->onDelete('cascade');
            $table->unsignedBigInteger('afiliado_id');
            $table->decimal('amount', 12, 2);
            $table->string('status')->default('DI'); // DI (Dispersada)
            $table->timestamps();
        });

        // 7. Bandeja de Notificaciones Internas Unipago
        Schema::create('unipago_mock_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('notification_type'); // Lote recibido, Cápita notificada, etc.
            $table->string('reference_type')->nullable(); // batch, claim, dispersion, capitation
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('title');
            $table->text('message');
            $table->dateTime('read_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unipago_mock_notifications');
        Schema::dropIfExists('dispersion_cut_details');
        Schema::dropIfExists('dispersion_cuts');
        Schema::dropIfExists('capitation_notifications');
        Schema::dropIfExists('affiliation_batch_details');
        Schema::dropIfExists('affiliation_batches');
        Schema::dropIfExists('unipago_mock_requests');
    }
};
