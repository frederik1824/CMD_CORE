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
        // --- 1. PHARMACY TABLES ---

        // pharmacy_prescriptions
        Schema::create('pharmacy_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pss_id');
            $table->unsignedBigInteger('afiliado_id');
            $table->string('prescription_number')->unique();
            $table->string('doctor_name');
            $table->string('doctor_exequatur');
            $table->string('specialty')->nullable();
            $table->string('diagnosis')->nullable();
            $table->date('prescription_date');
            $table->string('document_path')->nullable();
            $table->string('status')->default('Borrador'); // Borrador, Validada, etc.
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('pss_id')->references('id')->on('pss')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // pharmacy_dispensations
        Schema::create('pharmacy_dispensations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prescription_id')->nullable();
            $table->unsignedBigInteger('pss_id');
            $table->unsignedBigInteger('afiliado_id');
            $table->unsignedBigInteger('authorization_id')->nullable(); // linked to autorizaciones if any
            $table->string('dispensation_number')->unique();
            $table->timestamp('dispensed_at');
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->decimal('ars_amount', 12, 2)->default(0.00);
            $table->decimal('affiliate_copay_amount', 12, 2)->default(0.00);
            $table->decimal('non_covered_amount', 12, 2)->default(0.00);
            $table->string('status')->default('Dispensada'); // Borrador, Dispensada, Reclamada, etc.
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('prescription_id')->references('id')->on('pharmacy_prescriptions')->onDelete('set null');
            $table->foreign('pss_id')->references('id')->on('pss')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // pharmacy_dispensation_items
        Schema::create('pharmacy_dispensation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dispensation_id');
            $table->unsignedBigInteger('pdss_service_id')->nullable();
            $table->string('medicine_code');
            $table->string('medicine_name');
            $table->string('presentation')->nullable();
            $table->string('concentration')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0.00);
            $table->decimal('total_price', 12, 2)->default(0.00);
            $table->decimal('ars_covered_amount', 12, 2)->default(0.00);
            $table->decimal('copay_amount', 12, 2)->default(0.00);
            $table->decimal('non_covered_amount', 12, 2)->default(0.00);
            $table->boolean('requires_authorization')->default(false);
            $table->string('status')->default('Activo');
            $table->timestamps();

            $table->foreign('dispensation_id')->references('id')->on('pharmacy_dispensations')->onDelete('cascade');
        });

        // --- 2. LABORATORY TABLES ---

        // lab_orders
        Schema::create('lab_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pss_id');
            $table->unsignedBigInteger('afiliado_id');
            $table->string('order_number')->unique();
            $table->string('doctor_name');
            $table->string('doctor_exequatur');
            $table->string('specialty')->nullable();
            $table->string('diagnosis')->nullable();
            $table->date('order_date');
            $table->string('document_path')->nullable();
            $table->string('status')->default('Borrador'); // Borrador, Orden recibida, etc.
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('pss_id')->references('id')->on('pss')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // lab_order_items
        Schema::create('lab_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lab_order_id');
            $table->unsignedBigInteger('pdss_service_id')->nullable();
            $table->string('simon_code_snapshot')->nullable();
            $table->string('cups_code_snapshot')->nullable();
            $table->string('test_name');
            $table->string('coverage_type')->nullable(); // laboratorio, imagen, etc.
            $table->decimal('contracted_amount', 12, 2)->default(0.00);
            $table->decimal('requested_amount', 12, 2)->default(0.00);
            $table->decimal('authorized_amount', 12, 2)->default(0.00);
            $table->boolean('requires_authorization')->default(false);
            $table->boolean('requires_audit')->default(false);
            $table->string('status')->default('Pendiente'); // Pendiente, Realizada, etc.
            $table->timestamps();

            $table->foreign('lab_order_id')->references('id')->on('lab_orders')->onDelete('cascade');
        });

        // lab_results
        Schema::create('lab_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lab_order_id');
            $table->unsignedBigInteger('lab_order_item_id');
            $table->string('result_number')->unique();
            $table->string('result_status')->default('Resultados Disponibles');
            $table->string('result_file_path')->nullable();
            $table->date('result_date');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->foreign('lab_order_id')->references('id')->on('lab_orders')->onDelete('cascade');
            $table->foreign('lab_order_item_id')->references('id')->on('lab_order_items')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_results');
        Schema::dropIfExists('lab_order_items');
        Schema::dropIfExists('lab_orders');
        Schema::dropIfExists('pharmacy_dispensation_items');
        Schema::dropIfExists('pharmacy_dispensations');
        Schema::dropIfExists('pharmacy_prescriptions');
    }
};
