<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CV - {{ $student->first_name }} {{ $student->last_name }}</title>
    <style>
        @page {
            margin: 0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.6;
            color: #2d3748;
            background: white;
            padding: 20mm 15mm;
        }
        
        .container {
            max-width: 100%;
        }
        
        /* Header profesional con fondo sólido */
        .header {
            background: #0f172a;
            color: white;
            padding: 30px 35px;
            margin: -20mm -15mm 0 -15mm;
            width: calc(100% + 30mm);
            display: table;
        }
        
        .header-content {
            display: table-cell;
            vertical-align: middle;
            padding-right: 20px;
        }
        
        .header-photo {
            display: table-cell;
            vertical-align: middle;
            width: 100px;
            text-align: right;
        }
        
        .header-photo img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.3);
            object-fit: cover;
        }
        
        .header h1 {
            font-size: 24pt;
            margin-bottom: 4px;
            font-weight: 700;
        }
        
        .header .subtitle {
            font-size: 12pt;
            margin-bottom: 15px;
        }
        
        /* Barra de contacto */
        .contact-bar {
            background: #f1f5f9;
            padding: 15px 35px;
            border-bottom: 3px solid #0f172a;
            margin: 0 -15mm 25px -15mm;
            width: calc(100% + 30mm);
        }
        
        .contact-item {
            display: inline-block;
            margin-right: 25px;
            font-size: 9pt;
            color: #475569;
        }
        
        .contact-item strong {
            color: #1e293b;
            margin-right: 5px;
        }
        
        /* Contenido */
        .content {
            padding: 0 15px;
        }
        
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .section:not(:first-child) {
            margin-top: 25px;
        }
        
        .section-title {
            font-size: 14pt;
            font-weight: 700;
            color: #0f172a;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 5px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .subsection {
            margin-bottom: 15px;
            padding-left: 5px;
        }
        
        .subsection-title {
            font-weight: 700;
            color: #1e293b;
            font-size: 11pt;
            margin-bottom: 3px;
        }
        
        .subsection-subtitle {
            color: #64748b;
            font-size: 9pt;
            margin-bottom: 5px;
            font-style: italic;
        }
        
        .subsection-dates {
            color: #94a3b8;
            font-size: 8.5pt;
            margin-bottom: 5px;
        }
        
        .subsection-content {
            color: #475569;
            font-size: 9.5pt;
            line-height: 1.5;
            margin-top: 5px;
        }
        
        /* Info grid */
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 35%;
            padding: 3px 0;
            font-weight: 600;
            color: #475569;
            font-size: 9pt;
        }
        
        .info-value {
            display: table-cell;
            padding: 3px 0;
            color: #1e293b;
            font-size: 9pt;
        }
        
        /* Tags/Pills */
        .tags {
            margin-top: 8px;
        }
        
        .tag {
            display: inline-block;
            background: #e0e7ff;
            color: #0f172a;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 8.5pt;
            margin-right: 6px;
            margin-bottom: 6px;
            font-weight: 500;
        }
        
        .tag-list {
            margin-top: 5px;
        }
        
        .tag-list-item {
            color: #475569;
            font-size: 9pt;
            margin-bottom: 3px;
            padding-left: 12px;
            position: relative;
        }
        
        .tag-list-item:before {
            content: "•";
            position: absolute;
            left: 0;
            color: #0f172a;
            font-weight: bold;
        }
        
        .presentation-box {
            background: #f8fafc;
            border-left: 3px solid #0f172a;
            padding: 12px 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        
        .presentation-box p {
            color: #475569;
            font-size: 9.5pt;
            line-height: 1.6;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <div class="header-content">
                <h1>{{ $student->first_name }} {{ $student->last_name }}</h1>
                <div class="subtitle">
                    @if($student->cycle)
                        {{ $student->cycle }}
                    @endif
                    @if($student->center)
                        - {{ $student->center }}
                    @endif
                </div>
            </div>
            @if($student->avatar_path)
            <div class="header-photo">
                <img src="{{ storage_path('app/public/' . $student->avatar_path) }}" alt="Foto">
            </div>
            @endif
        </div>

        <!-- BARRA DE CONTACTO -->
        <div class="contact-bar">
            @if($student->email ?? $student->user->email)
                <span class="contact-item"><strong>Email:</strong> {{ $student->email ?? $student->user->email }}</span>
            @endif
            @if($student->phone)
                <span class="contact-item"><strong>Teléfono:</strong> {{ $student->phone }}</span>
            @endif
            @if($student->city && $student->province)
                <span class="contact-item"><strong>Ubicación:</strong> {{ $student->city }}, {{ $student->province }}</span>
            @endif
        </div>

        <div class="content">
            <!-- DATOS PERSONALES -->
            <div class="section">
                <div class="section-title">Datos Personales</div>
                <div class="info-grid">
                    @if($student->dni)
                    <div class="info-row">
                        <div class="info-label">DNI/NIE:</div>
                        <div class="info-value">{{ $student->dni }}</div>
                    </div>
                    @endif
                    
                    @if($student->birth_date)
                    <div class="info-row">
                        <div class="info-label">Fecha de nacimiento:</div>
                        <div class="info-value">{{ $student->birth_date->format('d/m/Y') }}</div>
                    </div>
                    @endif
                    
                    @if($student->address)
                    <div class="info-row">
                        <div class="info-label">Dirección completa:</div>
                        <div class="info-value">{{ $student->address }}</div>
                    </div>
                    @endif
                    
                    @if($student->postal_code || $student->city)
                    <div class="info-row">
                        <div class="info-label">Código postal / Ciudad:</div>
                        <div class="info-value">
                            @if($student->postal_code) {{ $student->postal_code }} @endif
                            @if($student->city) - {{ $student->city }} @endif
                        </div>
                    </div>
                    @endif
                    
                    @if($student->has_driver_license !== null)
                    <div class="info-row">
                        <div class="info-label">Carnet de conducir:</div>
                        <div class="info-value">{{ $student->has_driver_license ? 'Sí' : 'No' }}</div>
                    </div>
                    @endif
                    
                    @if($student->has_vehicle !== null)
                    <div class="info-row">
                        <div class="info-label">Vehículo propio:</div>
                        <div class="info-value">{{ $student->has_vehicle ? 'Sí' : 'No' }}</div>
                    </div>
                    @endif
                    
                    @if($student->link_linkedin)
                    <div class="info-row">
                        <div class="info-label">LinkedIn:</div>
                        <div class="info-value">{{ $student->link_linkedin }}</div>
                    </div>
                    @endif
                    
                    @if($student->link_portfolio)
                    <div class="info-row">
                        <div class="info-label">Portfolio:</div>
                        <div class="info-value">{{ $student->link_portfolio }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- PRESENTACIÓN -->
            @php
                $presentation = $student->non_formal_experience;
            @endphp
            @if($presentation)
            <div class="section">
                <div class="section-title">Presentación</div>
                <div class="presentation-box">
                    <p>{{ $presentation }}</p>
                </div>
            </div>
            @endif

            <!-- FORMACIÓN ACADÉMICA -->
            @php
                $educations = is_array($student->educations) ? $student->educations : ($student->educations ?? collect());
            @endphp
            @if($student->cycle || $student->center || count($educations) > 0)
            <div class="section">
                <div class="section-title">Formación Académica</div>
                
                @if($student->cycle || $student->center)
                <div class="subsection">
                    <div class="subsection-title">{{ $student->cycle ?? 'Formación Profesional' }}</div>
                    @if($student->center)
                        <div class="subsection-subtitle">{{ $student->center }}</div>
                    @endif
                    @if($student->year_start || $student->year_end)
                        <div class="subsection-dates">
                            @if($student->year_start) {{ $student->year_start }} @endif
                            @if($student->year_end) - {{ $student->year_end }} @endif
                        </div>
                    @endif
                    @if($student->fp_modality)
                        <div class="subsection-content">Modalidad: {{ ucfirst($student->fp_modality) }}</div>
                    @endif
                </div>
                @endif

                @foreach($educations as $edu)
                <div class="subsection">
                    <div class="subsection-title">{{ $edu->title ?? 'Formación' }}</div>
                    @if($edu->center)
                        <div class="subsection-subtitle">{{ $edu->center }}</div>
                    @endif
                    @if($edu->start_date || $edu->end_date)
                        <div class="subsection-dates">
                            {{ $edu->start_date ? $edu->start_date->format('m/Y') : '' }}
                            @if($edu->end_date)
                                - {{ $edu->end_date->format('m/Y') }}
                            @else
                                - Actualidad
                            @endif
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            <!-- EXPERIENCIA PROFESIONAL -->
            @php
                $experiences = is_array($student->experiences) ? $student->experiences : ($student->experiences ?? collect());
            @endphp
            @if(count($experiences) > 0)
            <div class="section">
                <div class="section-title">Experiencia Profesional</div>
                
                @foreach($experiences as $exp)
                <div class="subsection">
                    <div class="subsection-title">{{ $exp->position ?? 'Puesto' }}</div>
                    @if($exp->company)
                        <div class="subsection-subtitle">{{ $exp->company }}</div>
                    @endif
                    @if($exp->start_date || $exp->end_date)
                        <div class="subsection-dates">
                            {{ $exp->start_date ? $exp->start_date->format('m/Y') : '' }}
                            @if($exp->end_date)
                                - {{ $exp->end_date->format('m/Y') }}
                            @else
                                - Actualidad
                            @endif
                        </div>
                    @endif
                    @if($exp->functions)
                        <div class="subsection-content">{{ $exp->functions }}</div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            <!-- COMPETENCIAS TÉCNICAS Y PERSONALES -->
            <div class="section">
                <div class="section-title">Competencias Técnicas y Personales</div>
                
                <!-- Herramientas y Software -->
                @php
                    $tech_competencies = is_array($student->tech_competencies) ? $student->tech_competencies : (is_string($student->tech_competencies) ? json_decode($student->tech_competencies, true) : []);
                @endphp
                @if($tech_competencies && count($tech_competencies) > 0)
                <div class="subsection">
                    <div class="subsection-title">Herramientas y Software</div>
                    <div class="tags">
                        @foreach($tech_competencies as $tech)
                            <span class="tag">{{ $tech }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Idiomas -->
                @php
                    $languages = is_array($student->languages) ? $student->languages : (is_string($student->languages) ? json_decode($student->languages, true) : []);
                @endphp
                @if($languages && count($languages) > 0)
                <div class="subsection">
                    <div class="subsection-title">Idiomas</div>
                    <div class="tag-list">
                        @foreach($languages as $lang)
                            <div class="tag-list-item">
                                {{ is_array($lang) ? ($lang['language'] ?? $lang['name'] ?? $lang) : $lang }}
                                @if(is_array($lang) && isset($lang['level']))
                                    ({{ $lang['level'] }})
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Certificaciones -->
                @php
                    $certifications = is_array($student->certifications) ? $student->certifications : (is_string($student->certifications) ? json_decode($student->certifications, true) : []);
                @endphp
                @if($certifications && count($certifications) > 0)
                <div class="subsection">
                    <div class="subsection-title">Certificaciones y Formaciones Complementarias</div>
                    <div class="tag-list">
                        @foreach($certifications as $cert)
                            <div class="tag-list-item">{{ is_array($cert) ? ($cert['name'] ?? $cert['title'] ?? $cert) : $cert }}</div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Habilidades Personales (Soft Skills) -->
                @php
                    $soft_skills = is_array($student->soft_skills) ? $student->soft_skills : (is_string($student->soft_skills) ? json_decode($student->soft_skills, true) : []);
                @endphp
                @if($soft_skills && count($soft_skills) > 0)
                <div class="subsection">
                    <div class="subsection-title">Habilidades Personales (Soft Skills)</div>
                    <div class="tags">
                        @foreach($soft_skills as $skill)
                            <span class="tag">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- DISPONIBILIDAD -->
            @if($student->availability_slot || $student->work_modality || $student->relocate !== null)
            <div class="section">
                <div class="section-title">Disponibilidad</div>
                <div class="info-grid">
                    @if($student->availability_slot)
                    <div class="info-row">
                        <div class="info-label">Jornada:</div>
                        <div class="info-value">{{ ucfirst($student->availability_slot) }}</div>
                    </div>
                    @endif
                    
                    @if($student->work_modality)
                    <div class="info-row">
                        <div class="info-label">Modalidad de trabajo:</div>
                        <div class="info-value">{{ ucfirst($student->work_modality) }}</div>
                    </div>
                    @endif
                    
                    @if($student->available_from)
                    <div class="info-row">
                        <div class="info-label">Disponible desde:</div>
                        <div class="info-value">{{ $student->available_from->format('d/m/Y') }}</div>
                    </div>
                    @endif
                    
                    @if($student->relocate !== null)
                    <div class="info-row">
                        <div class="info-label">Disposición para relocalizarse:</div>
                        <div class="info-value">{{ $student->relocate ? 'Sí' : 'No' }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- TIPOS DE EMPRESAS DE INTERÉS -->
            @if($student->preferred_companies)
            <div class="section">
                <div class="section-title">Tipos de Empresas de Interés</div>
                <div class="presentation-box">
                    <p>{{ $student->preferred_companies }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
