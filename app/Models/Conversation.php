<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['matching_id'];

    public function matching()
    {
        return $this->belongsTo(Matching::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Obtener los participantes de la conversación
     * (alumno, empresa, profesor)
     */
    public function getParticipants()
    {
        $matching = $this->matching;
        if (!$matching) return collect();

        $participants = collect();

        // Alumno
        if ($matching->student && $matching->student->user) {
            $participants->push($matching->student->user);
        }

        // Empresa
        if ($matching->company && $matching->company->user) {
            $participants->push($matching->company->user);
        }

        // Profesores (del aula del alumno)
        if ($matching->student && $matching->student->classroom && $matching->student->classroom->teachers) {
            foreach ($matching->student->classroom->teachers as $teacher) {
                if ($teacher->user) {
                    $participants->push($teacher->user);
                }
            }
        }

        return $participants;
    }
}
