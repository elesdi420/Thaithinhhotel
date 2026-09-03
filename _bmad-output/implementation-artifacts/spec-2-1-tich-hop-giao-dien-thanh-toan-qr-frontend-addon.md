---
title: 'Story 2.1: Tích hợp giao diện thanh toán QR (Frontend Addon)'
type: 'feature'
created: '2026-09-02'
status: 'done'
review_loop_iteration: 0
context: []
baseline_commit: NO_VCS
---

<frozen-after-approval reason="human-owned intent — do not modify unless human renegotiates">

## Intent

**Problem:** Guests cannot pay via bank QR transfer during online checkout; the system currently only has standard wire transfer and check stubs.

**Approach:** Build a native QloApps payment module `modules/qlovietqr/` extending `PaymentModule`. The module hooks into `payment` (checkout option) and `paymentReturn` (order confirmation screen), generating dynamic VietQR images (`https://img.vietqr.io/image/...`) with exact order amount, bank details, and transfer memo containing the order reference.

## Boundaries & Constraints

**Always:**
- Build strictly as a self-contained module in `modules/qlovietqr/`; zero core file modifications (AD-1, AD-3).
- Dynamic QR code generation uses direct VietQR image URL syntax (`img.vietqr.io`); no third-party heavyweight SDK required.
- Bank configuration (Bank ID, Account Number, Account Name) configurable in Back Office or `.env`.

**Ask First:**
- Adding specific corporate bank credentials if available.

**Never:**
- Do not patch QloApps checkout controllers or theme core files.

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| Checkout Payment Option | Guest at checkout payment step | "Thanh toán Chuyển khoản QR (VietQR)" option displayed | Currency check VND |
| Dynamic QR Display | Guest confirms QR payment | Order created in Awaiting Payment status; Dynamic QR code rendered with exact amount and Order Reference memo | Default placeholder if image service offline |
| Module Configuration | Admin in Back Office > Modules > VietQR | Form to configure Bank ID (e.g. MB, VCB), Account Number, Account Name | Validation on required fields |

</frozen-after-approval>

## Code Map

- `modules/qlovietqr/qlovietqr.php` -- Main module class inheriting `PaymentModule`. Implements `install()`, `uninstall()`, `hookPayment()`, `hookPaymentReturn()`, `getContent()`.
- `modules/qlovietqr/controllers/front/payment.php` -- Front controller displaying payment review and QR details.
- `modules/qlovietqr/controllers/front/validation.php` -- Validates order in `PS_OS_AWAITING_PAYMENT` state and redirects to confirmation.
- `modules/qlovietqr/views/templates/hook/payment.tpl` -- Smarty template rendering the VietQR payment button at checkout.
- `modules/qlovietqr/views/templates/hook/payment_return.tpl` -- Smarty template rendering dynamic QR code, bank info, and transfer syntax.
- `modules/qlovietqr/logo.png` -- VietQR payment badge icon.

## Tasks & Acceptance

**Execution:**
- [x] `modules/qlovietqr/qlovietqr.php` -- create payment module class -- implement install/uninstall, hooks, and configuration
- [x] `modules/qlovietqr/controllers/front/` -- create payment & validation controllers -- handle checkout execution and order creation
- [x] `modules/qlovietqr/views/templates/hook/` -- create Smarty templates -- render payment button and dynamic QR code screen
- [x] `qlovietqr module installation` -- install and activate module via QloApps Module API -- register payment hooks
- [x] `checkout verification` -- test checkout flow with VietQR -- verify dynamic QR image URL generated with amount and reference

**Acceptance Criteria:**
- Given a guest with rooms in cart at checkout
- When selecting "Thanh toán Chuyển khoản QR (VietQR)"
- Then an order is created and the confirmation screen displays the dynamic VietQR code
- And the QR code image URL contains the exact order amount and order reference in transfer memo

## Design Notes

VietQR QuickLink format:
`https://img.vietqr.io/image/{$bank_id}-{$account_no}-compact2.png?amount={$amount}&addInfo={$memo}&accountName={$account_name}`
Default bank settings:
- Bank ID: `MB` (MBBank - Ngân hàng Quân Đội)
- Account Number: `0901234567`
- Account Name: `CMS HOTEL PMS`

## Verification

**Commands:**
- `docker exec qloapps_web php -r "require_once 'config/config.inc.php'; echo Module::isInstalled('qlovietqr') ? 'INSTALLED' : 'NOT INSTALLED';"` -- expected: `INSTALLED`
- `curl -s http://localhost:8080/module/qlovietqr/payment | grep -i "vietqr"` -- expected: 200 or redirect to cart

## Suggested Review Order

**VietQR Payment Module Implementation**

- Native payment module definition, hooks, and configuration form
  [`modules/qlovietqr/qlovietqr.php:1`](../../modules/qlovietqr/qlovietqr.php#L1)

- Frontend payment step controller and order validation controller
  [`modules/qlovietqr/controllers/front/payment.php:1`](../../modules/qlovietqr/controllers/front/payment.php#L1)
  [`modules/qlovietqr/controllers/front/validation.php:1`](../../modules/qlovietqr/controllers/front/validation.php#L1)

- Smarty templates for checkout option, execution page, and dynamic QR display
  [`modules/qlovietqr/views/templates/hook/payment.tpl:1`](../../modules/qlovietqr/views/templates/hook/payment.tpl#L1)
  [`modules/qlovietqr/views/templates/front/payment_execution.tpl:1`](../../modules/qlovietqr/views/templates/front/payment_execution.tpl#L1)
  [`modules/qlovietqr/views/templates/hook/payment_return.tpl:1`](../../modules/qlovietqr/views/templates/hook/payment_return.tpl#L1)
