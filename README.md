# DeskFlow

A focused support-ticket app portfolio project. A requester can submit a ticket and follow its conversation; support staff can triage, assign, reply, and resolve requests.

**Status:** Foundation scaffold. Core ticket workflows are the next implementation milestone.

## Stack

- PHP 8.5 and Laravel 13
- Official Laravel Livewire starter kit: Livewire 4, Blade, Tailwind CSS, and Flux UI
- PostgreSQL 18
- Docker Compose for the application, frontend asset server, and database
- Pest for automated tests

## Run locally

Requirements: Docker Engine with the Compose plugin. PHP, Composer, Node.js, and PostgreSQL do not need to be installed on the host.

    git clone https://github.com/LouielAngeloQuisim/DeskFlow.git
    cd DeskFlow
    docker compose up --build

Open http://localhost:8080. Vite serves development assets on port 5174. PostgreSQL is available to host tools on port 5434 and is isolated in the deskflow_pgdata volume. The local-only database credentials are configured in Compose; change them before any deployment.

Run the test suite with:

    docker compose exec app ./vendor/bin/pest

Stop the services with docker compose down. To remove the local database too, use docker compose down -v.

## Planned MVP

- Requester and staff roles with policy-based access control
- Ticket creation, categories, priorities, statuses, assignment, and replies
- Requester ticket history and a staff queue with filters
- Feature tests for ticket workflows and authorization

Email, attachments, public API, multi-tenant support, and advanced reporting are out of scope for the first release.
