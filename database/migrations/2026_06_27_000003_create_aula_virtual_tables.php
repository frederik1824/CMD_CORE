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
        // 1. virtual_courses
        Schema::create('virtual_courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->integer('hours');
            $table->string('image')->nullable();
            $table->string('status')->default('Activo'); // Activo, Inactivo
            $table->timestamps();
        });

        // 2. virtual_lessons
        Schema::create('virtual_lessons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->string('title');
            $table->integer('duration_minutes')->default(15);
            $table->text('content')->nullable();
            $table->integer('order_index')->default(0);
            $table->string('status')->default('Activo');
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('virtual_courses')->onDelete('cascade');
        });

        // 3. virtual_enrollments
        Schema::create('virtual_enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_id');
            $table->string('status')->default('En Curso'); // En Curso, Completado, Cancelado
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('virtual_courses')->onDelete('cascade');
            $table->unique(['user_id', 'course_id']);
        });

        // 4. virtual_progress
        Schema::create('virtual_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('lesson_id');
            $table->boolean('completed')->default(true);
            $table->timestamp('completed_at')->useCurrent();
            $table->timestamps();

            $table->foreign('enrollment_id')->references('id')->on('virtual_enrollments')->onDelete('cascade');
            $table->foreign('lesson_id')->references('id')->on('virtual_lessons')->onDelete('cascade');
            $table->unique(['enrollment_id', 'lesson_id']);
        });

        // 5. virtual_materials
        Schema::create('virtual_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_id');
            $table->string('name');
            $table->string('file_path');
            $table->string('file_type')->default('pdf');
            $table->integer('size_bytes')->default(0);
            $table->timestamps();

            $table->foreign('lesson_id')->references('id')->on('virtual_lessons')->onDelete('cascade');
        });

        // 6. virtual_assessments
        Schema::create('virtual_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->string('title');
            $table->text('questions_json'); // JSON string for questions
            $table->integer('min_score')->default(70);
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('virtual_courses')->onDelete('cascade');
        });

        // 7. virtual_certificates
        Schema::create('virtual_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_id');
            $table->string('certificate_code')->unique();
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('virtual_courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_certificates');
        Schema::dropIfExists('virtual_assessments');
        Schema::dropIfExists('virtual_materials');
        Schema::dropIfExists('virtual_progress');
        Schema::dropIfExists('virtual_enrollments');
        Schema::dropIfExists('virtual_lessons');
        Schema::dropIfExists('virtual_courses');
    }
};
