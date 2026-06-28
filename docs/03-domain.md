# Domain Model

## Core Entities

---

### Document

Represents an institutional document.

A document is not a file. It is a business entity that can contain multiple attachments.

#### Properties:

- code (e.g. DOC-2026-000001)
- title
- description
- category
- state
- priority
- responsible user
- timestamps

#### Rules:

- Cannot be edited when archived
- Must have a unique code
- Every state change must be logged

---

### Category

Represents classification of documents.

Examples:

- Legal
- HR
- Procurement
- Library
- Systems

---

### State

Represents the lifecycle stage of a document. Controlled by the `DocumentWorkflowService`.

- **Draft**: The initial state. Only draft documents can be edited.
- **In Review**: Document is locked for edits and pending administrator approval.
- **Published**: Document is officially released. Cannot be reverted to Draft.
- **Archived**: Document is retired and immutable.

#### Rules:

- Defines allowed transitions (e.g., Draft -> In Review).
- Controls edit permissions via `DocumentPolicy`.

---

### User

System user with role-based permissions.

---

### Role

Defines access level (`App\Enums\Role`):

- **Administrator**: Full access to publish, reject, archive, and view audit trails.
- **Operator**: Can create documents, upload attachments, and submit drafts for review.

---

### Attachment (Media)

Represents files linked to a document (managed via `spatie/laravel-medialibrary`).

- Can be PDF, DOC, DOCX, JPG, PNG (Max 10MB per file).
- Stored on a private disk; access requires authorization.
- Maximum 5 attachments per document.

---

### Audit Log (Activity)

Records all significant changes in the system (managed via `spatie/laravel-activitylog` through `AuditLoggerInterface`).

Examples:

- Document created/updated/deleted.
- Workflow transitions (e.g., Draft -> In Review).
- Attachment added/removed.
- Records the responsible `causer` (User) and specific dirty properties.

---

## Business Rules

- Archived documents are strictly immutable.
- Only administrators can delete documents or view the audit trail.
- State transitions must follow defined workflow pathways; database state IDs are not directly editable.
- Every document action generates audit entries with a human-readable timeline.
- Document codes are automatically generated sequentially (`DOC-YYYY-0000X`) under database transactions.
