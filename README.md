# DeskFlow

DeskFlow is a polished support-ticket app for customers and support agents. Customers can submit a request, follow its status, and reply in one conversation. Agents get a searchable queue where they can assign, prioritize, resolve, and respond to requests. Private team notes stay hidden from customers.

Built as a Laravel portfolio project with a focus on practical product behavior, authorization, PostgreSQL, Docker, and automated testing.

## Stack

- PHP 8.5 and Laravel 13
- Livewire 4 starter-kit foundation, Blade, Tailwind CSS, and Flux UI
- PostgreSQL 18
- Docker Compose
- PHPUnit feature and unit tests
- GitHub Actions CI against PostgreSQL

## Run locally

Install Docker Engine with the Compose plugin. PHP, Composer, Node.js, and PostgreSQL do not need to be installed on the host.

```bash
git clone https://github.com/LouielAngeloQuisim/DeskFlow.git
cd DeskFlow
docker compose up --build
```

Open [http://localhost:8080](http://localhost:8080). Vite serves development assets on port `5174`; PostgreSQL is exposed to host tools on port `5434`. Compose persists database data in the `deskflow_pgdata` volume and applies migrations plus default support categories at startup.

Register through the app to create a customer account. To grant support-agent access to an existing account:

```bash
docker compose exec app php artisan deskflow:make-agent you@example.com
```

Sign out and back in to load the agent workspace. Public registration cannot choose a staff role.

### Demo data

To create a sample customer, agent, and example tickets:

```bash
docker compose exec app php artisan db:seed --class=Database\\Seeders\\DemoSeeder
```

Local demo accounts:

| Role | Email | Password |
| --- | --- | --- |
| Customer | `customer@deskflow.test` | `DeskFlowDemo!2026` |
| Support agent | `agent@deskflow.test` | `DeskFlowDemo!2026` |

These credentials are for a local portfolio demo only. Never use them on a public deployment.

## Tests and quality checks

Run the full local checks (formatting, static analysis, and tests isolated on in-memory SQLite):

```bash
docker compose exec app composer ci:check
```

The GitHub Actions workflow runs formatting, static analysis, and the test suite against a PostgreSQL service.

## Product behavior

- Customers only see their own requests and public conversation messages.
- Support agents can see the shared queue, assign tickets, update status and priority, and add public replies or internal notes.
- Staff role changes happen through the Artisan command; role selection is not exposed on registration.
- Ticket references use a short public code, separate from the database ID.
- New customer accounts can use the local MVP without email delivery. Password reset remains available, but sending reset links requires configuring a mail provider.

## Project plan

The scoped product plan, acceptance criteria, and follow-up boundaries live in [`docs/IMPLEMENTATION_PLAN.md`](docs/IMPLEMENTATION_PLAN.md).

## MVP boundaries

Email delivery, file uploads, an external API, multi-tenancy, billing, and advanced analytics are follow-up work. Internal notes, ticket ownership, and role enforcement are covered by feature tests.