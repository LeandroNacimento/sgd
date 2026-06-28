# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-06-28

### Added (v1.0 Core Milestones)
- **M1: Foundation:** Scaffolded Laravel, configured Sail, Pest, Pint, and Tailwind CSS. Established base `app` layout.
- **M2: Core Entities:** Implemented models, migrations, factories, and seeders for `Document`, `Category`, `Role`, `User`, and `DocumentState`.
- **M3: CRUD Foundation:** Built foundational `DocumentController` featuring list, create, edit, update, and delete actions with Form Requests.
- **M4: Sequential ID Generation:** Introduced `DocumentCodeGenerator` ensuring sequential `DOC-YYYY-000X` format generation under database transactions.
- **M5: Authentication & Authorization:** Integrated Laravel Breeze. Created standard roles (`Operator`, `Administrator`) and enforced them via `DocumentPolicy` and Controller Gates.
- **M6: UI Refinement:** Upgraded views with a polished Tailwind CSS design system, intuitive sidebar navigation, and standardized form partials.
- **M7: Architectural Verification:** Ensured clean separation between Form Requests and Controller authorization. Validated `DocumentCodeGenerator` robustness and index query pagination.
- **M8: Discovery (Search & Filter):** Upgraded index view with title/code search and filtering by Category and State. Introduced `IndexDocumentRequest`.
- **M9: File Attachments:** Integrated `spatie/laravel-medialibrary` using private storage. Implemented `DocumentAttachmentController` to securely store, download, and delete attachments.
- **M10: Document Workflow:** Transitioned to Domain-Driven workflow. Created `DocumentWorkflowService` to enforce strict state transitions (Draft &rarr; In Review &rarr; Published &rarr; Archived) via dedicated routes and specific policies.
- **M11: Audit System:** Integrated `spatie/laravel-activitylog` abstracted behind `AuditLoggerInterface`. Audits all document changes, file attachments, and workflow transitions. Exposed timeline UI to Administrators.
- **M12: Operational Dashboard:** Added a metric-rich dashboard surfacing total counts, state distributions, actionable "Awaiting Review" widgets, and a human-readable recent activity timeline via a thin `DashboardService`.

### Changed
- Refactored `DocumentController` to orchestrate actions exclusively, migrating business logic to `DocumentService`.
- Replaced direct state manipulation with explicitly governed Workflow endpoints.

### Removed
- Removed default Laravel placeholder dashboard in favor of the SGD operational dashboard.
