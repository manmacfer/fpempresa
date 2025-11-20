# Implementación API de Municipios y Provincias

## Resumen de Implementación

Se ha completado la integración de un sistema de gestión de ubicaciones (municipios y provincias) para mejorar la compatibilidad entre estudiantes y vacantes.

## Archivos Creados/Modificados

### Modelos

#### `app/Models/Municipality.php`
- Modelo para gestionar municipios españoles con códigos INE
- Método `search()` para búsquedas por nombre de municipio o provincia
- Estructura: code (INE), name, province_code, province_name

#### `app/Models/Province.php`
- Modelo auxiliar para gestionar provincias (calculadas desde municipalities)
- Métodos:
  - `all()`: Lista todas las provincias únicas
  - `search()`: Búsqueda de provincias por nombre
  - `municipalities()`: Obtiene municipios de una provincia
  - `isSameProvince()`: Compara dos provincias
  - `normalize()`: Normaliza nombres de provincias para comparación

### Controladores

#### `app/Http/Controllers/MunicipalityController.php`
Endpoints API REST:
- `GET /api/municipalities/search?q={query}&limit={n}` - Buscar municipios
- `GET /api/municipalities/by-province/{code}` - Municipios por provincia
- `GET /api/provinces` - Lista de todas las provincias
- `GET /api/provinces/search?q={query}` - Buscar provincias

### Servicios

#### `app/Services/CompatibilityService.php` - Actualizado
Mejoras en el scoring de ubicación (peso: 10% del total):

**Reglas de compatibilidad:**
- **1.0 puntos (100%)**: 
  - Mismo municipio Y misma provincia
  - O modalidad Remota/Teletrabajo (ubicación irrelevante)
- **0.5 puntos (50%)**: 
  - Solo coincide la provincia (diferente municipio)
  - Y la vacante es Presencial o Híbrida
- **0.0 puntos (Incompatible)**:
  - No coincide la provincia y es Presencial o Híbrida
  - O falta información de ubicación de estudiante/vacante

### Componentes Vue

#### `resources/js/Components/MunicipalitySearch.vue` - Actualizado
- Autocomplete con búsqueda dinámica (debounce 300ms)
- Integrado con la API `/api/municipalities/search`
- Muestra municipio y provincia en los resultados
- Navegación por teclado (↑/↓/Enter/Esc)
- Estados: cargando, sin resultados, error

#### `resources/js/Components/MunicipalityAutocomplete.vue` - Creado
- Componente alternativo simplificado
- Misma funcionalidad pero con diseño más limpio

#### `resources/js/Components/ProvinceAutocomplete.vue` - Creado
- Autocomplete específico para provincias
- Carga inicial de todas las provincias
- Filtrado local para mejor rendimiento

### Formularios Actualizados

#### `resources/js/Pages/Students/Edit.vue`
- Ya usaba `MunicipalitySearch`
- Actualizado para usar nueva API

#### `resources/js/Pages/Companies/Edit.vue`
- Integrado `MunicipalitySearch`
- Reemplaza inputs manuales de ciudad y provincia

#### `resources/js/Pages/Vacancies/Create.vue`
- Integrado `MunicipalitySearch`
- Permite selección precisa de ubicación

### Database

#### Migración: `2025_11_20_124256_create_municipalities_table.php`
Estructura de tabla:
```sql
- id (bigint, autoincrement)
- code (string, unique) - Código INE
- name (string) - Nombre del municipio
- province_code (string, index) - Código de provincia (2 dígitos)
- province_name (string, index) - Nombre de la provincia
- created_at, updated_at
- Índice compuesto: (name, province_name)
```

#### Seeder: `database/seeders/MunicipalitySeeder.php`
- Puebla 47 municipios principales de España
- Incluye ciudades importantes de: Madrid, Barcelona, Valencia, Sevilla, Málaga, Zaragoza, Alicante, Vizcaya, A Coruña
- **NOTA**: Para producción, importar el listado completo del INE

### Rutas API

```php
// routes/api.php
GET /api/municipalities/search?q={query}&limit={n}
GET /api/municipalities/by-province/{provinceCode}
GET /api/provinces
GET /api/provinces/search?q={query}
```

## Mejoras en Compatibilidad

### Sistema de Scoring de Ubicación

La ubicación representa el **10%** del score total de compatibilidad.

#### Reglas implementadas:

1. **Compatible 100% (1.0 puntos)**
   - Provincia Y municipio coinciden exactamente
   - O la vacante es modalidad Remota/Teletrabajo

2. **Compatible 50% (0.5 puntos)**
   - Solo coincide la provincia (diferente municipio)
   - Y la vacante es Presencial o Híbrida

3. **Incompatible (0.0 puntos)**
   - No coincide la provincia
   - Y la vacante es Presencial o Híbrida
   - O falta información de ubicación

### Antes
- Comparación simple de strings sin normalización
- No consideraba modalidad de trabajo

### Ahora
- Normalización de nombres (minúsculas, sin acentos)
- Considera la modalidad de trabajo en el scoring
- API con datos estructurados (códigos INE)
- Autocompletado inteligente en formularios
- Comparación precisa provincia + municipio

## Próximos Pasos Recomendados

1. **Importar Listado Completo del INE**
   - Descargar: https://www.ine.es/daco/daco42/codmun/cod_municipios_provincia.htm
   - Crear script de importación masiva
   - ~8,000 municipios españoles

2. **Geocodificación (Opcional)**
   - Añadir coordenadas (lat/lng) a municipios
   - Calcular distancia real en km
   - Scoring más preciso basado en distancia

3. **Cache**
   - Cachear lista de provincias (raramente cambia)
   - Optimizar búsquedas frecuentes

4. **Testing**
   - Tests unitarios para normalización de provincias
   - Tests de integración para API endpoints
   - Tests de scoring de compatibilidad

## Uso

### En el Frontend (Vue)

```vue
<script setup>
import MunicipalitySearch from '@/Components/MunicipalitySearch.vue'

const form = useForm({
  city: '',
  province: ''
})

function handleMunicipalitySelect(municipality) {
  form.city = municipality.name
  form.province = municipality.province_name
}
</script>

<template>
  <MunicipalitySearch
    label="Municipio y Provincia"
    placeholder="Escribe para buscar..."
    @select="handleMunicipalitySelect"
    :error="form.errors.city || form.errors.province"
  />
  <div v-if="form.city && form.province">
    Seleccionado: <strong>{{ form.city }}</strong> ({{ form.province }})
  </div>
</template>
```

### En el Backend (PHP)

```php
use App\Models\Municipality;
use App\Models\Province;

// Buscar municipios
$municipalities = Municipality::search('madrid', 20);

// Listar provincias
$provinces = Province::all();

// Comparar provincias
$isSame = Province::isSameProvince('Madrid', 'madrid'); // true
```

## Estado Actual

✅ Todas las tareas completadas:
1. ✅ Modelo Province creado
2. ✅ API endpoints implementados
3. ✅ CompatibilityService mejorado
4. ✅ Formularios frontend actualizados
5. ✅ Seeder de municipios ejecutado

El sistema está listo para uso en desarrollo. Para producción, importar el listado completo del INE.
