<?php

return [
    // Page titles & navigation
    'title' => 'Documents',
    'back_to_documents' => '← Back to Documents',
    'new_document' => 'New Document',
    'create_document' => 'Create Document',
    'edit_document' => 'Edit Document',
    'create_button' => 'Create Document',
    'update_button' => 'Update Document',
    'cancel' => 'Cancel',
    'edit_button' => 'Edit',
    'view_button' => 'View',
    'delete_button' => 'Delete',

    // Form fields
    'field_title' => 'Title',
    'field_description' => 'Description',
    'field_category' => 'Category',
    'field_priority' => 'Priority',
    'select_category' => 'Select a category',
    'select_priority' => 'Select a priority',

    // Filter bar
    'filter_search' => 'Search',
    'filter_search_placeholder' => 'Code or Title...',
    'filter_category' => 'Category',
    'filter_all_categories' => 'All Categories',
    'filter_state' => 'State',
    'filter_all_states' => 'All States',
    'filter_priority' => 'Priority',
    'filter_all_priorities' => 'All Priorities',
    'filter_button' => 'Filter',
    'filter_clear' => 'Clear',

    // Table headers
    'column_code' => 'Code',
    'column_title' => 'Title',
    'column_category' => 'Category',
    'column_state' => 'State',
    'column_priority' => 'Priority',
    'column_actions' => 'Actions',

    // Empty state
    'no_documents' => 'No documents found.',

    // Details section
    'section_details' => 'Details',
    'section_metadata' => 'Metadata',
    'section_workflow' => 'Workflow Actions',
    'section_attachments' => 'Attachments',
    'section_versions' => 'Version History',
    'audit_trail' => 'Audit Trail',

    // Metadata labels
    'meta_state' => 'State',
    'meta_priority' => 'Priority',
    'meta_category' => 'Category',
    'meta_responsible' => 'Responsible User',
    'meta_version_created' => 'Version Created At',
    'meta_version_updated' => 'Version Last Updated',

    // Version selector
    'version_current' => 'Current',
    'version_viewing' => 'Viewing',
    'version_view' => 'View',
    'version_notice' => 'Note: You are viewing a historical version (:version).',
    'version_view_current' => 'View Current Version',

    // Workflow actions
    'workflow_submit_review' => 'Submit for Review',
    'workflow_publish' => 'Publish Document',
    'workflow_reject' => 'Reject to Draft',
    'workflow_archive' => 'Archive Document',
    'workflow_archived_note' => 'This document is archived and read-only.',
    'workflow_draft_note' => 'Draft documents can only be submitted by operators.',
    'workflow_new_version' => 'New Version',

    // Confirmations
    'confirm_submit_review' => 'Submit this document for review?',
    'confirm_publish' => 'Publish this document?',
    'confirm_reject' => 'Reject and return to draft?',
    'confirm_archive' => 'Archive this document? It cannot be modified afterwards.',
    'confirm_new_version' => 'Create a new draft version?',
    'confirm_delete' => 'Are you sure you want to delete this entire document and all its versions?',
    'confirm_delete_attachment' => 'Are you sure you want to remove this attachment?',

    // Attachments
    'attachment_upload' => 'Upload File',
    'attachment_download' => 'Download',
    'attachment_delete' => 'Delete',
    'attachment_none' => 'No attachments found.',
    'attachment_limit' => 'Maximum of 5 attachments reached.',
    'attachment_hint' => 'Allowed types: PDF, DOC, DOCX, JPG, PNG (Max: 10MB)',

    // Audit trail
    'audit_causer' => 'System',
    'audit_changes' => 'Changes',
    'audit_field_updated' => ':field updated',
    'audit_transition' => 'Transition: :from → :to',

    // Document states (display labels)
    'states' => [
        'draft' => 'Draft',
        'in_review' => 'In Review',
        'published' => 'Published',
        'archived' => 'Archived',
    ],

    // Document priorities (display labels)
    'priorities' => [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
    ],

    // Document categories (display labels)
    'categories' => [
        'legal' => 'Legal',
        'hr' => 'HR',
        'procurement' => 'Procurement',
        'library' => 'Library',
        'systems' => 'Systems',
    ],
];
