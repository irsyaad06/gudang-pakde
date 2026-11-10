# 02 — Tech Stack & Architecture Decisions

## Approved stack
| Layer | Choice | Why | Trade-off |
|---|---|---|---|
| Backend | Laravel 13 | Eloquent, migrations, validation, policies, auth | Requires PHP runtime tuning at scale |
| Frontend | Vue 3 | Reusable, reactive components | Build/tooling overhead |
| Language | TypeScript preferred | Better type safety for components/props | May require adapting existing JS starter |
| Bridge | Inertia.js | Vue pages served by Laravel without separate REST backend | Couples web frontend to Laravel |
| UI | Tailwind CSS | Fast, consistent, responsive styling | Long utility class lists |
| Database | MySQL 8.x / InnoDB | Mature relational engine, ACID, simple CRUD | Complex text search needs tuning |
| ORM | Eloquent | Fast development and relationships | Watch N+1 and expensive queries |
| Authentication | Laravel web session | Suitable for same-origin Inertia web app | Future external/mobile clients need separate auth design |
| Media MVP | Laravel filesystem local/public | Fast and no external account | Cannot share files across app nodes |
| Media production | S3-compatible object storage | Shared media and CDN integration | External dependency/cost |
| Cache/session/queue production | Redis | Shared state and asynchronous jobs | Additional infrastructure |
| Application style | Modular monolith | One codebase/deployment, clear boundaries | Modules scale together |

**Installed project caveat:** Existing user project is Laravel 13.42.0 with Breeze-style `resources/js/Components`, `Layouts`, `Pages`, `app.js`, and `bootstrap.js`. Inspect installed versions and actual setup before changing it. Do **not** reinstall Laravel or Breeze automatically.

## ADR-001 — Modular monolith instead of microservices
**Decision:** One Laravel codebase with clear modules (identity, catalog, listing, media, contact).  
**Rationale:** Nine MVP features, one-day delivery, low coordination overhead.  
**Trade-off:** Coupled deployment; can scale entire application horizontally before extracting services.

## ADR-002 — Inertia over separate REST SPA
**Decision:** Laravel web routes return Inertia pages and props to Vue.  
**Rationale:** Avoid duplicate routing/auth/API boilerplate under time constraint.  
**Trade-off:** No independent public JSON API by default. Add `/api/v1` only if later required.

## ADR-003 — MySQL instead of PostgreSQL
**Decision:** MySQL InnoDB.  
**Rationale:** Straightforward relational marketplace requirements and team familiarity/setup simplicity.  
**Trade-off:** Advanced text search may require FULLTEXT or dedicated search later. Neither engine alone guarantees security or 100K DAU.

## ADR-004 — Local media first, object storage later
**Decision:** Laravel Storage abstraction from day one.  
**Rationale:** MVP works locally, future storage driver can change.  
**Trade-off:** Migration of existing files and URLs must be planned before multi-instance deployment.

## ADR-005 — Redis only when needed
**Decision:** MVP may use Laravel's configured local/database cache, session, and sync/database queue. Production design uses shared Redis.  
**Rationale:** Avoid provisioning complexity during MVP.  
**Trade-off:** Requires configuring shared stores before running multiple app instances.

## ADR-006 — No AI runtime or paid WhatsApp API
**Decision:** Use a WhatsApp click-to-chat URL. Coding assistants may be used in development but are not application dependencies.  
**Rationale:** Assignment requires contact button, not messaging automation.  
**Trade-off:** Cannot observe message delivery or conversations.

## Decision policy
Changes to this stack require a short ADR: context, options, decision, consequences, migration implications, and approval.
