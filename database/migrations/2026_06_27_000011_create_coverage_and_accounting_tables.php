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
        // 1. Reglas de Cobertura PDSS 11.0
        Schema::create('pdss_coverage_rules', function (Blueprint $table) {
            $table->id();
            $table->string('plan_code');
            $table->string('plan_name');
            $table->date('effective_date')->nullable();
            $table->string('service_group');
            $table->string('service_subgroup')->nullable();
            $table->string('coverage_limit_type'); // ilimitada, anual, evento, diaria, tramos
            $table->decimal('coverage_limit_amount', 14, 2)->default(0);
            $table->decimal('coverage_percent_ars', 5, 2)->default(0); // Ej: 80.00
            $table->decimal('copay_percent_affiliate', 5, 2)->default(0); // Ej: 20.00
            $table->decimal('copay_fixed_amount', 14, 2)->default(0);
            $table->decimal('copay_cap_amount', 14, 2)->default(0); // Límite de copago
            $table->decimal('annual_limit', 14, 2)->default(0);
            $table->decimal('event_limit', 14, 2)->default(0);
            $table->decimal('daily_limit', 14, 2)->default(0);
            $table->boolean('requires_continuity_validation')->default(false);
            $table->boolean('requires_seniority_validation')->default(false);
            $table->boolean('requires_authorization')->default(true);
            $table->boolean('requires_medical_audit')->default(false);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Acumuladores de Cobertura
        Schema::create('pdss_coverage_accumulators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('afiliado_id');
            $table->unsignedBigInteger('pdss_service_id')->nullable();
            $table->string('service_group');
            $table->string('period_year'); // YYYY
            $table->string('event_key')->nullable(); // Para límites por evento
            $table->decimal('accumulated_authorized_amount', 14, 2)->default(0);
            $table->decimal('accumulated_claimed_amount', 14, 2)->default(0);
            $table->decimal('accumulated_paid_amount', 14, 2)->default(0);
            $table->decimal('available_amount', 14, 2)->default(0);
            $table->timestamps();
        });

        // 3. Tabla de Médicos Auditores
        Schema::create('medical_auditors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('auditor_code')->unique();
            $table->string('exequatur');
            $table->string('auditor_type')->default('fisico'); // fisico/moral
            $table->string('professional_type')->default('medico'); // medico/odontologo
            $table->string('status')->default('Activo');
            $table->dateTime('registered_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });

        // 4. Glosas de Reclamaciones
        Schema::create('claim_glosses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->constrained('authorization_claims')->onDelete('cascade');
            $table->unsignedBigInteger('claim_detail_id')->nullable(); // Si hay detalle de ítems
            $table->foreignId('audit_id')->nullable()->constrained('claim_audits')->onDelete('set null');
            $table->string('glosa_code');
            $table->string('glosa_type');
            $table->string('objected_service');
            $table->text('objection_reason');
            $table->string('evidence_reference')->nullable();
            $table->decimal('original_amount', 14, 2);
            $table->decimal('objected_amount', 14, 2);
            $table->decimal('recognized_amount', 14, 2)->default(0);
            $table->string('status')->default('Registrada'); // Registrada, Notificada a PSS, En conciliación, Ratificada, Levantada, Parcialmente aceptada, En arbitraje, Cerrada
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 5. Conciliaciones de Glosas
        Schema::create('claim_conciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->constrained('authorization_claims')->onDelete('cascade');
            $table->foreignId('glosa_id')->constrained('claim_glosses')->onDelete('cascade');
            $table->string('instance')->default('primera_instancia'); // primera_instancia, segunda_instancia, arbitraje
            $table->string('requested_by')->nullable(); // PSS / ARS
            $table->dateTime('requested_at')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->string('result_status')->nullable(); // Aceptada, Rechazada, Aceptada Parcial
            $table->decimal('agreement_amount', 14, 2)->default(0);
            $table->text('ars_observation')->nullable();
            $table->text('pss_observation')->nullable();
            $table->string('final_decision')->nullable(); // Ratificada, Levantada, Parcialmente Aceptada
            $table->string('signed_document_path')->nullable();
            $table->timestamps();
        });

        // 6. Casos de Reembolsos de Afiliados
        Schema::create('reimbursement_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique();
            $table->unsignedBigInteger('afiliado_id'); // ID del Afiliado
            $table->string('origin')->default('ars'); // ars, dida, sisalril, idoppril
            $table->string('request_channel')->default('presencial'); // presencial, app, correo, otro
            $table->string('request_type')->default('cobro_indebido'); // cobro_indebido, negacion_cobertura, ambas
            $table->foreignId('pss_id')->nullable()->constrained('pss')->onDelete('set null');
            $table->date('service_date');
            $table->date('payment_date');
            $table->decimal('requested_amount', 14, 2);
            $table->decimal('approved_amount', 14, 2)->default(0);
            $table->decimal('rejected_amount', 14, 2)->default(0);
            $table->string('status')->default('Recibido'); // Recibido, Pendiente de documentos, Expediente completo, En revisión, Aprobado, Rechazado, Aprobado parcial, Reembolsado, Escalado a DIDA, Escalado a SISALRIL, Cerrado
            $table->dateTime('received_at')->nullable();
            $table->dateTime('completed_documents_at')->nullable();
            $table->date('response_due_date')->nullable(); // 10 días hábiles
            $table->dateTime('responded_at')->nullable();
            $table->string('written_response_path')->nullable();
            $table->text('final_decision')->nullable();
            $table->boolean('pss_debit_required')->default(false); // Si genera débito a PSS
            $table->foreignId('related_authorization_id')->nullable()->constrained('autorizaciones')->onDelete('set null');
            $table->foreignId('related_claim_id')->nullable()->constrained('authorization_claims')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 7. Documentos del Reembolso
        Schema::create('reimbursement_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reimbursement_case_id')->constrained('reimbursement_cases')->onDelete('cascade');
            $table->string('document_type'); // Factura, Recibo de pago, Indicación médica, etc.
            $table->string('file_path');
            $table->string('status')->default('Válido'); // Válido, Rechazado, Pendiente
            $table->dateTime('uploaded_at')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 8. Trazabilidad de Acciones del Reembolso
        Schema::create('reimbursement_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reimbursement_case_id')->constrained('reimbursement_cases')->onDelete('cascade');
            $table->string('action_type'); // Recepción, Aprobación, Rechazo, Escalado, Documentación Faltante
            $table->text('description');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // 9. Catálogo de Cuentas Contables
        Schema::create('chart_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->integer('account_class'); // 1 = Activo, 2 = Pasivo, 3 = Capital, 4 = Ingreso, 5 = Gasto, 6 = Orden
            $table->unsignedBigInteger('parent_id')->nullable(); // Relación recursiva para jerarquía
            $table->integer('level')->default(1);
            $table->string('account_type')->default('auxiliar'); // control / auxiliar
            $table->string('nature')->default('debito'); // debito/credito
            $table->boolean('is_postable')->default(true); // Si permite transacciones directas
            $table->boolean('is_system')->default(false); // Cuentas fijas protegidas
            $table->boolean('allows_children')->default(true);
            $table->boolean('sisalril_required')->default(false);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 10. Diarios Contables
        Schema::create('accounting_journals', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 11. Asientos de Diario Contable
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_number')->unique();
            $table->foreignId('journal_id')->constrained('accounting_journals')->onDelete('cascade');
            $table->date('entry_date');
            $table->string('period'); // YYYYMM
            $table->string('source_module'); // autorizaciones, reclamaciones, pagos, reembolsos, dispersión, promotores, unipago, manual
            $table->string('source_type')->nullable(); // Nombre del modelo fuente (ej: Reclamacion, Pago)
            $table->unsignedBigInteger('source_id')->nullable(); // ID del registro fuente
            $table->text('description');
            $table->string('status')->default('borrador'); // borrador, posteado, anulado
            $table->decimal('total_debit', 14, 2)->default(0);
            $table->decimal('total_credit', 14, 2)->default(0);
            $table->foreignId('posted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('posted_at')->nullable();
            $table->unsignedBigInteger('reversed_entry_id')->nullable(); // Por si es contrasiento
            $table->timestamps();
        });

        // 12. Líneas del Asiento
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->onDelete('cascade');
            $table->foreignId('account_id')->constrained('chart_accounts')->onDelete('cascade');
            $table->decimal('debit', 14, 2)->default(0);
            $table->decimal('credit', 14, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('third_party_type')->nullable(); // pss, promotor, afiliado, suplidor
            $table->unsignedBigInteger('third_party_id')->nullable(); // ID del tercero
            $table->string('cost_center_id')->nullable(); // Centros de costo si aplica
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // 13. Períodos Contables
        Schema::create('periodos_contables', function (Blueprint $table) {
            $table->id();
            $table->string('period_code')->unique(); // YYYYMM (ej: 202606)
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_closed')->default(false);
            $table->dateTime('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 14. Modificar tablas existentes para incorporar los nuevos campos contables y de cobertura
        Schema::table('autorizaciones', function (Blueprint $table) {
            $table->decimal('monto_ars', 14, 2)->default(0)->after('monto_contratado');
            $table->decimal('monto_afiliado', 14, 2)->default(0)->after('monto_ars');
            $table->decimal('copago', 14, 2)->default(0)->after('monto_afiliado');
            $table->decimal('exceso', 14, 2)->default(0)->after('copago');
            $table->decimal('monto_no_cubierto', 14, 2)->default(0)->after('exceso');
            $table->string('exception_coverage_type')->nullable()->after('monto_no_cubierto'); // SRL, FONAMAT, N/A
        });

        Schema::table('accounts_payable', function (Blueprint $table) {
            $table->string('account_payable_number')->nullable()->after('payable_number');
            $table->string('vendor_type')->default('PSS')->after('account_payable_number'); // PSS, promotor, afiliado, suplidor
            $table->unsignedBigInteger('vendor_id')->nullable()->after('vendor_type'); // ID del tercero
            $table->decimal('gross_amount', 14, 2)->default(0)->after('amount');
            $table->decimal('objected_amount', 14, 2)->default(0)->after('gross_amount');
            $table->decimal('approved_amount', 14, 2)->default(0)->after('objected_amount');
            $table->decimal('tax_withholding_amount', 14, 2)->default(0)->after('retained_amount');
            $table->decimal('other_deductions', 14, 2)->default(0)->after('tax_withholding_amount');
            $table->unsignedBigInteger('accounting_entry_id')->nullable()->after('status');
            $table->unsignedBigInteger('payment_entry_id')->nullable()->after('accounting_entry_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts_payable', function (Blueprint $table) {
            $table->dropColumn([
                'account_payable_number',
                'vendor_type',
                'vendor_id',
                'gross_amount',
                'objected_amount',
                'approved_amount',
                'tax_withholding_amount',
                'other_deductions',
                'accounting_entry_id',
                'payment_entry_id'
            ]);
        });

        Schema::table('autorizaciones', function (Blueprint $table) {
            $table->dropColumn([
                'monto_ars',
                'monto_afiliado',
                'copago',
                'exceso',
                'monto_no_cubierto',
                'exception_coverage_type'
            ]);
        });

        Schema::dropIfExists('periodos_contables');
        Schema::dropIfExists('journal_entry_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('accounting_journals');
        Schema::dropIfExists('chart_accounts');
        Schema::dropIfExists('reimbursement_actions');
        Schema::dropIfExists('reimbursement_documents');
        Schema::dropIfExists('reimbursement_cases');
        Schema::dropIfExists('claim_conciliations');
        Schema::dropIfExists('claim_glosses');
        Schema::dropIfExists('medical_auditors');
        Schema::dropIfExists('pdss_coverage_accumulators');
        Schema::dropIfExists('pdss_coverage_rules');
    }
};
