<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentCvController extends Controller
{
    /**
     * Generar y descargar el CV del estudiante en PDF
     */
    public function download(Student $student)
    {
        // Cargar relaciones necesarias
        $student->load(['user', 'educations', 'experiences', 'languages']);

        // Generar el PDF con la vista
        $pdf = Pdf::loadView('pdf.student-cv', [
            'student' => $student
        ]);

        // Configurar tamaño y orientación
        $pdf->setPaper('A4', 'portrait');

        // Nombre del archivo
        $fileName = 'CV_' . str_replace(' ', '_', $student->first_name . '_' . $student->last_name) . '.pdf';

        // Descargar
        return $pdf->download($fileName);
    }

    /**
     * Visualizar el CV en el navegador (opcional)
     */
    public function preview(Student $student)
    {
        $student->load(['user', 'educations', 'experiences', 'languages']);

        $pdf = Pdf::loadView('pdf.student-cv', [
            'student' => $student
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream();
    }
}
