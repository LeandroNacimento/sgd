<?php

return [
    // Page titles & navigation
    'title' => 'Documentos',
    'back_to_documents' => '← Volver a Documentos',
    'new_document' => 'Nuevo documento',
    'create_document' => 'Crear documento',
    'edit_document' => 'Editar documento',
    'create_button' => 'Crear documento',
    'update_button' => 'Actualizar documento',
    'cancel' => 'Cancelar',
    'edit_button' => 'Editar',
    'view_button' => 'Ver',
    'delete_button' => 'Eliminar',

    // Form fields
    'field_title' => 'Título',
    'field_description' => 'Descripción',
    'field_category' => 'Categoría',
    'field_priority' => 'Prioridad',
    'select_category' => 'Selecciona una categoría',
    'select_priority' => 'Selecciona una prioridad',

    // Filter bar
    'filter_search' => 'Búsqueda',
    'filter_search_placeholder' => 'Código o título...',
    'filter_category' => 'Categoría',
    'filter_all_categories' => 'Todas las categorías',
    'filter_state' => 'Estado',
    'filter_all_states' => 'Todos los estados',
    'filter_priority' => 'Prioridad',
    'filter_all_priorities' => 'Todas las prioridades',
    'filter_button' => 'Filtrar',
    'filter_clear' => 'Limpiar',

    // Table headers
    'column_code' => 'Código',
    'column_title' => 'Título',
    'column_category' => 'Categoría',
    'column_state' => 'Estado',
    'column_priority' => 'Prioridad',
    'column_actions' => 'Acciones',

    // Empty state
    'no_documents' => 'No se encontraron documentos.',

    // Details section
    'section_details' => 'Detalles',
    'section_metadata' => 'Metadatos',
    'section_workflow' => 'Acciones de flujo',
    'section_attachments' => 'Adjuntos',
    'section_versions' => 'Historial de versiones',
    'audit_trail' => 'Registro de auditoría',

    // Metadata labels
    'meta_state' => 'Estado',
    'meta_priority' => 'Prioridad',
    'meta_category' => 'Categoría',
    'meta_responsible' => 'Responsable',
    'meta_version_created' => 'Versión creada',
    'meta_version_updated' => 'Versión actualizada',

    // Version selector
    'version_current' => 'Actual',
    'version_viewing' => 'Viendo',
    'version_view' => 'Ver',
    'version_notice' => 'Nota: Estás viendo una versión histórica (:version).',
    'version_view_current' => 'Ver versión actual',

    // Workflow actions
    'workflow_submit_review' => 'Enviar a revisión',
    'workflow_publish' => 'Publicar documento',
    'workflow_reject' => 'Rechazar a borrador',
    'workflow_archive' => 'Archivar documento',
    'workflow_archived_note' => 'Este documento está archivado y es de sólo lectura.',
    'workflow_draft_note' => 'Los borradores sólo pueden ser enviados por operadores.',
    'workflow_new_version' => 'Nueva versión',

    // Confirmations
    'confirm_submit_review' => '¿Enviar este documento a revisión?',
    'confirm_publish' => '¿Publicar este documento?',
    'confirm_reject' => '¿Rechazar y volver a borrador?',
    'confirm_archive' => '¿Archivar este documento? No podrá modificarse después.',
    'confirm_new_version' => '¿Crear una nueva versión en borrador?',
    'confirm_delete' => '¿Estás seguro de que deseas eliminar este documento y todas sus versiones?',
    'confirm_delete_attachment' => '¿Estás seguro de que deseas eliminar este adjunto?',

    // Attachments
    'attachment_upload' => 'Subir archivo',
    'attachment_download' => 'Descargar',
    'attachment_delete' => 'Eliminar',
    'attachment_none' => 'No se encontraron adjuntos.',
    'attachment_limit' => 'Se alcanzó el límite máximo de 5 adjuntos.',
    'attachment_hint' => 'Tipos permitidos: PDF, DOC, DOCX, JPG, PNG (Máx: 10 MB)',

    // Audit trail
    'audit_causer' => 'Sistema',
    'audit_changes' => 'Cambios',
    'audit_field_updated' => ':field actualizado',
    'audit_transition' => 'Transición: :from → :to',
    'audit_event_created' => 'Versión del documento creada',
    'audit_event_updated' => 'Versión del documento actualizada',
    'audit_event_deleted' => 'Documento eliminado',
    'audit_event_attachment_uploaded' => 'Archivo adjunto subido: :filename',
    'audit_event_attachment_deleted' => 'Archivo adjunto eliminado: :filename',
    'audit_event_workflow_transition' => 'Cambio de estado del documento',

    // Flash messages
    'flash_created' => 'Documento creado exitosamente.',
    'flash_updated' => 'Documento actualizado exitosamente.',
    'flash_deleted' => 'Documento eliminado exitosamente.',
    'flash_submitted' => 'Documento enviado a revisión.',
    'flash_published' => 'Documento publicado exitosamente.',
    'flash_rejected' => 'Documento rechazado y devuelto a borrador.',
    'flash_archived' => 'Documento archivado exitosamente.',
    'flash_new_version' => 'Nueva versión en borrador creada exitosamente.',
    'flash_attachment_uploaded' => 'Adjunto subido exitosamente.',
    'flash_attachment_deleted' => 'Adjunto eliminado exitosamente.',

    // Document states (display labels)
    'states' => [
        'draft' => 'Borrador',
        'in_review' => 'En Revisión',
        'published' => 'Publicado',
        'archived' => 'Archivado',
    ],

    // Document priorities (display labels)
    'priorities' => [
        'low' => 'Baja',
        'medium' => 'Media',
        'high' => 'Alta',
    ],

    // Document categories (display labels)
    'categories' => [
        'legal' => 'Legal',
        'hr' => 'Recursos Humanos',
        'procurement' => 'Compras',
        'library' => 'Biblioteca',
        'systems' => 'Sistemas',
    ],
];
