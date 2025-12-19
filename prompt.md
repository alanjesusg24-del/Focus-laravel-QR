Guía para Reorganización de Proyecto Laravel (V4.0)
Este documento contiene las reglas de estructura de carpetas basadas en el Manual de Programación Laravel V4.0. El objetivo es mover los archivos a sus ubicaciones correctas y actualizar sus referencias.

1. Estructura de Carpetas Objetivo
Controladores: app/Http/Controllers/ + Subcarpeta por módulo funcional.

Modelos: app/Models/ (en singular y PascalCase).

Repositorios: app/Repositories/.

Vistas: resources/views/ organizado en:

layouts/: Plantillas maestras.

components/: Componentes Blade reutilizables.

partials/: Fragmentos como headers o sidebars.

modules/: Vistas agrupadas por módulo (ej. users/, perfil/).

Livewire: Clases en app/Livewire/ y sus vistas en resources/views/livewire/ siguiendo la misma jerarquía modular.

2. Instrucciones de Reorganización para la IA
"Claude, analiza mi proyecto y aplica los siguientes cambios de organización sin modificar la lógica interna de los métodos:

Mover Archivos: Si un archivo no cumple con la jerarquía de la Sección 1, muévelo a la carpeta correspondiente (crea la carpeta si no existe).

Actualizar Namespaces: Al mover archivos .php, ajusta el namespace al inicio del archivo para que coincida con la nueva ubicación.

Corregir Referencias (use): Busca en todo el proyecto las clases que fueron movidas y actualiza sus sentencias use.

Actualizar Rutas y Vistas:

Si un controlador se movió, actualiza su ruta en routes/web.php.

Actualiza los paths en @extends, @include y en el método view() o render() de los controladores y componentes Livewire para que apunten a las nuevas carpetas en resources/views/.

Nomenclatura: Asegura que los nombres de las rutas sigan el formato proyecto.modulo.accion (ej. ff.settings.profile)."