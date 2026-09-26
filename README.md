# Portal Seguro

Portal Seguro es un proyecto de CMS institucional desarrollado con Laravel para centralizar contenido, controlar accesos y documentar el trabajo del equipo con trazabilidad, seguridad y evidencia.

## 1. Objetivo del proyecto

El objetivo principal es convertir la planeación del trabajo en un entorno operativo con estas características:

- organización clara de contenidos, usuarios y evidencias;
- control de autenticación y sesiones para usuarios registrados;
- trazabilidad del proyecto desde la planeación hasta la entrega;
- estructura modular pensada para crecer sin perder orden ni seguridad.

## 2. Estado actual

El proyecto se encuentra en desarrollo inicial, con la base funcional construida y el siguiente enfoque de trabajo:

- Login y registro con autenticación segura.
- Dashboard protegido para usuarios autenticados.
- Navegación del CMS por módulos.
- Planeación del proyecto conectada con historias, sprints y evidencias.

## 3. Módulos del CMS

El sistema está dividido en los siguientes módulos:

1. Resumen
   - Vista principal del panel administrativo.
   - Muestra avances, control y próximos movimientos.

2. Contenidos
   - Páginas, noticias y servicios institucionales.
   - Organización centralizada del contenido del portal.

3. Usuarios y roles
   - Administración de identidades y perfiles de operación.
   - Base para permisos y accesos por rol.

4. Multimedia
   - Biblioteca de recursos visuales y documentos del portal.

5. Planeación
   - Mapa del proyecto, backlog, épicas e historias.
   - Enlace directo con la trazabilidad del desarrollo.

6. Seguridad
   - Revisión de controles activos, validaciones y tareas pendientes.

7. Evidencias
   - Registro de entregables, pruebas, referencias y trazabilidad técnica.

## 4. Tecnologías utilizadas

- PHP 8.2
- Laravel 12
- Blade para vistas
- Vite para assets frontend
- MySQL / base de datos del entorno Laravel
- Composer y npm para gestión de dependencias

## 5. Requisitos previos

Antes de iniciar, asegúrate de tener instalado:

- PHP 8.2+
- Composer
- Node.js y npm
- Servidor local o XAMPP con acceso a PHP
- Base de datos configurada en el archivo .env

## 6. Instalación

Ejecuta los siguientes pasos en la raíz del proyecto:

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
```

Si la base de datos aún no está creada y configurada, ajusta las credenciales dentro de `.env` y después ejecuta:

```bash
php artisan migrate
```

## 7. Ejecución del proyecto

Para levantar la aplicación en entorno local:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Y para compilar los assets frontend:

```bash
npm run dev
```

También puedes utilizar el script del proyecto para configuración rápida:

```bash
npm run setup
```

## 8. Estructura principal del proyecto

```text
app/
  Http/Controllers/
  Models/
config/
database/
public/
resources/
routes/
storage/
tests/
```

### Archivos clave

- `routes/web.php`: defines las rutas públicas y del panel.
- `app/Http/Controllers/CmsController.php`: controla los módulos del CMS.
- `resources/views/dashboard.blade.php`: vista del dashboard autenticado.
- `resources/views/home.blade.php`: vista pública inicial.
- `resources/views/cms/navigation.blade.php`: menú del CMS.

## 9. Flujo de trabajo recomendado

El proyecto sigue un enfoque de trabajo orientado a evidencia y control:

1. Definir la historia o tarea.
2. Asignar responsable y criterio de aceptación.
3. Validar controles de seguridad.
4. Ejecutar pruebas y revisar entregables.
5. Registrar la evidencia asociada al cambio.

## 10. Seguridad y buenas prácticas

- Usar autenticación por sesión para rutas protegidas.
- Mantener validaciones en formularios y entradas.
- Revisar hash de contraseñas y manejo de sesiones.
- Evitar exposición de información sensible en vistas públicas.
- Documentar cada avance con evidencia y trazabilidad.

## 11. Próximo avance sugerido

El siguiente bloque de trabajo recomendado es:

- completar permisos por roles;
- ampliar gestión de contenidos;
- consolidar evidencias y pruebas del proyecto;
- cerrar la trazabilidad del backlog con responsables y entregables reales.

## 12. Licencia

Este proyecto se encuentra bajo la licencia MIT, como base del ecosistema Laravel.

---

Documento base del proyecto Portal Seguro. Mantener este README actualizado conforme avanza el CMS.
