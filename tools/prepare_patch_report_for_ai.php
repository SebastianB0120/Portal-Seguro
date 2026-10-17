<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
$options = getopt('', ['output:', 'zap-report:', 'screenshots:', 'force', 'help']);

if (isset($options['help'])) {
    echo "Uso:\n";
    echo "  php tools/prepare_patch_report_for_ai.php [--output=ruta.md] [--zap-report=ruta.html] [--screenshots=carpeta] [--force]\n\n";
    echo "Genera un paquete Markdown con el manual y el código real para cargarlo en Manus AI.\n";
    echo "El informe ZAP y las capturas son opcionales; si no se proporcionan, el escaneo queda como no ejecutado.\n";
    exit(0);
}

$resolvePath = static function (string $path) use ($projectRoot): string {
    if (preg_match('/^(?:[A-Za-z]:[\\\\\/]|[\\\\\/])/', $path) === 1) {
        return $path;
    }

    return $projectRoot . DIRECTORY_SEPARATOR . $path;
};

$outputPath = $resolvePath((string) ($options['output'] ?? 'storage/app/private/informe-parches-contexto-ia.md'));
$outputDirectory = dirname($outputPath);

if (!is_dir($outputDirectory) && !mkdir($outputDirectory, 0775, true) && !is_dir($outputDirectory)) {
    fwrite(STDERR, "No se pudo crear la carpeta de salida: {$outputDirectory}\n");
    exit(1);
}

if (is_file($outputPath) && !isset($options['force'])) {
    fwrite(STDERR, "El archivo ya existe: {$outputPath}\nUsa --force para reemplazarlo o indica otra ruta con --output.\n");
    exit(1);
}

$sourceFiles = [
    'routes/web.php' => 'Rutas del portal y del informe',
    'app/Http/Controllers/PatchReportController.php' => 'Datos y contenido del informe',
    'config/zap.php' => 'Configuración local de OWASP ZAP',
    '.env.example' => 'Variables de entorno de ZAP',
    'resources/views/reports/patch-management.blade.php' => 'Plantilla del informe',
    'public/js/patch-report.js' => 'Generación automática de capturas PNG',
    'tests/Feature/ExampleTest.php' => 'Pruebas del manual en la portada',
    'tests/Feature/PatchReportTest.php' => 'Pruebas de la página de informe',
];

$readProjectFile = static function (string $relativePath) use ($projectRoot): ?string {
    $fullPath = $projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

    return is_file($fullPath) ? file_get_contents($fullPath) : null;
};

$markdownFence = static function (string $content): string {
    preg_match_all('/`+/', $content, $matches);
    $longestRun = 0;

    foreach ($matches[0] ?? [] as $run) {
        $longestRun = max($longestRun, strlen($run));
    }

    return str_repeat('`', max(3, $longestRun + 1));
};

$manualSource = $readProjectFile('resources/views/home.blade.php');
if ($manualSource === null) {
    fwrite(STDERR, "No se encontró resources/views/home.blade.php. Ejecuta este script desde una copia del proyecto.\n");
    exit(1);
}

if (preg_match('/<section class="patch-plan"[\s\S]*?(?=<section class="public-section")/', $manualSource, $manualMatch) === 1) {
    $manualSource = trim($manualMatch[0]);
}

$sections = [];
$sections[] = "# Paquete de contexto para Manus AI: gestión de parches y OWASP ZAP\n\n";
$sections[] = 'Generado: ' . date(DATE_ATOM) . "\n\n";
$sections[] = "## Instrucción para la otra IA\n\n";
$sections[] = "Redacta en español un informe técnico listo para entregar, usando únicamente el código y las evidencias incluidos en este paquete. Explica el manual de gestión de parches, la integración local de OWASP ZAP, el flujo de análisis pasivo y la generación de capturas. Separa implementación, procedimiento y resultados ejecutados. No inventes alertas, vulnerabilidades, métricas, correcciones ni capturas. Si el estado dice que ZAP no está disponible o no se adjunta un reporte exportado, decláralo y no presentes hallazgos como resultados. Organiza el documento con portada, objetivo, alcance, herramientas, procedimiento, evidencia disponible, limitaciones y conclusión.\n\n";
$sections[] = "## Estado del análisis\n\n";

$zapReportPath = isset($options['zap-report']) ? $resolvePath((string) $options['zap-report']) : null;
if ($zapReportPath !== null) {
    if (!is_file($zapReportPath) || !is_readable($zapReportPath)) {
        fwrite(STDERR, "No se puede leer el reporte ZAP: {$zapReportPath}\n");
        exit(1);
    }

    $zapReport = file_get_contents($zapReportPath);
    if ($zapReport === false || strlen($zapReport) > 5_000_000) {
        fwrite(STDERR, "El reporte ZAP no se pudo leer o supera el máximo permitido de 5 MB.\n");
        exit(1);
    }

    $zapLanguage = strtolower(pathinfo($zapReportPath, PATHINFO_EXTENSION)) === 'json' ? 'json' : 'html';
    $sections[] = "Se adjuntó un archivo exportado desde ZAP. La otra IA debe resumir únicamente los hallazgos contenidos en ese archivo; no debe tratarlos como verificados sin evidencia.\n\n";
    $sections[] = '### Reporte ZAP: ' . basename($zapReportPath) . "\n\n";
    $fence = $markdownFence($zapReport);
    $sections[] = $fence . $zapLanguage . "\n" . $zapReport . "\n" . $fence . "\n\n";
} else {
    $sections[] = "No se proporcionó un reporte exportado desde OWASP ZAP. Estado: no ejecutado o sin evidencia adjunta. No presentar resultados de escaneo.\n\n";
}

$sections[] = "## Manual publicado en la aplicación\n\n";
$sections[] = "Fuente: `resources/views/home.blade.php`, sección del plan y manual de parches.\n\n";
$fence = $markdownFence($manualSource);
$sections[] = $fence . "html\n" . $manualSource . "\n" . $fence . "\n\n";

$sections[] = "## Archivos del proyecto relacionados\n\n";
foreach ($sourceFiles as $relativePath => $purpose) {
    $content = $readProjectFile($relativePath);
    if ($content === null) {
        $sections[] = "### {$purpose}\n\nNo se encontró el archivo `{$relativePath}`.\n\n";
        continue;
    }

    $extension = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));
    $language = match ($extension) {
        'php', 'blade.php' => 'php',
        'js' => 'javascript',
        'json' => 'json',
        default => 'text',
    };
    $fence = $markdownFence($content);
    $sections[] = "### {$purpose}: `{$relativePath}`\n\n";
    $sections[] = $fence . $language . "\n" . rtrim($content) . "\n" . $fence . "\n\n";
}

$screenshotDirectory = isset($options['screenshots']) ? $resolvePath((string) $options['screenshots']) : null;
if ($screenshotDirectory !== null) {
    if (!is_dir($screenshotDirectory) || !is_readable($screenshotDirectory)) {
        fwrite(STDERR, "No se puede leer la carpeta de capturas: {$screenshotDirectory}\n");
        exit(1);
    }

    $assetDirectory = $outputPath . '.assets';
    if (!is_dir($assetDirectory) && !mkdir($assetDirectory, 0775, true) && !is_dir($assetDirectory)) {
        fwrite(STDERR, "No se pudo crear la carpeta de evidencias: {$assetDirectory}\n");
        exit(1);
    }

    $sections[] = "## Capturas adjuntas\n\n";
    $screenshotCount = 0;
    foreach (new DirectoryIterator($screenshotDirectory) as $fileInfo) {
        if (!$fileInfo->isFile() || $fileInfo->isLink()) {
            continue;
        }

        $extension = strtolower($fileInfo->getExtension());
        if (!in_array($extension, ['png', 'jpg', 'jpeg', 'webp'], true)) {
            continue;
        }

        $filename = basename($fileInfo->getFilename());
        $destination = $assetDirectory . DIRECTORY_SEPARATOR . $filename;
        if (!copy($fileInfo->getPathname(), $destination)) {
            fwrite(STDERR, "No se pudo copiar la captura: {$filename}\n");
            exit(1);
        }

        $relativeAssetPath = basename($outputPath) . '.assets/' . rawurlencode($filename);
        $sections[] = '![Captura: ' . $filename . '](' . $relativeAssetPath . ")\n\n";
        $screenshotCount++;
    }

    if ($screenshotCount === 0) {
        $sections[] = "No se encontraron imágenes PNG, JPG o WebP en la carpeta indicada.\n\n";
    }
} else {
    $sections[] = "## Capturas adjuntas\n\nNo se proporcionó una carpeta de capturas. Adjunta las imágenes PNG descargadas desde la página del informe, o vuelve a ejecutar este script con `--screenshots=ruta`.\n\n";
}

$sections[] = "## Criterios para redactar el informe\n\n";
$sections[] = "- No afirmar que OWASP ZAP se ejecutó si no hay un reporte exportado.\n";
$sections[] = "- Separar el análisis de dependencias (`composer audit`) del escaneo dinámico web de ZAP.\n";
$sections[] = "- Tratar los resultados del escáner como alertas que requieren revisión; no declarar una alerta confirmada sin evidencia.\n";
$sections[] = "- Describir las capturas como evidencias visuales de la página del informe, no como prueba de que el escaneo se ejecutó.\n";
$sections[] = "- Indicar como pendiente cualquier dato que no esté presente en los archivos o evidencias suministrados.\n";

$written = file_put_contents($outputPath, implode('', $sections), LOCK_EX);
if ($written === false) {
    fwrite(STDERR, "No se pudo escribir el paquete: {$outputPath}\n");
    exit(1);
}

echo "Paquete para Manus AI creado correctamente.\n";
echo "Archivo: {$outputPath}\n";
if ($screenshotDirectory !== null) {
    echo "Capturas copiadas: {$screenshotCount}\n";
    echo "Carpeta de evidencias: {$assetDirectory}\n";
}