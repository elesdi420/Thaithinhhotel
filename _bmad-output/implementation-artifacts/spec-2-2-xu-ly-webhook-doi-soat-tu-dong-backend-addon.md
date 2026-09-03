---
title: 'Story 2.2: Xử lý Webhook đối soát tự động (Backend Addon)'
type: 'feature'
created: '2026-09-02'
status: 'done'
review_loop_iteration: 0
context: []
baseline_commit: NO_VCS
---

<frozen-after-approval reason="human-owned intent — do not modify unless human renegotiates">

## Intent

**Problem:** Receptionists have to manually verify bank statements to confirm room bookings paid via bank transfer, creating delay and manual friction.

**Approach:** Build a dedicated webhook front controller `modules/qlovietqr/controllers/front/webhook.php`. It receives transaction webhooks from payment gateways / bank notifications, authenticates via a secure token/header, matches the transfer memo or reference to the pending order, verifies the amount, updates the order status to `Payment Accepted` (`PS_OS_PAYMENT_ACCEPTED`), and triggers automated confirmation email dispatch.

## Boundaries & Constraints

**Always:**
- Self-contained inside `modules/qlovietqr/` (AD-1, AD-3).
- Strict idempotency: if an order is already paid, return 200 OK without re-triggering state changes or emails.
- Validate transfer amount against `Order->total_paid`.
- Authenticate requests with `VIETQR_WEBHOOK_SECRET`.

**Ask First:**
- Custom signature verification algorithms specific to an individual bank gateway.

**Never:**
- Do not accept unauthenticated webhook payloads in production.
- Do not mark orders paid if amount is insufficient.

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| Valid Payment Webhook | JSON with matching reference, valid amount, correct token | Order updated to `PS_OS_PAYMENT_ACCEPTED`; confirmation email sent; JSON 200 returned | Log success |
| Duplicate Webhook | Webhook re-sent for already paid order | Idempotent HTTP 200 with `status: already_paid` | No duplicate email |
| Invalid Token / Secret | Bad `X-Webhook-Secret` or `?token=` | HTTP 401 Unauthorized | Reject immediately |
| Underpaid Amount | Received amount < order total | Order left pending, HTTP 422 with `error: underpaid` | Alert logged |
| Unknown Reference | Memo has no valid order reference | HTTP 404 with `error: order_not_found` | Log unrecognized transfer |

</frozen-after-approval>

## Code Map

- `modules/qlovietqr/controllers/front/webhook.php` -- Front controller endpoint handling `POST /index.php?fc=module&module=qlovietqr&controller=webhook`.
- `modules/qlovietqr/qlovietqr.php` -- Registers webhook configuration (`VIETQR_WEBHOOK_SECRET`) and order history helper.
- `scripts/test_webhook_reconciliation.php` -- Verification script simulating an end-to-end booking order and webhook reconciliation.

## Tasks & Acceptance

**Execution:**
- [x] `modules/qlovietqr/controllers/front/webhook.php` -- create webhook front controller -- parse JSON, authenticate secret, match order, update state, trigger email
- [x] `modules/qlovietqr/qlovietqr.php` -- update module config for webhook secret -- add settings to Back Office form
- [x] `scripts/test_webhook_reconciliation.php` -- create automated webhook test script -- create pending test order, post webhook, assert state transition to Payment Accepted
- [x] `verification check` -- verify database order state and Mailpit email -- confirm booking confirmed and email received

**Acceptance Criteria:**
- Given a booking in `Chờ thanh toán VietQR` status
- When a valid webhook is posted with correct order reference and amount
- Then the order state transitions to `Payment accepted` (ID 2)
- And a confirmation email is automatically sent to the customer
- And duplicate webhooks are handled idempotently

## Design Notes

Webhook URL format:
`http://localhost:8080/index.php?fc=module&module=qlovietqr&controller=webhook&token=<SECRET>`
Payload flexibility:
Accepts both standard VietQR/bank gateway JSON (`{reference, amount, transaction_id}`) and PayOS format (`{data: {description, amount}}`).

## Verification

**Commands:**
- `docker exec qloapps_web php scripts/test_webhook_reconciliation.php` -- expected: `Webhook Reconciliation PASS: Order state updated to Payment accepted`
- `curl -s http://localhost:8025/api/v1/messages | grep -i "BOOK-"` -- expected: email captured in Mailpit

## Suggested Review Order

**Webhook Reconciliation Addon**

- Dedicated webhook endpoint handling authentication, memo parsing, amount check, and state transition
  [`modules/qlovietqr/controllers/front/webhook.php:1`](../../modules/qlovietqr/controllers/front/webhook.php#L1)

- End-to-end simulation and verification script testing webhook reconciliation, MariaDB state check, and idempotency
  [`scripts/test_webhook_reconciliation.php:1`](../../scripts/test_webhook_reconciliation.php#L1)
