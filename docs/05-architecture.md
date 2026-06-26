# Architecture

## Overview

The application follows a layered architecture based on Laravel best practices, emphasizing separation of concerns and maintainability.

---

## Layers

### Request Layer

- Routes
- Controllers
- Form Requests

### Application Layer

- Services (Business rules)
- Actions (Business rules)
- DTOs (if needed)

### Domain Layer

- Models (Anemic models only, no business logic)
- Enums

### Infrastructure Layer

- Database (Eloquent)
- File storage
- External services (future scope)

---

## Request Flow

Request → Controller → Service → Model → Database

---

## Rules

- Controllers must remain thin
- Business logic must not be placed in controllers
- Validation must use Form Requests
- Authorization must use Policies

---

## Folder Conventions

- app/Services → business logic
- app/Actions → single-purpose operations
- app/Models → domain entities
- app/Http/Requests → validation
- app/Policies → authorization
- app/Enums → state and role definitions

---

## Design Principles

- Prefer clarity over abstraction
- Avoid unnecessary patterns
- Keep domain logic centralized
- Maintain consistency across modules
