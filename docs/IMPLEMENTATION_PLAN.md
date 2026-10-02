# DeskFlow Implementation Plan

## Product goal

Turn the Laravel starter kit into a complete, polished help-desk application that demonstrates practical Laravel skills: authentication, relational data modeling, authorization policies, Livewire interactions, validation, PostgreSQL, automated tests, and a reproducible Docker setup.

## MVP user experience

DeskFlow supports two roles:

- **Customer**: register/sign in, submit a support request, view their own requests, reply to an open conversation, and see status updates.
- **Support agent**: use a staff dashboard to find and filter the queue, assign work, change status and priority, and reply to customers.

New registrations are customers by default. Staff accounts are provisioned through a documented, repeatable Artisan command; role selection is never exposed to public registration. Customers can never access another customer's ticket or staff-only screens.

## Implementation phases and acceptance criteria

### 1. Product plan and local runtime baseline
- Preserve the existing Docker Compose + PostgreSQL + Laravel 13 + Livewire 4 stack.
- Record this plan in `docs/IMPLEMENTATION_PLAN.md` before feature work begins.
- Verify the app, database, Vite assets, Git state, and current starter-kit auth behavior.
- **Done when:** the plan is checked into the repository and baseline checks identify any auth/runtime issue.

### 2. Domain model and access control
- Add `role` to users with a safe customer default and a staff helper.
- Add ticket categories, tickets, and ticket messages with migrations, factories, relationships, and stable status/priority values.
- Implement ticket policies and role-aware routes; enforce ownership and staff access server-side.
- Support staff replies and private internal notes; customers only see public conversation entries.
- **Done when:** authorization feature tests prove customer isolation and staff-only operations.

### 3. Public entry and authentication
- Replace Laravel's generic welcome page with a responsive DeskFlow product landing page and clear sign-in/create-ticket calls to action.
- Keep the official starter-kit login, registration, password reset, and verification flows; apply DeskFlow branding and clear feedback.
- Registration creates a customer and signs them into their portal without requiring an unavailable mail server for local MVP use.
- Verify register → authenticated portal, logout → login → return, validation errors, and password hashing.
- **Done when:** end-to-end feature tests exercise those paths and the UI has no dead-end links.

### 4. Customer ticket portal
- Add a customer dashboard with counts, recent activity, searchable/filterable ticket list, and an empty state.
- Add validated ticket submission with subject, category, description, and priority.
- Add ticket detail/conversation view with status, reference number, timestamps, and public replies.
- Customers may reply while a ticket is open or in progress; resolved/closed tickets cannot be changed without reopening through an allowed path.
- **Done when:** a customer can create, find, open, reply to, and track only their own tickets.

### 5. Support workspace
- Build a staff dashboard and ticket queue with status/category/priority filters and search.
- Let staff assign/unassign to an agent, update priority/status, reply publicly, and add internal notes.
- Record assignment/status activity in the conversation timeline so changes are understandable.
- Add clear access-denied behavior and useful first-run empty states.
- **Done when:** a seeded agent can process a request end-to-end and internal notes never leak to customers.

### 6. Demo data, setup, and product polish
- Add deterministic sample categories and an opt-in demo seeder with customer/agent credentials documented for local portfolio review.
- Add an Artisan command for safely promoting an existing account to agent/admin; never hard-code role elevation into public forms.
- Improve responsive navigation, loading/empty/error states, consistent date/priority/status presentation, and DeskFlow metadata.
- Update README with product overview, screenshots section placeholder, architecture, roles, setup, tests, demo credentials, and security boundaries.
- **Done when:** a new clone can be started with Docker and seeded into a useful demo state from documented commands.

### 7. Verification and delivery
- Run migrations against PostgreSQL, Pint formatting, static analysis, the full feature/unit suite, Docker Compose validation, and browser smoke tests for public/auth/customer/staff pages.
- Verify no secrets or local `.env` content are committed, `git diff --check` is clean, and GitHub Actions passes.
- Commit and push the completed MVP to the existing public repository.
- **Done when:** local app and CI agree on the test result and the README accurately describes the delivered scope.

## MVP boundaries

The first complete portfolio release will not include email delivery, file uploads, billing, multitenancy, an external REST API, or advanced analytics. The data model and policies should leave those as sensible follow-up improvements without delaying the usable ticket lifecycle.

## Technical conventions

- Laravel controllers/Livewire actions validate input and delegate access decisions to policies.
- PostgreSQL is the source of truth for development and CI.
- Tickets have a non-guessable human-readable reference in addition to the internal primary key.
- All customer-visible queries scope by authenticated owner, even when a policy also protects the individual record.
- Internal notes are hidden at the query/render boundary for customers and covered by regression tests.
- Use database transactions when a ticket state change also creates a timeline entry.

## Delivery status

- [x] Phase 1 — Baseline and plan committed before implementation.
- [x] Phase 2 — Customer/support roles, ticket schema, policies, and internal notes.
- [x] Phase 3 — DeskFlow landing/auth branding; new accounts can reach the portal without email delivery.
- [x] Phase 4 — Customer dashboard, ticket submission, search, conversation, and reply flow.
- [x] Phase 5 — Staff queue, assignment, status/priority updates, public replies, and internal notes.
- [x] Phase 6 — Categories, opt-in demo seeder, agent provisioning command, responsive UI, and documentation.
- [x] Phase 7 — Local SQLite and PostgreSQL checks pass; final GitHub Actions run passed.
