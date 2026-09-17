<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class CmsController extends Controller
{
    public function module(string $module): View
    {
        $modules = [
            'usuarios' => [
                'label' => 'E2 / Usuarios y roles',
                'title' => 'Usuarios del CMS',
                'description' => 'Administra las identidades que podrán operar la plataforma.',
                'action' => 'Crear usuario',
                'items' => ['Administradores', 'Editores', 'Autores'],
            ],
            'contenidos' => [
                'label' => 'E4 / Contenidos',
                'title' => 'Contenido institucional',
                'description' => 'Organiza páginas, noticias y publicaciones desde un único espacio.',
                'action' => 'Crear contenido',
                'items' => ['Páginas', 'Noticias', 'Servicios'],
            ],
            'multimedia' => [
                'label' => 'E6 / Multimedia',
                'title' => 'Biblioteca multimedia',
                'description' => 'Prepara imágenes y archivos para los contenidos del portal.',
                'action' => 'Subir recurso',
                'items' => ['Imágenes', 'Banners', 'Galería'],
            ],
            'seguridad' => [
                'label' => 'E10 / Pruebas y seguridad',
                'title' => 'Centro de seguridad',
                'description' => 'Consulta los controles activos y las tareas de seguridad pendientes.',
                'action' => 'Revisar controles',
                'items' => ['Autenticación activa', 'Sesiones protegidas', 'Autorización pendiente'],
            ],
            'planeacion' => [
                'label' => 'E1 / Planeación del desarrollo',
                'title' => 'Mapa del proyecto',
                'description' => 'Convierte las ideas del equipo en épicas, historias, responsables y sprints verificables.',
                'action' => 'Ver backlog',
                'items' => ['Épicas y prioridades', 'Historias de usuario', 'Sprint siguiente'],
            ],
            'evidencias' => [
                'label' => 'QA / Evidencias y trazabilidad',
                'title' => 'Pruebas y entregables',
                'description' => 'Reúne las pruebas, capturas y vínculos que conectan cada avance con el trabajo realizado.',
                'action' => 'Registrar evidencia',
                'items' => ['Commits verificables', 'Pruebas de seguridad', 'Video técnico'],
            ],
        ];

        abort_unless(array_key_exists($module, $modules), 404);

        return view('cms.module', ['module' => $modules[$module]]);
    }
}