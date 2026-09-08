---
title: 'Story 4.2: Đồng bộ Lịch Hai Chiều OTA qua iCal/ICS'
type: 'feature'
created: '2026-09-08'
status: 'done'
review_loop_iteration: 0
context: []
baseline_commit: a64a218
---

<frozen-after-approval reason="human-owned intent — do not modify unless human renegotiates">

## Intent

**Problem:** 
1. The hotel currently has no automated connection with major OTA channels (Airbnb, Agoda, Booking.com, Traveloka). Rooms booked on the direct website must be manually closed on OTAs, and vice-versa, creating severe risks of double booking (Overbooking).
2. Third-party channel manager middlewares cost $50-$100/month, which is over-engineered and costly for a 50-room boutique hotel chain.
3. Major OTA channels natively support two-way synchronization via industry-standard iCalendar (`.ics`) calendar feeds.
4. The current `AdminOtaSyncController.php` is only a static non-functional mockup.

**Approach:**
1. **Export Feed (Chiều xuất sang OTA):**
   - Provide clean public endpoints serving RFC-5545 compliant `.ics` feeds for each branch and room type.
   - Include all active bookings (`qlo_htl_booking_detail` with `is_cancelled = 0`) to automatically block dates on Airbnb, Agoda, and Booking.com.
2. **Import Feed (Chiều nhập từ OTA về hệ thống):**
   - Provide a persistent table/storage for external OTA iCal URLs (`qlo_ota_sync_feed`).
   - Implement an iCal parser extracting `VEVENT` periods (`DTSTART`, `DTEND`, `UID`, `SUMMARY`).
   - Automatically block dates in `qlo_htl_room_disable_dates` with clear reason tags (`Đồng bộ OTA: [Kênh] #[UID]`).
3. **Admin Management Screen (`AdminOtaSyncController`):**
   - Upgrade from mockup to fully working UI:
     - Copyable iCal export URLs for each branch / room type.
     - Table of connected OTA feeds with status, last sync timestamp, and total blocked nights.
     - "Đồng bộ ngay" (Sync Now) button with instant feedback.
4. **Cron Synchronization Script:**
   - Create `scripts/sync_ota_calendars.php` for automated background sync.

## Boundaries & Constraints

**Always:**
- Use standard RFC 5545 iCalendar format compatible with Airbnb, Agoda, Booking.com, and Google Calendar.
- Respect branch permissions: receptionists only view their assigned hotel's OTA feeds; managers and admins view all.
- Idempotent import: re-running sync must update or skip existing blocks, not duplicate records.
- Fail-safe: malformed or unreachable external OTA URLs must be logged without crashing the system.

**Never:**
- Do not require paid third-party channel manager subscription APIs when standard iCal feeds fulfill the requirement.

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| OTA fetches Export URL | GET `/index.php?fc=module&module=qlovietqr&controller=ical&id_hotel=1` | Returns HTTP 200 with `text/calendar; charset=utf-8` header and valid `VCALENDAR` payload | Returns 404 if invalid branch |
| Manual Sync Trigger | Admin clicks "Đồng bộ ngay" | Fetches configured OTA feeds, parses events, records blocks in `qlo_htl_room_disable_dates`, reports count | Shows alert error if feed unreachable |
| Overbooking Prevention | Room dates blocked via OTA import | Direct booking engine (`HotelBookingDetail::getBookingData()`) returns room as unavailable for those dates | Zero collision |
| Cancellation on OTA | Event disappears from OTA iCal feed | System unblocks previously held dates | Clean removal |

</frozen-after-approval>

## Code Map

- `classes/OtaCalendarSync.php` -- Core engine for iCal generation and parsing/synchronization.
- `modules/qlovietqr/controllers/front/ical.php` -- Public endpoint serving .ics feeds to OTAs.
- `controllers/admin/AdminOtaSyncController.php` -- Upgraded operational Back Office controller.
- `admin2833tvua4/themes/default/template/controllers/ota_sync/view.tpl` -- Upgraded management template.
- `scripts/sync_ota_calendars.php` -- Background worker script.

## Tasks & Acceptance

**Execution:**
- [x] Database schema -- create `qlo_ota_sync_feed` table for storing connected OTA feed links.
- [x] Core Sync Engine -- implement `classes/OtaCalendarSync.php` (export iCal, fetch & parse iCal, block dates in DB, adjust room status).
- [x] Front Controller -- implement export endpoint accessible by OTAs (`modules/qlovietqr/controllers/front/ical.php`).
- [x] Admin Controller & View -- upgrade `AdminOtaSyncController` and `view.tpl` to provide working management UI.
- [x] Worker Script -- create `scripts/sync_ota_calendars.php` and test end-to-end.
- [x] Verification -- test export feed validation, test import of sample Airbnb iCal file, verify room is blocked on frontend booking search (available rooms drops 10 -> 9 on blocked dates, restored to 10 outside blocked dates).

**Acceptance Criteria:**
- Given an external OTA needs to sync calendar
- When accessing the hotel's export URL
- Then a valid `.ics` calendar file is returned with all current reservations
- And when an external OTA calendar feed is added to CMS Hotel
- Then clicking "Đồng bộ ngay" imports all booked dates and blocks the room in QloApps
- And the direct booking engine on the frontend prevents booking those dates

