# Requirements

## Functional Requirements

- User authentication system
- Role-based access control
- Document management (CRUD)
- Category management
- Document state management
- File attachments handling
- Audit trail of changes
- Search and filtering system
- Dashboard with basic metrics

---

## Non-Functional Requirements

- Responsive design
- Secure authentication and authorization
- Maintainable architecture
- Modular code structure
- Scalable database design
- Consistent UI/UX
- Docker-based environment

---

## Core Use Cases

### Document Lifecycle

1. Administrator creates a document (Draft)
2. Operator reviews document
3. Document moves to "In Review"
4. Administrator publishes document
5. Document is eventually archived

---

### Access Control

- Administrators can manage everything
- Operators can edit documents in Draft or In Review
- Read-only access for Viewers

---

### Audit System

Every important action must be recorded:

- Document creation
- State changes
- Updates
- Attachment modifications
- Deletion (if allowed)
