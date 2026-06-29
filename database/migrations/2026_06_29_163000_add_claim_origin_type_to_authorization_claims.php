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
        Schema::table('authorization_claims', function (Blueprint $table) {
            if (!Schema::hasColumn('authorization_claims', 'claim_origin_type')) {
                $table->string('claim_origin_type')->default('medical_center')->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('authorization_claims', function (Blueprint $table) {
            $table->dropColumn('claim_origin_type');
        });
    }
};
