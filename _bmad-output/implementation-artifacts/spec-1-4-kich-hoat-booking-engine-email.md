---
title: 'Story 1.4: Kích hoạt Booking Engine & Email'
type: 'feature'
created: '2026-09-02'
status: 'done'
review_loop_iteration: 0
context: []
baseline_commit: NO_VCS
---

<frozen-after-approval reason="human-owned intent — do not modify unless human renegotiates">

## Intent

**Problem:** Guests need to find available rooms by date and receive an automated booking confirmation email via SMTP upon reservation.

**Approach:** Verify and activate the direct booking engine search across all 3 branches, integrate Mailpit into Docker Compose for local SMTP email capture (with Web UI on port 8025), configure QloApps SMTP settings, and verify that booking confirmation emails (`order_conf`) are dispatched successfully.

## Boundaries & Constraints

**Always:**
- Use QloApps native booking search engine and `Mail::send` API (AD-1).
- Transactional emails only (no marketing or promo flows).
- SMTP host, port, and credentials must be configurable via `.env` / `qlo_configuration`.

**Ask First:**
- External third-party SMTP production credentials (e.g. Sendgrid API keys).

**Never:**
- Do not build custom booking search engines outside QloApps core.
- Do not build separate marketing email automation.

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| Search Room Availability | Query dates + branch ID | Returns exact count of available rooms and prices | Empty result if dates fully booked |
| Dispatch Confirmation Email | `Mail::send` triggered with `order_conf` | Email captured by SMTP server with recipient, room details, and pricing | Log failure if SMTP server unreachable |
| Mailpit UI Inspection | Open `http://localhost:8025` | Sent booking confirmation email visible in inbox | Check container health |

</frozen-after-approval>

## Code Map

- `docker-compose.yml` -- Adds `mailpit` service for local SMTP capture on port 1025 and Web UI on port 8025.
- `.env` -- Defines SMTP variables (`SMTP_SERVER`, `SMTP_PORT`, `SHOP_EMAIL`).
- `scripts/configure_smtp_and_test_email.php` -- Updates QloApps SMTP configuration and sends test booking confirmation email.

## Tasks & Acceptance

**Execution:**
- [x] `docker-compose.yml` -- add mailpit service -- enable SMTP on port 1025 and Web UI on port 8025
- [x] `docker compose up -d` -- start mailpit service -- ensure mail server container is running
- [x] `scripts/configure_smtp_and_test_email.php` -- configure SMTP in QloApps and test booking engine & email dispatch -- verify email arrives in Mailpit
- [x] `verification check` -- inspect Mailpit inbox at localhost:8025 -- confirm order_conf email is captured

**Acceptance Criteria:**
- Given a guest searches for rooms by date
- When dates are provided for any of the 3 branches
- Then the available rooms and VND prices are accurately calculated
- And when a booking confirmation is dispatched, an email is delivered via SMTP and received in Mailpit

## Design Notes

Mailpit provides a lightweight, zero-dependency SMTP server for development and testing. In production on a VPS, the administrator simply updates `PS_MAIL_SERVER`, `PS_MAIL_USER`, `PS_MAIL_PASSWD`, `PS_MAIL_SMTP_PORT` to point to their transactional email provider (Sendgrid, AWS SES, or Gmail).

## Verification

**Commands:**
- `docker exec qloapps_web php scripts/configure_smtp_and_test_email.php` -- expected: `SMTP Configured & Test Email Sent Successfully`
- `curl -s http://localhost:8025/api/v1/messages | grep -i "booking"` -- expected: message found in Mailpit API

## Suggested Review Order

**SMTP & Booking Verification**

- Mailpit container integration for local transactional email inspection
  [`docker-compose.yml:28`](../../docker-compose.yml#L28)

- Configuration script for SMTP settings, direct booking search tests, and email dispatch
  [`scripts/configure_smtp_and_test_email.php:1`](../../scripts/configure_smtp_and_test_email.php#L1)
