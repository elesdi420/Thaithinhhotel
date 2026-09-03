---
title: 'Story 1.1: Triển khai mã nguồn QloApps'
type: 'chore'
created: '2026-09-02'
status: 'done'
review_loop_iteration: 0
context: []
baseline_commit: NO_VCS
---

<frozen-after-approval reason="human-owned intent — do not modify unless human renegotiates">

## Intent

**Problem:** The CMS workspace currently has no application runtime or database, leaving all hotel PMS features in backlog.

**Approach:** Sync QloApps source into the project, provide a Docker Compose environment (PHP 8.2 Apache + MariaDB 10.11), and execute the silent CLI installer (`install/index_cli.php`) to initialize the database and admin dashboard.

## Boundaries & Constraints

**Always:**
- Keep QloApps core clean and unmodified (AD-1).
- Use containerized setup via `docker-compose.yml` (AD-4).
- Ensure environment settings are configured via `.env`.

**Ask First:**
- Confirming Docker daemon startup if Docker Desktop is stopped.

**Never:**
- Do not edit core files under `classes/`, `controllers/`, or `config/`.
- Do not add Kubernetes, cloud infrastructure, or multi-tenant database isolation.

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| Container startup | `docker compose up -d` | Web and DB containers healthy | Inspect container logs with `docker compose logs` |
| Web frontend access | HTTP GET `http://localhost:8080/` | HTTP 200 with QloApps hotel homepage | Verify Apache docroot and permissions |
| Admin panel access | HTTP GET `http://localhost:8080/admin/` | HTTP 200 with Admin Login page | Verify admin path after installation |

</frozen-after-approval>

## Code Map

- `docker-compose.yml` -- Defines `web` (PHP 8.2 Apache) and `db` (MariaDB 10.11) services with port 8080.
- `Dockerfile` -- Custom PHP 8.2 Apache container with extensions: `pdo_mysql`, `gd`, `intl`, `zip`, `bcmath`.
- `.env` -- Local configuration for DB user, DB password, database name, and admin credentials.
- `install/index_cli.php` -- Native CLI installer for automated database creation and admin user seeding.

## Tasks & Acceptance

**Execution:**
- [x] `docker-compose.yml` -- create Docker Compose configuration -- define web and db containers with persistent volumes
- [x] `Dockerfile` -- build PHP 8.2 Apache image with required PHP extensions -- ensure GD, intl, pdo_mysql, zip are installed
- [x] `.env` -- create environment template -- define DB_NAME, DB_USER, DB_PASSWORD, PORT
- [x] `qloapps source` -- sync QloApps source code into workspace -- populate codebase without overwriting `_bmad`
- [x] `install/index_cli.php` -- run CLI installer inside container -- bootstrap QloApps database and default admin user

**Acceptance Criteria:**
- Given Docker Desktop is running
- When `docker compose up -d` and installation command are executed
- Then `http://localhost:8080` responds with HTTP 200 showing QloApps homepage
- And the admin login screen is accessible

## Design Notes

QloApps requires `install/` directory to be removed or renamed after installation to unlock the back-office admin login. MariaDB 10.11 is used for reliable compatibility with PHP 8.2.

## Verification

**Commands:**
- `docker compose ps` -- expected: both services running
- `curl -s -o /dev/null -w "%{http_code}" http://localhost:8080` -- expected: `200` or `302`

## Suggested Review Order

**Container & Runtime Configuration**

- Docker Compose orchestration for web and database services
  [`docker-compose.yml:1`](../../docker-compose.yml#L1)

- Custom PHP 8.2 Apache image with GD, PDO MySQL, Zip, Intl extensions
  [`Dockerfile:1`](../../Dockerfile#L1)

- Environment configuration for local ports and database credentials
  [`.env:1`](../../.env#L1)
