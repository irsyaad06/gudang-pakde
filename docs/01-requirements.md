# 01 — Requirements & Scope

**Product:** Gudang Pakde  
**Source:** *PR Fullstack Developer – GITS*, pages 1–2 (mini project, estimated 1 day).  
**Status:** Planned; do not equate documentation with completed implementation.

## Problem and objective
A community trades used personal goods via group chat. Listings are hard to find because messages, photos, and prices become scattered. Build a simple community secondhand marketplace for browsing, posting, searching, and contacting sellers. Also **design**, not necessarily deploy, an architecture for approximately 100,000 daily active users.

## Actors
- **Guest:** browse/search/filter listings, view details, open seller WhatsApp link.
- **Authenticated member:** guest actions plus profile and management of own listings. A member may both buy and sell.
- **Administrator:** not required; out of MVP scope.

## Functional requirements
| ID | Feature | Acceptance criteria |
|---|---|---|
| FR-01 | Register, login, logout | User can register, sign in, sign out; protected pages require login |
| FR-02 | User profile | Shows basic user information and listings they posted |
| FR-03 | Listings grid/list | Each item displays photo, name, price, condition, location |
| FR-04 | Search & filters | Search listings; filter by category and condition; filters can combine |
| FR-05 | Listing details | Photo, price, description, condition, location, seller information |
| FR-06 | Create listing | Authenticated member submits name, category, price, condition, description, location, photo |
| FR-07 | Manage own listings | Member views, edits, and deletes only their own listings |
| FR-08 | Availability status | Owner can set `available` or `sold`; status is visible |
| FR-09 | Seller contact | Opens WhatsApp with a prefilled message referring to the item |

## Business rules (project decisions)
1. All users can view public listings; modifying listing data requires authentication.
2. Only the owner can update/delete/change status; unauthorized request returns 403.
3. Validate required fields, category existence, nonnegative price, accepted image type and size.
4. A seller must have a valid WhatsApp phone number to publish a contactable listing.
5. Deletion uses soft delete; deleted items do not appear in public catalog.
6. `sold` listings remain visible with a clear status indicator.
7. One image per listing is acceptable in the 1-day MVP; database may support multiple.
8. WhatsApp is an external redirect; the application does not send messages or process payments.

## Explicitly out of scope
Checkout, payment gateway, escrow, shipping, in-app messaging, reviews, favorites, notifications, admin panel, AI API, microservices, and production cloud provisioning. These are not in the source assignment.

## Nonfunctional requirements
- Mobile-responsive UI and accessible form labels/states.
- Password hashing, CSRF, authorization, server validation, safe file upload.
- Search pagination, eager loading, appropriate database indexes.
- Local setup reproducible from README and `.env.example`.
- Architecture documentation for 100K DAU with stated traffic assumptions, scaling, monitoring, backup, and security boundaries.

## Submission acceptance checklist (from assignment)
- [ ] GitHub repository accessible to reviewer
- [ ] Laravel and Vue source code
- [ ] README with local setup/configuration and run commands
- [ ] Database design and relationships
- [ ] Application architecture diagram
- [ ] Infrastructure architecture diagram
- [ ] Endpoint/method/input/response/auth documentation
- [ ] Architecture decisions and trade-offs
- [ ] Deployment/operations, monitoring, backup, troubleshooting
- [ ] `.env.example` without real credentials
- [ ] Screenshots/demo link if available
- [ ] Application runs locally

## Priority
All nine functional requirements are **P0**. Production infrastructure is **documentation only**. If time is tight, simplify visuals and image count, but do not skip ownership checks or essential documentation.
