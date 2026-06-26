<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- If Laravel Boost MCP tools are unavailable, the agent must fallback to: Artisan commands, direct file inspection, and Eloquent queries.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
    - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Docker is the ONLY deployment strategy for this project.
- Ensure all environments, including production, are deployed using Docker containers.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

</laravel-boost-guidelines>

## Project Rules

These rules define the engineering standards, architecture and development methodology for this project. They complement Laravel Boost and must be followed throughout the entire development lifecycle.

---

## Project Vision

- This project follows a **documentation-first** development methodology.
- Before implementing any functionality, always consult the documentation under `/docs`.
- Documentation is considered the **single source of truth** for the project.
- If code and documentation differ, assume the documentation is correct and ask before making changes.

---

## Reading Order

Before starting work on any task, consult the documentation in the following order:

1. `docs/01-vision.md`
2. `docs/02-requirements.md`
3. `docs/03-domain.md`
4. `docs/04-database.md`
5. `docs/05-architecture.md`
6. `docs/06-ui.md`
7. `docs/07-roadmap.md`
8. `docs/08-definition-of-done.md`
9. `docs/09-backlog.md`

---

## Architecture

- Follow the architecture defined in `docs/05-architecture.md`.
- Do not introduce new architectural patterns without explicit approval.
- Do not create new top-level directories without approval.
- Prefer extending the existing architecture over introducing new abstractions.

---

## Domain

- The business domain is defined in `docs/03-domain.md`.
- Never infer business rules.
- If a business rule is missing or ambiguous, ask before implementing it.
- The domain model always takes precedence over implementation details.

---

## Documentation

- Documentation evolves together with the code.
- Whenever an architectural, database or domain decision changes, update the corresponding document.
- Documentation is part of the project's Definition of Done.

---

## Scope

- Stay within the roadmap defined in `docs/07-roadmap.md`.
- Do not implement features outside the roadmap.
- Do not introduce additional features, packages or abstractions unless explicitly requested.
- Any new idea must be added to `/docs/09-backlog.md` first.

---

## Consistency Rule

When implementing new features, check existing code first and reuse patterns instead of creating new ones.

--

## Service Layer

- Controllers should only orchestrate requests and responses.
- Business logic must be implemented in Services or Actions, not in Controllers or Models.
- Validation belongs in Form Requests.
- Authorization belongs in Policies.
- Keep Controllers thin and focused.

---

## Database

- The official database engine for this project is **MySQL**.
- SQLite is not supported.
- Design the database according to the documented domain model before implementation.

---

## User Interface

- Follow the design language defined in `docs/06-ui.md`.
- Maintain consistency across all screens.
- Reuse existing UI patterns whenever possible.
- Do not invent new visual components without approval.

---

## Before Completing Any Task

Before considering a task complete, ensure that:

- Documentation is still accurate.
- The implementation follows the documented architecture.
- The implementation follows the documented domain model.
- Laravel Pint has been executed if PHP files were modified.
- All relevant tests pass.
- The project scope has not unintentionally expanded.

---

## Development Philosophy

When making technical decisions, always prioritize in the following order:

1. Business Domain
2. Documentation
3. Architecture
4. Laravel Conventions
5. Code Implementation

The code is the result of the design process, never the starting point.

Always prefer clarity, consistency and maintainability over clever or overly complex solutions.

---

## Source of Truth Priority

In case of conflict between:

- Code
- Documentation
- Assumptions

The priority is:

1. Documentation
2. Architecture rules in this file
3. Laravel conventions
4. Code implementation

--

## Agent Behavior

- Always read `/docs` before implementing features.
- Do not infer missing business rules.
- Do not introduce new architecture without updating documentation first.
- If uncertain, stop and ask for clarification instead of guessing.
