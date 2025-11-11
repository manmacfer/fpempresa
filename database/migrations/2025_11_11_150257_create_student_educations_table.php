<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
public function up(): void {
if (!Schema::hasTable('student_educations')) {
Schema::create('student_educations', function (Blueprint $table) {
$table->id();
$table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
$table->string('title', 190);
$table->string('center', 190)->nullable();
$table->date('start_date')->nullable();
$table->date('end_date')->nullable();
$table->timestamps();
});
}
}
public function down(): void {
Schema::dropIfExists('student_educations');
}
};