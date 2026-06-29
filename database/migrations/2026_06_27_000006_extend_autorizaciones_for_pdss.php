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
        Schema::table('autorizaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('pdss_service_id')->nullable()->after('servicio_medico_id');
            $table->string('simon_code_snapshot')->nullable()->after('pdss_service_id');
            $table->string('cups_code_snapshot')->nullable()->after('simon_code_snapshot');
            $table->string('service_description_snapshot')->nullable()->after('cups_code_snapshot');
            $table->string('coverage_type_snapshot')->nullable()->after('service_description_snapshot');
            $table->string('pdss_group_snapshot')->nullable()->after('coverage_type_snapshot');
            $table->string('pdss_subgroup_snapshot')->nullable()->after('pdss_group_snapshot');
            $table->integer('level_requested')->nullable()->after('pdss_subgroup_snapshot');
            $table->string('coverage_allowed')->nullable()->after('level_requested');
            $table->string('copay_type_snapshot')->nullable()->after('coverage_allowed');
            $table->string('amount_coverage_snapshot')->nullable()->after('copay_type_snapshot');

            $table->foreign('pdss_service_id')->references('id')->on('pdss_services')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('autorizaciones', function (Blueprint $table) {
            $table->dropForeign(['pdss_service_id']);
            $table->dropColumn([
                'pdss_service_id',
                'simon_code_snapshot',
                'cups_code_snapshot',
                'service_description_snapshot',
                'coverage_type_snapshot',
                'pdss_group_snapshot',
                'pdss_subgroup_snapshot',
                'level_requested',
                'coverage_allowed',
                'copay_type_snapshot',
                'amount_coverage_snapshot'
            ]);
        });
    }
};
