# Architecture

## Overview

The application follows a layered architecture based on Laravel best practices, emphasizing separation of concerns and maintainability.

---

## Layers

### Request Layer

- Routes (`web.php`)
- Controllers (e.g., `DocumentController`, `DocumentWorkflowController`, `DashboardController`)
- Form Requests (e.g., `StoreDocumentRequest`, `IndexDocumentRequest`)

### Application Layer

- Services (e.g., `DocumentService`, `DocumentWorkflowService`, `DashboardService`)
- Laravel Scout (Database engine)
- Azure AI Document Intelligence (for OCR / text extraction)
- Contracts / Interfaces (e.g., `AuditLoggerInterface`)
- DTOs (if needed)

### Domain Layer

- Models (Anemic models only, no business logic)
- Enums (`DocumentStateName`, `Role`, `DocumentPriority`)

### Infrastructure Layer

- Database (MySQL 8.x via Eloquent)
- Cache & Queue Broker (Redis)
- Background Jobs (Laravel Horizon with dedicated worker containers)
- File storage (`spatie/laravel-medialibrary` configurable via `MEDIA_DISK` for `local` or `azure`. Azure Blob containers must be private, with downloads served through Laravel to preserve the authorization model.)
- Audit Logging (`spatie/laravel-activitylog` via `SpatieAuditLogger`)

---

## Request Flow

1. **Browser** sends request.
2. **Route** forwards to **Controller**.
3. **Form Request** validates data and authorizes (or **Gate/Policy** in Controller).
4. **Controller** calls **Service** with validated data.
5. **Hybrid Search (Laravel Scout + Azure AI Document Intelligence)**
   - **Search Engine:** Laravel Scout configured with the `database` engine.
   - **Traits:** The `Searchable` trait is applied to the `Document` model. The `toSearchableArray` method aggregates metadata such as `code`, and delegates to its `currentVersion` for `title`, `description`, and `extracted_text`.
   - **OCR Integration:**
     - When attachments are uploaded to a `DocumentVersion`, an asynchronous `ProcessDocumentOcrJob` is dispatched to the background queue (specifically, the `ocr` queue).
     - The background job is processed by a dedicated worker running Laravel Horizon via Redis.
     - This job downloads the file stream from the underlying disk (Local or Azure Blob Storage) and submits it to Azure AI Document Intelligence (`prebuilt-read` endpoint) via the REST API.
     - The job polls Azure until text is returned, then saves the extracted content into the `extracted_text` column of the `document_versions` table.
     - Saving the model triggers Laravel Scout to re-index the data.
   - **Query Integration:** The `Document::scopeFilter` uses Scout's `keys()` method (`Document::search($term)->keys()`) to retrieve matching Document IDs, injecting them into the Eloquent query with a `whereIn` clause. This allows seamlessly blending Scout's full-text matching with existing Eloquent filtering criteria without breaking pagination or REST endpoints.
6. **Service** executes business logic, calls **Models** (Database), and fires **Audit Logger**.
7. **Controller** returns response/view.

---

## Rules

- **Controllers must remain thin**: Only orchestrate requests and responses.
- **Business logic must not be placed in controllers**: Use Services.
- **Validation must use Form Requests**: Keep validation rules reusable and isolated.
- **Authorization must use Policies**: Bind policies to models (e.g., `DocumentPolicy`).
- **Encapsulate Third-Party Packages**: e.g., Audit logging is hidden behind `AuditLoggerInterface`.
- **Eager Loading**: Prevent N+1 queries by eager loading relationships inside Services/Controllers.

---

## Folder Conventions

- `app/Contracts` &rarr; Interfaces shielding the application from external dependencies.
- `app/Services` &rarr; Core business logic, workflow, and query aggregation.
- `app/Models` &rarr; Domain entities and relationships.
- `app/Http/Requests` &rarr; Validation rules.
- `app/Policies` &rarr; Authorization rules.
- `app/Enums` &rarr; Type-safe state and role definitions.

---

## Design Principles

- Prefer clarity over abstraction
- Avoid unnecessary patterns
- Keep domain logic centralized
- Maintain consistency across modules
