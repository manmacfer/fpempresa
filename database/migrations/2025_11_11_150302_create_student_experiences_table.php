<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
public function up(): void {
if (!Schema::hasTable('student_experiences')) {
Schema::create('student_experiences', function (Blueprint $table) {
$table->id();
$table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
$table->string('company', 190);
$table->string('position', 190)->nullable();
$table->date('start_date')->nullable();
$table->date('end_date')->nullable();
$table->text('functions')->nullable();
$table->boolean('is_non_formal')->default(false);
$table->timestamps();
});
}
}
public function down(): void {
Schema::dropIfExists('student_experiences');
}
};