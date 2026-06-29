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
        // 1. Update pss table
        Schema::table('pss', function (Blueprint $table) {
            if (!Schema::hasColumn('pss', 'commercial_name')) {
                $table->string('commercial_name')->nullable()->after('nombre');
            }
            if (!Schema::hasColumn('pss', 'habilitation_number')) {
                $table->string('habilitation_number')->nullable()->after('commercial_name');
            }
            if (!Schema::hasColumn('pss', 'pss_type')) {
                $table->string('pss_type')->nullable()->after('habilitation_number');
            }
            if (!Schema::hasColumn('pss', 'pss_category')) {
                $table->string('pss_category')->nullable()->after('pss_type');
            }
            if (!Schema::hasColumn('pss', 'level_of_care')) {
                $table->integer('level_of_care')->default(1)->after('pss_category');
            }
            if (!Schema::hasColumn('pss', 'network_status')) {
                $table->boolean('network_status')->default(true)->after('level_of_care');
            }
            if (!Schema::hasColumn('pss', 'contract_status')) {
                $table->boolean('contract_status')->default(true)->after('network_status');
            }
            if (!Schema::hasColumn('pss', 'province')) {
                $table->string('province')->nullable()->after('contract_status');
            }
            if (!Schema::hasColumn('pss', 'municipality')) {
                $table->string('municipality')->nullable()->after('province');
            }
        });

        // 2. Create pss_users table if not exists
        if (!Schema::hasTable('pss_users')) {
            Schema::create('pss_users', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('pss_id');
                $table->string('role')->default('viewer');
                $table->string('access_type')->default('medical_center'); // medical_center, pharmacy, laboratory
                $table->boolean('is_default')->default(false);
                $table->string('status')->default('activo');
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('pss_id')->references('id')->on('pss')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pss_users');
        Schema::table('pss', function (Blueprint $table) {
            $table->dropColumn([
                'commercial_name',
                'habilitation_number',
                'pss_type',
                'pss_category',
                'level_of_care',
                'network_status',
                'contract_status',
                'province',
                'municipality'
            ]);
        });
    }
};
