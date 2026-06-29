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
        // 1. Rangos de Contratos
        Schema::create('affiliation_contract_ranges', function (Blueprint $table) {
            $table->id();
            $table->string('range_code')->unique();
            $table->string('description')->nullable();
            $table->integer('start_number');
            $table->integer('end_number');
            $table->integer('total_numbers');
            $table->integer('available_count')->default(0);
            $table->integer('reserved_count')->default(0);
            $table->integer('used_count')->default(0);
            $table->integer('ok_count')->default(0);
            $table->integer('pending_count')->default(0);
            $table->integer('rejected_count')->default(0);
            $table->integer('blocked_count')->default(0);
            $table->string('source')->default('manual'); // unipago/sisalril/manual/otro
            $table->string('approval_reference')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->string('status')->default('activo'); // activo, agotado, suspendido, cerrado, anulado
            $table->text('observations')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });

        // 2. Números Individuales
        Schema::create('affiliation_contract_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliation_contract_range_id')->constrained('affiliation_contract_ranges')->onDelete('cascade');
            $table->string('contract_number')->unique();
            $table->string('status')->default('disponible'); // disponible, reservado, usado, enviado_unipago, ok, pe, re, anulado, devuelto, bloqueado, saltado
            $table->integer('assigned_to_user_id')->nullable();
            $table->integer('assigned_to_promoter_id')->nullable();
            $table->integer('assigned_to_affiliate_id')->nullable();
            $table->integer('assigned_to_batch_id')->nullable();
            $table->string('reservation_token')->nullable();
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('reservation_expires_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamp('sent_to_unipago_at')->nullable();
            $table->integer('unipago_lote_id')->nullable();
            $table->string('unipago_request_id')->nullable();
            $table->string('unipago_response_status')->nullable();
            $table->string('unipago_response_code')->nullable();
            $table->text('unipago_response_message')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->string('released_by')->nullable();
            $table->text('release_reason')->nullable();
            $table->timestamp('blocked_at')->nullable();
            $table->string('blocked_by')->nullable();
            $table->text('block_reason')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->index(['status', 'contract_number']);
        });

        // 3. Movimientos Históricos
        Schema::create('affiliation_contract_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_number_id')->constrained('affiliation_contract_numbers')->onDelete('cascade');
            $table->string('movement_type');
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->integer('user_id')->nullable();
            $table->integer('affiliate_id')->nullable();
            $table->integer('batch_id')->nullable();
            $table->integer('lote_id')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        // 4. Reservas Temporales
        Schema::create('affiliation_contract_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_number_id')->constrained('affiliation_contract_numbers')->onDelete('cascade');
            $table->string('reserved_by')->nullable();
            $table->string('reservation_type')->default('individual'); // individual, carga_masiva, lote, promotor
            $table->timestamp('expires_at');
            $table->string('status')->default('activa'); // activa, consumida, liberada, expirada
            $table->timestamp('consumed_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // 5. Inyección de Campos en Afiliados
        Schema::table('afiliados', function (Blueprint $table) {
            $table->unsignedBigInteger('contract_number_id')->nullable()->after('id');
            $table->string('contract_number')->nullable()->after('contract_number_id');
            $table->unsignedBigInteger('contract_range_id')->nullable()->after('contract_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('afiliados', function (Blueprint $table) {
            $table->dropColumn(['contract_number_id', 'contract_number', 'contract_range_id']);
        });

        Schema::dropIfExists('affiliation_contract_reservations');
        Schema::dropIfExists('affiliation_contract_movements');
        Schema::dropIfExists('affiliation_contract_numbers');
        Schema::dropIfExists('affiliation_contract_ranges');
    }
};
