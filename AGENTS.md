# Gudang Pakde — AI Agent Instructions

> **Mandatory entry point for coding agents.** This repository is the GITS Fullstack Developer mini-project. Build a functional MVP, not a speculative enterprise platform.

## 1. Source of truth and reading order
Before modifying code, read:
1. `docs/01-requirements.md` — authoritative MVP scope and acceptance criteria.
2. `docs/02-tech-stack-decisions.md` — locked stack and architectural decisions.
3. `docs/03-application-architecture.md` — implementation boundaries and flows.
4. `docs/05-database-design.md` — schema and data constraints.
5. `docs/06-api-documentation.md` — web/Inertia route contracts.
6. `docs/07-scaling-and-security.md` — security requirements.
7. `docs/09-ui-ux-design-system.md` — mandatory visual tokens, components, responsiveness, accessibility.
8. `docs/10-page-specifications.md` — page wireframes, user flows, interaction/empty/error states.
9. `docs/04-infrastructure-architecture.md` and `docs/08-deployment-and-operations.md` — production design and submission.

The original GITS assignment is the authority for **required outcomes**. These docs are the project team's implementation decisions. If they conflict with the assignment, flag the conflict; do not silently change scope. If docs conflict with each other, report it before implementing. The actual installed package versions and existing source files are authoritative for **current implementation state**; do not claim a feature exists just because it is documented.

## 2. Locked decisions
- Product name: **Gudang Pakde**.
- Backend: **Laravel 13**; frontend: **Vue 3 + Inertia.js + Tailwind CSS**.
- Use **TypeScript where the existing Vue setup supports it**; do not break a working JavaScript starter merely to migrate tooling during the one-day MVP.
- Database: **MySQL 8.x, InnoDB** (8.4 LTS recommended for new installs).
- Pattern: **modular monolith**, one Laravel application and one deployment unit.
- Web MVP uses **Laravel session authentication and Inertia responses**, not a separate SPA backend or mandatory REST API.
- Photos: Laravel local/public disk for MVP; S3-compatible object storage is **future production design**.
- WhatsApp: **click-to-chat link**, not WhatsApp Business API.
- Production Redis, load balancer, CDN/WAF, read replica, multiple containers, and observability are **design-only**, unless specifically requested.

## 3. Mandatory coding behavior
- Implement one vertical feature at a time; state which requirement IDs it satisfies.
- Inspect existing routes, components, dependencies, migrations, and tests before changing them.
- Prefer Laravel conventions: Form Requests, Policies, Eloquent, migrations, Inertia page props, Vue components.
- No premature repository/service abstractions, microservices, event bus, Docker orchestration, payment, AI, chat, or admin features.
- No broad unrelated refactors or dependency upgrades without explicit need.
- Never bypass authorization because a button is hidden in the frontend.
- Use server-side validation; do not trust client data.
- Use pagination and eager loading; avoid N+1 queries.
- Keep code readable and minimize new dependencies.
- Never commit `.env`, API keys, credentials, real personal data, or generated secrets.
- Update documentation if the implemented route/schema differs from the planned contract.
- Keep UI clean, modern, mobile-responsive, and Tailwind-based. **No pixel-perfect mockup is attached to this repository**; ask for the approved design asset before claiming exact visual fidelity.

## 4. Definition of done per feature
- Requirement and authorization behavior implemented.
- Happy path, invalid input, and forbidden access tested where applicable.
- `php artisan test` and `npm run build` pass (or report exact failures).
- No secrets or debug artifacts committed.
- Final response states: changed files, requirement IDs, tests executed, unresolved issues.
- Never say “tested” without running tests; never say “production-ready” solely because it builds.

## 5. Work order
1. Inspect current Laravel/Breeze/Inertia/Vue project (do not reinstall).
2. Validate MySQL connection and migrations.
3. Implement catalog data model and seeders.
4. Implement authenticated listing CRUD, upload, policies.
5. Implement catalog, search/filter, detail, profile, status, WhatsApp.
6. Add focused tests, refine responsive UI, document routes.
7. Validate clean setup and GitHub submission artifacts.

## 6. Handling ambiguity
Use the smallest implementation consistent with the assignment. Document assumptions and ask before making irreversible decisions (e.g., deleting existing migrations, changing authentication, or replacing the starter kit).
