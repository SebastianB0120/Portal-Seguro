# Informe de gestion de parches

**Proyecto:** Portal Seguro
**Fecha de evaluacion:** 31 de octubre de 2026
**Alcance:** revision local del codigo, dependencias PHP y paginas funcionales disponibles.
**Responsable:** pendiente de asignacion por el equipo.

## Resumen ejecutivo

Se actualizaron dependencias PHP con avisos de seguridad a versiones compatibles con PHP 8.2 y las restricciones actuales del proyecto. Composer informa cero avisos despues del cambio y la suite Laravel pasa: 8 pruebas y 49 aserciones. No se actualizo a Laravel 13: este proyecto usa PHP 8.2.12 y la guia requiere PHP 8.3.36 para la ruta de migracion descrita. Ese salto necesita actualizar y validar primero el runtime.

Este resultado no equivale a certificar toda la entrega de la guia. No se pudieron ejecutar las auditorias npm, ZAP o Burp en este equipo. Tampoco existen en el CMS actual las funciones CRUD, API, carga multimedia o envio de correo que la guia pide probar. Estas limitaciones se detallan mas abajo; no se registran como pruebas aprobadas.

## Inventario del entorno

| Componente | Version o estado observado |
|---|---|
| PHP CLI | 8.2.12, `C:\xampp\php\php.exe` |
| Laravel Framework | 12.69.2 |
| Entorno de Laravel | `local`; modo de depuracion activado |
| Composer | 2.10.3, ejecutado con `C:\xampp\php\composer.phar` |
| Node.js / npm | No disponibles en PATH ni en ubicaciones locales comunes revisadas |
| Dependencias JavaScript | `package.json` define Vite 7 y Tailwind CSS 4; no hay `node_modules` ni `package-lock.json` |
| Base de datos del proyecto | SQLite, segun `php artisan about` y `.env` |
| MariaDB de XAMPP | Cliente 10.4.32 disponible; la aplicacion no apunta a MariaDB en esta configuracion |
| Apache de XAMPP | 2.4.58 disponible |
| PHP extensions detectadas | Incluye `mbstring`, `openssl`, `pdo_sqlite`, `pdo_mysql`, `fileinfo`, `curl` y `zip` |
| Laravel routes | 8 rutas web; panel y modulos detras de `auth`, formularios de acceso detras de `guest` |

`composer validate --no-check-publish` reporto que `composer.json` es valido. La consulta inicial `composer outdated --direct` encontro actualizaciones menores disponibles para Laravel Framework, Sail y PHPUnit; no se actualizaron como parte de este parche porque no eran necesarias para cerrar los avisos auditados.

## Resultado de auditoria PHP

### Baseline anterior al parche

`composer audit` encontro cuatro avisos en tres paquetes:

| Paquete | Version inicial | Severidad | Aviso y aplicabilidad |
|---|---:|---|---|
| `league/commonmark` | 2.10.1 | Media | GHSA-97jj-33gv-5xf9: posible bypass XSS del filtro de HTML crudo en CommonMark. GHSA-3q6v-r5mr-hxv8: consumo cuadratico de CPU en la extension de tablas GFM. No se encontro uso de CommonMark ni Markdown en `app/`, `routes/` o `resources/`; el uso directo de esos caminos no esta demostrado, pero se actualizo la dependencia transitiva. |
| `symfony/http-foundation` | 7.4.18 | Alta | CVE-2026-108082: ReDoS por `Cache-Control`, alcanzable al construir una peticion. El componente forma parte del manejo HTTP de Laravel, por lo que la exposicion es pertinente. |
| `symfony/mime` | 7.4.18 | Media | CVE-2026-45070: inyeccion de cabeceras MIME si una entrada no confiable controla el nombre de un parametro. No se encontro envio de correo ni construccion manual de cabeceras MIME en la aplicacion. Se actualizo el componente de todas formas. |

Fuentes de los avisos confirmados:

- [GHSA-97jj-33gv-5xf9](https://github.com/advisories/GHSA-97jj-33gv-5xf9)
- [GHSA-3q6v-r5mr-hxv8](https://github.com/advisories/GHSA-3q6v-r5mr-hxv8)
- [Symfony CVE-2026-108082](https://symfony.com/blog/cve-2026-108082-redos-in-headerutils-split-reachable-through-the-cache-control-request-header)
- [Symfony CVE-2026-45070](https://symfony.com/blog/cve-2026-45070-email-header-injection-via-non-token-characters-in-mime-parameter-names)

### Cambios aplicados

Se actualizo `composer.lock`, sin cambiar las restricciones de `composer.json` ni realizar una actualizacion mayor:

| Paquete | Antes | Despues | Resultado |
|---|---:|---:|---|
| `league/commonmark` | 2.10.1 | 2.10.7 | Version posterior a los rangos afectados reportados |
| `symfony/http-foundation` | 7.4.18 | 7.4.22 | Incluye la correccion y el arreglo posterior del separador de espacios |
| `symfony/mime` | 7.4.18 | 7.4.22 | Version posterior a la correccion 7.4.22 |

Composer actualizo tambien siete dependencias transitivas compatibles. La instalacion regenero el autoloader y completo el descubrimiento de paquetes de Laravel sin error.

### Avisos indicados en la guia

Los identificadores Laravel `CVE-2026-114509` y `CVE-2026-114508` aparecen en el registro de GitHub Advisory Database. Ambos indican correccion en Laravel 12.68.0; la version instalada, 12.69.2, ya supera ese umbral. El primero afecta configuraciones de autenticacion `TokenGuard` basadas en tokens; el CMS revisado usa sesiones web y no registra un guard de API por token. El segundo requiere el camino de inclusion de relaciones afectado; no se encontraron endpoints API ni esa funcionalidad en las rutas revisadas.

Fuentes: [CVE-2026-114509 / GHSA-f9x3-7vq9-cph6](https://github.com/advisories/GHSA-f9x3-7vq9-cph6) y [CVE-2026-114508 / GHSA-5wcm-3v2w-8362](https://github.com/advisories/GHSA-5wcm-3v2w-8362).

No se pudo confirmar con los registros publicos consultados la informacion de la guia para `CVE-2026-48019`, `CVE-2026-109618` y `CVE-2026-109619`; la consulta exacta por CVE a GitHub Advisory Database no devolvio avisos. Esto no demuestra que los identificadores sean invalidos: deben verificarse con el docente o con una fuente oficial que publique el registro antes de cerrar esos puntos. Para PHP, 8.2.12 esta por debajo de la version 8.3.36 citada en la guia. No se actualizo PHP ni se declaro ese riesgo resuelto; requiere una decision de plataforma y pruebas de compatibilidad.

## Pruebas posteriores al parche

| Verificacion | Resultado |
|---|---|
| `composer validate --no-check-publish` | Correcto: `composer.json` valido |
| `composer audit` | Correcto: ningun aviso despues de actualizar |
| `php artisan test` | Correcto: 8 pruebas, 49 aserciones |
| Paginas de login, registro, cierre de sesion y panel protegido | Cubiertas por pruebas feature |
| Modulos autenticados | Los seis modulos configurados responden correctamente en pruebas |
| PHP y aplicacion | PHP 8.2.12 inicia Laravel Framework 12.69.2 |
| npm audit / npm outdated | No ejecutados: Node.js y npm no estan instalados; falta tambien `package-lock.json` |
| ZAP / Burp | No ejecutados: no se encontro configuracion ni evidencia de escaneo en el entorno |
| Paginas locales HTTP | Correcto: inicio, login y registro responden HTTP 200 |

Los modulos Contenidos, Multimedia, Usuarios y roles, Planeacion, Seguridad y Evidencias son vistas informativas; el controlador los presenta como disponibles proximamente. No se observaron operaciones CRUD persistentes, rutas API, procesamiento de imagenes ni envio de correo. Por ello, esas funciones no se reportan como implementadas o probadas.

## Plan de cierre

| Prioridad | Accion pendiente | Criterio de cierre | Responsable |
|---|---|---|---|
| Alta | Instalar PHP 8.3.36 o una version mantenida y compatible, y validar Laravel 13 solo tras resolver dependencias y compatibilidad | Version y extensiones registradas; pruebas completas exitosas en el runtime objetivo | Por asignar |
| Alta | Mantener `APP_DEBUG=false` y revisar secretos/configuracion antes de desplegar fuera de desarrollo | Configuracion de produccion validada sin exponer errores internos ni secretos | Por asignar |
| Alta | Verificar los tres CVE de PHP/Laravel no confirmados con la fuente primaria o el responsable de la guia | Referencia oficial y version corregida documentadas, o identificador corregido por el docente | Por asignar |
| Media | Instalar Node/npm en el entorno del proyecto y generar el lockfile desde una version acordada de Node | `npm audit`, `npm outdated` y compilacion frontend registrados con salida y fecha | Por asignar |
| Media | Ejecutar ZAP o Burp contra el servidor de pruebas autorizado | Archivo de resultados, alcance y fecha; hallazgos triados y reprobados despues de la correccion | Por asignar |
| Media | Implementar y probar CRUD, API, multimedia y correo solo si forman parte del alcance funcional requerido | Pruebas automatizadas de cada flujo y controles de autorizacion | Por asignar |
| Baja | Preparar evidencia GitHub y video tecnico de la entrega | Enlace accesible sin secretos y video que explique cambios y resultados | Por asignar |

No se creo un commit de pre-parche ni se publico codigo en GitHub: el cambio se hizo sobre el arbol de trabajo existente, y no se autorizo crear commits ni publicar el repositorio. Antes de cerrar la entrega academica, el equipo debe decidir como registrar la evidencia de version sin reescribir ni alterar el historial existente.

## Conclusion

La correccion de dependencias PHP fue compatible con el proyecto existente y esta respaldada por una auditoria posterior sin avisos y pruebas Laravel exitosas. No se puede afirmar que toda la guia este completada: quedan dependencias y escaneos frontend ausentes, CVE de la guia por confirmar, una plataforma PHP por actualizar y funcionalidades del CMS aun no implementadas. Estas limitaciones son bloqueos de entorno o de alcance, no pruebas aprobadas.
