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

Represents the lifecycle stage of a document.

- Draft
- In Review
- Published
- Archived

#### Rules:

- Defines allowed transitions
- Controls edit permissions

---

### User

System user with role-based permissions.

---

### Role

Defines access level:

- Administrator
- Operator
- Viewer

---

### Attachment

Represents files linked to a document.

- Can be PDF, DOCX, images, etc.
- Belongs to a document

---

### Audit Log

Records all significant changes in the system.

Examples:

- Document created
- State changed
- Attachment added/removed
- User updated document

---

## Business Rules

- Archived documents are immutable
- Only administrators can delete documents
- State transitions must follow defined workflow
- Every document action generates audit entries
- Document codes are automatically generated and unique
