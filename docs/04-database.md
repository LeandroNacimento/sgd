# Database Design

## Engine

- MySQL (official database engine for all environments, including testing)
- SQLite (including in-memory testing) is NOT allowed.

---

## Tables

- users
- roles
- documents
- categories
- document_states
- attachments
- audit_logs

---

## Relationships

### Documents

- belongs to category
- belongs to user (responsible)
- has many attachments
- has many audit logs

---

### Attachments

- belongs to document

---

### Audit Logs

- belongs to document
- optionally belongs to user

---

## Design Decisions

- Soft deletes may be used for documents (except archived ones if required)
- UUID or structured codes for document identification (DOC-YYYY-XXXX)
- Indexes on:
    - document code
    - state
    - category
    - responsible user

---

## Constraints

- Document codes must be unique
- Required foreign keys must enforce integrity
- State transitions validated at application level
