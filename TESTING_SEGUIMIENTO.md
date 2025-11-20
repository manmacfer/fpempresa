# Testing Guide: Sistema de Seguimiento de Matchings

## Resumen del sistema implementado

Se ha completado el sistema de validación de matchings y zona de seguimiento con las siguientes funcionalidades:

### 1. Validación de Matchings (Profesores)
- Panel para profesores con matchings pendientes de validar
- Vista detallada de cada matching con información completa
- Botón "Validar Matching" con modal de confirmación
- Campo para comentarios del centro educativo

### 2. Zona de Seguimiento (Todos los usuarios autenticados)
- Lista de matchings validados
- Filtrado por rol:
  - **Estudiantes**: Solo ven sus propios matchings
  - **Empresas**: Solo ven sus propios matchings
  - **Profesores**: Ven todos los matchings validados
- Tres pestañas en la vista detallada:
  1. **Resumen**: Información completa del matching
  2. **Chat**: Sistema de mensajería entre participantes
  3. **Documentos**: Subida y descarga de archivos

## Flujo de prueba

### Paso 1: Datos de prueba
Ya existe un matching de prueba creado:
- **Estudiante**: ID 7 (Alumno4)
- **Empresa**: ID 5 (Libreria)
- **Estado**: student_matched=true, company_matched=true, validado_centro=false

### Paso 2: Login como profesor
1. Ir a la aplicación: http://localhost
2. Login con credenciales de profesor (role_id = 2)
3. En el navbar, hacer clic en "Matchings"

### Paso 3: Validar matching
1. Deberías ver el matching pendiente de validación
2. Hacer clic en "Ver detalle"
3. Revisar la información del estudiante, empresa y vacante
4. Hacer clic en "Validar Matching"
5. En el modal, escribir comentarios (opcional)
6. Confirmar la validación

**Resultado esperado**: 
- Matching marcado como validado (validado_centro=true)
- Conversación creada automáticamente
- Redirección a zona de seguimiento

### Paso 4: Acceder a zona de seguimiento
1. En el navbar, hacer clic en "Seguimiento"
2. Deberías ver el matching validado en la lista
3. Hacer clic en "Ver detalle"

### Paso 5: Probar chat
1. En la vista detallada, ir a la pestaña "Chat"
2. Escribir un mensaje en el textarea
3. Hacer clic en "Enviar mensaje"

**Resultado esperado**:
- Mensaje aparece en la lista de mensajes
- Muestra usuario, contenido y fecha/hora
- Formulario se limpia después del envío

### Paso 6: Probar subida de documentos
1. Ir a la pestaña "Documentos"
2. Seleccionar tipo de documento (Contrato, Informe, Certificado, Otro)
3. Elegir un archivo (PDF, DOC, XLS, imagen)
4. Hacer clic en "Subir documento"

**Resultado esperado**:
- Documento aparece en la lista
- Muestra icono según tipo, nombre, usuario que lo subió, fecha
- Botón "Descargar" funcional

### Paso 7: Probar como estudiante/empresa
1. Logout del profesor
2. Login como estudiante (user_id del estudiante en el matching)
3. Ir a "Seguimiento"
4. Deberías ver SOLO tu matching validado
5. Probar enviar mensajes y subir documentos
6. Repetir con usuario empresa

## Verificaciones técnicas

### Base de datos
```sql
-- Ver matching validado
SELECT id, student_id, company_id, validado_centro, student_matched, company_matched 
FROM matchings 
WHERE validado_centro = 1;

-- Ver conversación creada
SELECT * FROM conversations WHERE matching_id = [ID_MATCHING];

-- Ver mensajes
SELECT m.*, u.name as user_name 
FROM messages m 
JOIN users u ON m.user_id = u.id 
WHERE conversation_id = [ID_CONVERSATION];

-- Ver documentos
SELECT md.*, u.name as uploader_name 
FROM matching_documents md 
JOIN users u ON md.uploaded_by = u.id 
WHERE matching_id = [ID_MATCHING];
```

### Rutas implementadas
- `GET /teacher/matchings` - Lista matchings pendientes (profesor)
- `GET /teacher/matchings/{matching}` - Detalle matching (profesor)
- `POST /teacher/matchings/{matching}/validate` - Validar matching (profesor)
- `GET /seguimiento` - Lista matchings validados (todos)
- `GET /seguimiento/{matching}` - Detalle con chat/docs (todos)
- `POST /conversations/{conversation}/messages` - Enviar mensaje
- `POST /matchings/{matching}/documents` - Subir documento
- `GET /documents/{document}/download` - Descargar documento

## Permisos de acceso

### Chat y documentos
Pueden participar:
- Estudiante del matching
- Empresa del matching
- Profesores (role_id = 2)

### Validación de matchings
- Solo profesores pueden validar
- Solo matchings con student_matched=true Y company_matched=true

## Archivos clave

### Backend
- `app/Http/Controllers/MatchingController.php` - Validación y seguimiento
- `app/Http/Controllers/MessageController.php` - Chat
- `app/Http/Controllers/DocumentController.php` - Documentos
- `app/Models/Matching.php` - Scopes: completed(), validated(), pendingValidation()
- `app/Models/Conversation.php` - Relación con mensajes
- `app/Models/Message.php` - Mensajes del chat
- `app/Models/MatchingDocument.php` - Documentos compartidos

### Frontend
- `resources/js/Pages/Teacher/Matchings/Index.vue` - Lista pendientes
- `resources/js/Pages/Teacher/Matchings/Show.vue` - Detalle y validación
- `resources/js/Pages/Seguimiento/Index.vue` - Lista validados
- `resources/js/Pages/Seguimiento/Show.vue` - Detalle con 3 pestañas

### Rutas
- `routes/web.php` - Todas las rutas registradas

## Notas importantes

1. **Storage público**: Asegurarse de que `storage/app/public` está enlazado:
   ```bash
   docker exec fpempresa-laravel.test-1 php artisan storage:link
   ```

2. **Permisos de archivos**: El directorio `storage/app/public/matching_documents` debe ser escribible

3. **Navegación por roles**: El navbar muestra enlaces según el rol:
   - "Matchings" solo para profesores
   - "Seguimiento" para todos los autenticados

4. **Datos de prueba adicionales**: Para crear más matchings de prueba:
   ```bash
   docker exec fpempresa-laravel.test-1 php artisan db:seed --class=MatchingTestSeeder
   ```

## Posibles mejoras futuras

- [ ] Notificaciones en tiempo real (pusher/websockets)
- [ ] Paginación de mensajes en conversaciones largas
- [ ] Búsqueda/filtrado en zona de seguimiento
- [ ] Exportar conversación a PDF
- [ ] Previsualización de documentos
- [ ] Estados adicionales (completado, cancelado)
- [ ] Recordatorios automáticos para profesores
