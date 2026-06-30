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
        // ==========================================
        // 1. MÓDULO PLANES DE SALUD Y COBERTURAS
        // ==========================================

        if (!Schema::hasTable('health_plans')) {
            Schema::create('health_plans', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->string('plan_type'); // pdss, complementario, voluntario, alternativo, pensionado
                $table->text('description')->nullable();
                $table->string('status')->default('Activo'); // Activo, Inactivo
                $table->date('effective_from')->nullable();
                $table->date('effective_to')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('health_plan_coverages')) {
            Schema::create('health_plan_coverages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('health_plan_id')->constrained('health_plans')->onDelete('cascade');
                $table->unsignedBigInteger('pdss_service_id')->nullable(); // Relación opcional a pdss_services
                $table->string('service_code')->nullable(); // Opcional para mapeo rápido
                $table->decimal('coverage_percent', 5, 2)->default(80.00);
                $table->decimal('copay_percent', 5, 2)->default(20.00);
                $table->decimal('fixed_copay', 12, 2)->default(0.00);
                $table->decimal('limit_amount', 12, 2)->default(0.00);
                $table->string('limit_period')->default('anual'); // anual, evento, diario
                $table->integer('waiting_period_days')->default(0);
                $table->boolean('requires_authorization')->default(true);
                $table->boolean('requires_audit')->default(false);
                $table->string('status')->default('Activo');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('coverage_derivation_rules')) {
            Schema::create('coverage_derivation_rules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('health_plan_id')->constrained('health_plans')->onDelete('cascade');
                $table->string('derivation_type'); // grupo_afiliado, tipo_riesgo, diagnostico, prestador, origen, subgrupo, rango_precio
                $table->json('condition_json'); // Reglas y filtros
                $table->json('result_json'); // Modificaciones resultantes en la cobertura
                $table->integer('priority')->default(1);
                $table->string('status')->default('Activo');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('coverage_limits')) {
            Schema::create('coverage_limits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('health_plan_id')->constrained('health_plans')->onDelete('cascade');
                $table->string('service_group')->nullable();
                $table->string('origin')->nullable();
                $table->unsignedBigInteger('affiliate_id')->nullable(); // Opcional por afiliado
                $table->string('limit_type'); // individual, familiar, origen, servicio
                $table->decimal('amount', 12, 2);
                $table->string('period')->default('anual');
                $table->string('status')->default('Activo');
                $table->timestamps();
            });
        }

        // ==========================================
        // 2. MÓDULO PyP (PROMOCIÓN Y PREVENCIÓN)
        // ==========================================

        if (!Schema::hasTable('pyp_risk_groups')) {
            Schema::create('pyp_risk_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->text('criteria')->nullable();
                $table->string('status')->default('Activo');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('pyp_risk_factors')) {
            Schema::create('pyp_risk_factors', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('status')->default('Activo');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('pyp_programs')) {
            Schema::create('pyp_programs', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('program_type'); // prenatal, hipertension, diabetes, etc.
                $table->text('description')->nullable();
                $table->string('target_population')->nullable();
                $table->foreignId('risk_group_id')->nullable()->constrained('pyp_risk_groups')->onDelete('set null');
                $table->string('status')->default('Activo');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('pyp_program_candidates')) {
            Schema::create('pyp_program_candidates', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('affiliate_id');
                $table->foreignId('program_id')->constrained('pyp_programs')->onDelete('cascade');
                $table->foreignId('risk_group_id')->nullable()->constrained('pyp_risk_groups')->onDelete('set null');
                $table->string('source')->default('diagnostico'); // diagnostico, edad, provincia, familiar
                $table->string('status')->default('Detectado'); // Detectado, Invitado, No Aceptado, Enrolado
                $table->string('reason_not_enrolled')->nullable();
                $table->timestamp('detected_at')->useCurrent();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('pyp_program_enrollments')) {
            Schema::create('pyp_program_enrollments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('affiliate_id');
                $table->foreignId('program_id')->constrained('pyp_programs')->onDelete('cascade');
                $table->date('enrollment_date');
                $table->string('status')->default('Activo'); // Activo, Completado, Cancelado
                $table->string('cancellation_reason')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('pyp_program_calendar')) {
            Schema::create('pyp_program_calendar', function (Blueprint $table) {
                $table->id();
                $table->foreignId('program_id')->constrained('pyp_programs')->onDelete('cascade');
                $table->string('service_name');
                $table->date('scheduled_date');
                $table->string('location')->nullable();
                $table->integer('capacity')->default(0);
                $table->string('status')->default('Programado'); // Programado, Realizado, Cancelado
                $table->timestamps();
            });
        }

        // ==========================================
        // 3. MÓDULO DE PRESTADORES (EXTENSIONES)
        // ==========================================

        Schema::table('pss', function (Blueprint $table) {
            if (!Schema::hasColumn('pss', 'pss_nature')) {
                $table->string('pss_nature')->default('Jurídica')->after('tipo_entidad'); // Física, Jurídica
            }
        });

        if (!Schema::hasTable('provider_groups')) {
            Schema::create('provider_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('status')->default('Activo');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('provider_networks')) {
            Schema::create('provider_networks', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('status')->default('Activo');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('provider_network_plan')) {
            Schema::create('provider_network_plan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('provider_network_id')->constrained('provider_networks')->onDelete('cascade');
                $table->foreignId('health_plan_id')->constrained('health_plans')->onDelete('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('provider_contracted_services')) {
            Schema::create('provider_contracted_services', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pss_id')->constrained('pss')->onDelete('cascade');
                $table->unsignedBigInteger('servicio_medico_id');
                $table->string('status')->default('Activo');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('provider_price_agreements')) {
            Schema::create('provider_price_agreements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pss_id')->constrained('pss')->onDelete('cascade');
                $table->foreignId('health_plan_id')->constrained('health_plans')->onDelete('cascade');
                $table->unsignedBigInteger('servicio_medico_id');
                $table->decimal('price', 12, 2);
                $table->string('status')->default('Activo');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('provider_geo_locations')) {
            Schema::create('provider_geo_locations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pss_id')->constrained('pss')->onDelete('cascade');
                $table->string('province');
                $table->string('municipality');
                $table->string('sector');
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->text('address_details')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('capitated_service_contracts')) {
            Schema::create('capitated_service_contracts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pss_id')->constrained('pss')->onDelete('cascade');
                $table->string('contract_number')->unique();
                $table->integer('coverage_population_count')->default(0);
                $table->decimal('monthly_capitation_rate', 12, 2);
                $table->decimal('total_monthly_amount', 12, 2);
                $table->string('status')->default('Activo');
                $table->date('start_date');
                $table->date('end_date');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('capitated_service_payments')) {
            Schema::create('capitated_service_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('capitated_contract_id')->constrained('capitated_service_contracts')->onDelete('cascade');
                $table->string('period'); // YYYYMM
                $table->decimal('amount_paid', 12, 2);
                $table->date('paid_at');
                $table->string('payment_reference')->nullable();
                $table->string('status')->default('Pagado');
                $table->timestamps();
            });
        }

        // ==========================================
        // 4. MÓDULO DE AFILIACIONES (EXTENSIONES)
        // ==========================================

        if (!Schema::hasTable('affiliate_groups')) {
            Schema::create('affiliate_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('rnc')->nullable();
                $table->text('description')->nullable();
                $table->string('status')->default('Activo');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('affiliate_contracts')) {
            Schema::create('affiliate_contracts', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->string('contract_type'); // colectivo, individual, voluntario
                $table->text('description')->nullable();
                $table->string('status')->default('Activo');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('business_units')) {
            Schema::create('business_units', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('status')->default('Activo');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('geographic_codes')) {
            Schema::create('geographic_codes', function (Blueprint $table) {
                $table->id();
                $table->string('region');
                $table->string('province');
                $table->string('municipality');
                $table->string('sector');
                $table->string('status')->default('Activo');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('affiliate_transactions')) {
            Schema::create('affiliate_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('affiliate_id');
                $table->string('affiliate_type'); // titular, dependiente
                $table->string('transaction_type'); // creacion, modificacion, traspaso, cancelacion
                $table->string('concept');
                $table->json('payload_before')->nullable();
                $table->json('payload_after')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });
        }

        // ==========================================
        // 5. MÓDULO DE CARNETIZACIÓN
        // ==========================================

        if (!Schema::hasTable('printing_centers')) {
            Schema::create('printing_centers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('location')->nullable();
                $table->string('contact_person')->nullable();
                $table->string('status')->default('Activo');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('printing_supplies')) {
            Schema::create('printing_supplies', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('supply_family'); // plastico, ribbon_color, ribbon_negro
                $table->integer('initial_stock')->default(0);
                $table->integer('current_stock')->default(0);
                $table->string('unit')->default('Unidad');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('printing_supply_movements')) {
            Schema::create('printing_supply_movements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('supply_id')->constrained('printing_supplies')->onDelete('cascade');
                $table->foreignId('printing_center_id')->nullable()->constrained('printing_centers')->onDelete('set null');
                $table->string('movement_type'); // entrada, salida, ajuste, transferencia, merma
                $table->integer('quantity');
                $table->string('reason')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('carnet_requests')) {
            Schema::create('carnet_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('affiliate_id');
                $table->string('affiliate_type'); // titular, dependiente
                $table->string('request_type')->default('Nuevo'); // Nuevo, Reposición, Extravío, Deterioro
                $table->foreignId('printing_center_id')->nullable()->constrained('printing_centers')->onDelete('set null');
                $table->date('request_date');
                $table->date('print_date')->nullable();
                $table->string('batch_number')->nullable();
                $table->string('status')->default('Solicitado'); // Solicitado, Impreso, En ruta, Recibido, Entregado, Anulado
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('carnet_deliveries')) {
            Schema::create('carnet_deliveries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('carnet_request_id')->constrained('carnet_requests')->onDelete('cascade');
                $table->string('recipient_name');
                $table->date('delivery_date');
                $table->string('delivery_location')->nullable();
                $table->string('signature_path')->nullable();
                $table->string('status')->default('Entregado');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('carnet_transfers')) {
            Schema::create('carnet_transfers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('carnet_request_id')->constrained('carnet_requests')->onDelete('cascade');
                $table->string('origin_location');
                $table->string('destination_location');
                $table->date('sent_date');
                $table->date('received_date')->nullable();
                $table->string('status')->default('En tránsito'); // En tránsito, Recibido, Devuelto
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('carnet_adjustments')) {
            Schema::create('carnet_adjustments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('supply_id')->constrained('printing_supplies')->onDelete('cascade');
                $table->foreignId('printing_center_id')->constrained('printing_centers')->onDelete('cascade');
                $table->string('adjustment_type'); // Merma, Conteo físico, Devolución
                $table->integer('quantity');
                $table->string('reason');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });
        }

        // ==========================================
        // 6. MÓDULO DE PROMOTORES
        // ==========================================

        if (!Schema::hasTable('promoters')) {
            Schema::create('promoters', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('promoter_type'); // persona_fisica, empresa
                $table->string('identification_number')->unique(); // Cédula o RNC
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('address')->nullable();
                $table->string('status')->default('Activo');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('promoter_contracts')) {
            Schema::create('promoter_contracts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('promoter_id')->constrained('promoters')->onDelete('cascade');
                $table->string('contract_number')->unique();
                $table->date('start_date');
                $table->date('end_date');
                $table->decimal('commission_percent', 5, 2)->default(5.00);
                $table->string('status')->default('Activo');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('promoter_campaigns')) {
            Schema::create('promoter_campaigns', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->date('start_date');
                $table->date('end_date');
                $table->decimal('commission_amount', 12, 2)->default(0.00); // Monto fijo por afiliado
                $table->string('status')->default('Activa');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('promoter_commissions')) {
            Schema::create('promoter_commissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('promoter_id')->constrained('promoters')->onDelete('cascade');
                $table->foreignId('campaign_id')->nullable()->constrained('promoter_campaigns')->onDelete('set null');
                $table->unsignedBigInteger('affiliate_id');
                $table->decimal('amount', 12, 2);
                $table->string('payout_period'); // YYYYMM
                $table->string('status')->default('Calculada'); // Calculada, Aprobada, En CXP, Pagada, Anulada
                $table->date('payment_date')->nullable();
                $table->timestamps();
            });
        }

        // ==========================================
        // 7. MÓDULO DE FACTURACIÓN Y SERVICIO AL CLIENTE
        // ==========================================

        if (!Schema::hasTable('billing_invoices')) {
            Schema::create('billing_invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number')->unique();
                $table->foreignId('health_plan_id')->nullable()->constrained('health_plans')->onDelete('set null');
                $table->unsignedBigInteger('affiliate_group_id')->nullable(); // Relación a grupos
                $table->decimal('amount', 12, 2);
                $table->string('ncf')->nullable();
                $table->string('status')->default('Emitida'); // Emitida, Pagada, Vencida, Anulada
                $table->date('issued_at');
                $table->date('due_date');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('customer_cases')) {
            Schema::create('customer_cases', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('affiliate_id');
                $table->string('case_type'); // solicitud, queja, reclamacion, sugerencia
                $table->text('description');
                $table->string('status')->default('Abierto'); // Abierto, En proceso, Pendiente, Resuelto, Cerrado
                $table->string('priority')->default('Media'); // Alta, Media, Baja
                $table->integer('sla_hours')->default(72);
                $table->timestamp('resolved_at')->nullable();
                $table->text('resolution_details')->nullable();
                $table->timestamps();
            });
        }

        // ==========================================
        // 8. CONTROL DE LOTES Y RADICACIONES
        // ==========================================

        Schema::table('authorization_claims', function (Blueprint $table) {
            if (!Schema::hasColumn('authorization_claims', 'batch_id')) {
                $table->unsignedBigInteger('batch_id')->nullable()->after('pss_id'); // Agrupación en lotes de reclamaciones
            }
            if (!Schema::hasColumn('authorization_claims', 'ncf_corrected_by')) {
                $table->unsignedBigInteger('ncf_corrected_by')->nullable()->after('ncf');
                $table->string('ncf_correction_reason')->nullable()->after('ncf_corrected_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('authorization_claims', function (Blueprint $table) {
            $table->dropColumn(['batch_id', 'ncf_corrected_by', 'ncf_correction_reason']);
        });

        Schema::dropIfExists('customer_cases');
        Schema::dropIfExists('billing_invoices');
        Schema::dropIfExists('promoter_commissions');
        Schema::dropIfExists('promoter_campaigns');
        Schema::dropIfExists('promoter_contracts');
        Schema::dropIfExists('promoters');
        Schema::dropIfExists('carnet_adjustments');
        Schema::dropIfExists('carnet_transfers');
        Schema::dropIfExists('carnet_deliveries');
        Schema::dropIfExists('carnet_requests');
        Schema::dropIfExists('printing_supply_movements');
        Schema::dropIfExists('printing_supplies');
        Schema::dropIfExists('printing_centers');
        Schema::dropIfExists('affiliate_transactions');
        Schema::dropIfExists('geographic_codes');
        Schema::dropIfExists('business_units');
        Schema::dropIfExists('affiliate_contracts');
        Schema::dropIfExists('affiliate_groups');
        Schema::dropIfExists('capitated_service_payments');
        Schema::dropIfExists('capitated_service_contracts');
        Schema::dropIfExists('provider_geo_locations');
        Schema::dropIfExists('provider_price_agreements');
        Schema::dropIfExists('provider_contracted_services');
        Schema::dropIfExists('provider_network_plan');
        Schema::dropIfExists('provider_networks');
        Schema::dropIfExists('provider_groups');

        Schema::table('pss', function (Blueprint $table) {
            $table->dropColumn('pss_nature');
        });

        Schema::dropIfExists('pyp_program_calendar');
        Schema::dropIfExists('pyp_program_enrollments');
        Schema::dropIfExists('pyp_program_candidates');
        Schema::dropIfExists('pyp_programs');
        Schema::dropIfExists('pyp_risk_factors');
        Schema::dropIfExists('pyp_risk_groups');
        Schema::dropIfExists('coverage_limits');
        Schema::dropIfExists('coverage_derivation_rules');
        Schema::dropIfExists('health_plan_coverages');
        Schema::dropIfExists('health_plans');
    }
};
