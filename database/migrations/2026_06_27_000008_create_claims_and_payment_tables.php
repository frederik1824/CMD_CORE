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
        // 1. Reclamaciones
        Schema::create('authorization_claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_number')->unique();
            $table->foreignId('authorization_id')->constrained('autorizaciones')->onDelete('cascade');
            $table->foreignId('pss_id')->constrained('pss')->onDelete('cascade');
            $table->unsignedBigInteger('afiliado_id'); // Relación polimórfica o directa por ID
            $table->string('invoice_number')->nullable();
            $table->string('ncf')->nullable();
            $table->date('service_date');
            $table->dateTime('received_at')->nullable();
            $table->decimal('claimed_amount', 14, 2);
            $table->decimal('authorized_amount', 14, 2);
            $table->decimal('approved_amount', 14, 2)->default(0);
            $table->decimal('objected_amount', 14, 2)->default(0);
            $table->string('status')->default('Reclamación recibida');
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->unsignedBigInteger('received_by')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });

        // 2. Documentos Soporte de la Reclamación
        Schema::create('claim_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->constrained('authorization_claims')->onDelete('cascade');
            $table->string('document_type');
            $table->string('file_path');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->dateTime('uploaded_at')->nullable();
            $table->string('status')->default('Activo');
            $table->timestamps();
        });

        // 3. Auditoría de Reclamaciones
        Schema::create('claim_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->constrained('authorization_claims')->onDelete('cascade');
            $table->string('audit_type');
            $table->unsignedBigInteger('auditor_id')->nullable();
            $table->string('status')->default('Pendiente de auditoría');
            $table->decimal('claimed_amount', 14, 2);
            $table->decimal('approved_amount', 14, 2)->default(0);
            $table->decimal('objected_amount', 14, 2)->default(0);
            $table->string('objection_reason')->nullable();
            $table->text('internal_observation')->nullable();
            $table->text('pss_observation')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->timestamps();
        });

        // 4. Cuentas por Pagar (CXP)
        Schema::create('accounts_payable', function (Blueprint $table) {
            $table->id();
            $table->string('payable_number')->unique();
            $table->foreignId('claim_id')->constrained('authorization_claims')->onDelete('cascade');
            $table->foreignId('authorization_id')->constrained('autorizaciones')->onDelete('cascade');
            $table->foreignId('pss_id')->constrained('pss')->onDelete('cascade');
            $table->decimal('amount', 14, 2);
            $table->decimal('retained_amount', 14, 2)->default(0);
            $table->decimal('net_amount', 14, 2);
            $table->string('status')->default('Generada');
            $table->unsignedBigInteger('generated_by')->nullable();
            $table->dateTime('generated_at')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamps();
        });

        // 5. Lotes de Pago
        Schema::create('payment_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number')->unique();
            $table->string('status')->default('Borrador');
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->integer('total_items')->default(0);
            $table->date('scheduled_payment_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
        });

        // 6. Detalles del Lote de Pago
        Schema::create('payment_batch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_batch_id')->constrained('payment_batches')->onDelete('cascade');
            $table->foreignId('account_payable_id')->constrained('accounts_payable')->onDelete('cascade');
            $table->decimal('amount', 14, 2);
            $table->string('status')->default('En lote de pago');
            $table->timestamps();
        });

        // 7. Conciliación de Pagos
        Schema::create('payment_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_batch_id')->constrained('payment_batches')->onDelete('cascade');
            $table->foreignId('account_payable_id')->constrained('accounts_payable')->onDelete('cascade');
            $table->foreignId('pss_id')->constrained('pss')->onDelete('cascade');
            $table->decimal('expected_amount', 14, 2);
            $table->decimal('paid_amount', 14, 2);
            $table->decimal('difference', 14, 2)->default(0);
            $table->string('bank_reference')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('status')->default('Pendiente');
            $table->text('observations')->nullable();
            $table->timestamps();
        });

        // 8. Línea de Tiempo de Autorizaciones
        Schema::create('authorization_timeline_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('authorization_id')->constrained('autorizaciones')->onDelete('cascade');
            $table->string('event_type');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authorization_timeline_events');
        Schema::dropIfExists('payment_reconciliations');
        Schema::dropIfExists('payment_batch_items');
        Schema::dropIfExists('payment_batches');
        Schema::dropIfExists('accounts_payable');
        Schema::dropIfExists('claim_audits');
        Schema::dropIfExists('claim_documents');
        Schema::dropIfExists('authorization_claims');
    }
};
