<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Vacancy extends Model
{
    use HasFactory;

    /** Tabla (por claridad) */
    protected $table = 'vacancies';

    /** Asignación masiva */
    protected $fillable = [
        'company_id',
        'title',
        'description',
        'ciclo_formativo_req',
        'modalidad',              // PRESENCIAL | HIBRIDA | REMOTA
        'ubicacion',
        'horarios_disponibles',   // JSON (array)
        'requiere_carnet',        // bool
        'permite_teletrabajo',    // bool
        'estado_vacante',         // ABIERTA | SELECCION | CERRADA
    ];

    /** Valores por defecto a nivel de modelo (además del DEFAULT en DB) */
    protected $attributes = [
        'modalidad'      => 'PRESENCIAL',
        'estado_vacante' => 'ABIERTA',
    ];

    /** Conversiones de tipo */
    protected $casts = [
        'horarios_disponibles' => 'array',
        'requiere_carnet'      => 'boolean',
        'permite_teletrabajo'  => 'boolean',
        // created_at / updated_at se castean automáticamente a datetime por Eloquent
    ];

    /* =========================================================
     | Relaciones
     |=========================================================*/

    /** Empresa dueña de la vacante */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /** Candidaturas a esta vacante */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Estudiantes que han aplicado.
     * Asumo que la tabla applications tiene columnas: vacancy_id, student_id
     * Si usas user_id en su lugar, cambia 'student_id' por 'user_id' y el modelo por User::class
     */
    public function applicants(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'applications', 'vacancy_id', 'student_id')
                    ->withTimestamps();
    }

    /**
     * Competencias requeridas por la vacante (Many-to-Many).
     * Asumo tabla pivote 'competency_vacancy' con columnas: competency_id, vacancy_id.
     * Si tu migración creó otro nombre (p.ej. 'competency_vacancies' o 'competency_vacancy_pivot'),
     * ajusta el 2º parámetro.
     */
    public function competencies(): BelongsToMany
    {
        return $this->belongsToMany(Competency::class, 'competency_vacancy', 'vacancy_id', 'competency_id')
                    ->withTimestamps();
    }

    /**
     * Idiomas requeridos (Many-to-Many).
     * Asumo pivote 'language_vacancy' con: language_id, vacancy_id.
     * Ajusta si tu migración usó otro nombre.
     */
    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'language_vacancy', 'vacancy_id', 'language_id')
                    ->withTimestamps();
    }

    /* =========================================================
     | Scopes de ayuda para filtrar
     |=========================================================*/

    public function scopeAbiertas($query)
    {
        return $query->where('estado_vacante', 'ABIERTA');
    }

    public function scopeEnSeleccion($query)
    {
        return $query->where('estado_vacante', 'SELECCION');
    }

    public function scopeCerradas($query)
    {
        return $query->where('estado_vacante', 'CERRADA');
    }

    public function scopeModalidad($query, string $modalidad)
    {
        $modalidad = strtoupper(trim($modalidad));
        return $query->whereIn('modalidad', ['PRESENCIAL', 'HIBRIDA', 'REMOTA'])
                     ->where('modalidad', $modalidad);
    }

    public function scopeCiudad($query, string $ciudad)
    {
        return $query->where('ubicacion', 'LIKE', '%'.trim($ciudad).'%');
    }

    /** Guardamos modalidad en MAYÚSCULAS y validada a los 3 valores */
    protected function modalidad(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                $v = strtoupper(trim((string)$value));
                if (!in_array($v, ['PRESENCIAL', 'HIBRIDA', 'REMOTA'], true)) {
                    $v = 'PRESENCIAL';
                }
                return $v;
            }
        );
    }

    /** Guardamos estado_vacante en MAYÚSCULAS y validado */
    protected function estadoVacante(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                $v = strtoupper(trim((string)$value));
                if (!in_array($v, ['ABIERTA', 'SELECCION', 'CERRADA'], true)) {
                    $v = 'ABIERTA';
                }
                return $v;
            }
        );
    }

    /** Atajo booleano: ¿es remota? */
    public function getEsRemotaAttribute(): bool
    {
        return $this->modalidad === 'REMOTA' || ($this->permite_teletrabajo === true);
    }

    /** Atajo: horarios como lista bonita "mañana, tarde" */
    public function getHorariosLabelAttribute(): string
    {
        $arr = $this->horarios_disponibles ?? [];
        return is_array($arr) ? implode(', ', $arr) : (string)$arr;
    }
}
