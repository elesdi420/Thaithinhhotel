---
title: 'Story 3.1: Nhúng Bản đồ Vị trí Chi nhánh'
type: 'feature'
created: '2026-09-02'
status: 'done'
review_loop_iteration: 0
context: []
baseline_commit: NO_VCS
---

<frozen-after-approval reason="human-owned intent — do not modify unless human renegotiates">

## Intent

**Problem:** Guests need to visually see where each hotel branch is located on an interactive map to make booking decisions and find directions, without incurring Google Maps API fees or billing lockouts.

**Approach:** Implement AD-5 (Google Maps iframe embed `output=embed` without API key). Populate accurate geographical coordinates (Lat/Lng) for all 3 hotel branches in the database, and enhance `themes/hotel-reservation-theme/our-properties.tpl` and `contact-form.tpl` to embed responsive, interactive Google Maps iframes for each hotel branch.

## Boundaries & Constraints

**Always:**
- Use zero-cost Google Maps iframe embed (`https://www.google.com/maps?q=...&output=embed`) (AD-5).
- Ensure iframes are responsive (`width: 100%`, `height: 220px`, `border: 0`, `loading: lazy`).
- Fallback gracefully to text address search query if coordinates are unavailable.

**Ask First:**
- Purchasing commercial map APIs (e.g. Mapbox / Google Maps Places API).

**Never:**
- Do not require Google Maps JavaScript API keys with credit card billing.

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| Our Properties Page | Guest visits `/our-properties` | Each branch card renders an interactive Google Maps embed pinpointing the hotel | Fallback to encoded address |
| Contact Us Page | Guest visits `/contact-us` | Branches in the "Our Hotels" section include the map embed | Lazy load iframe |
| Mobile View | Screen width < 768px | Map iframe adapts to 100% container width without overflow | Responsive CSS wrapper |

</frozen-after-approval>

## Code Map

- `scripts/update_hotel_coordinates.php` -- Updates Lat/Lng coordinates for all 3 hotel branches in `qlo_htl_branch_info`.
- `themes/hotel-reservation-theme/our-properties.tpl` -- Enhances hotel card with responsive Google Maps iframe embed.
- `themes/hotel-reservation-theme/contact-form.tpl` -- Enhances contact page hotel list with Google Maps embed.

## Tasks & Acceptance

**Execution:**
- [x] `scripts/update_hotel_coordinates.php` -- populate coordinates for 3 branches -- set Lat/Lng in qlo_htl_branch_info
- [x] `themes/hotel-reservation-theme/our-properties.tpl` -- add Google Maps iframe embed -- render interactive map per branch card
- [x] `themes/hotel-reservation-theme/contact-form.tpl` -- add Google Maps iframe embed -- render interactive map on contact page
- [x] `verification check` -- inspect rendered HTML and iframe URLs -- verify 0 API key required and valid map coordinates

**Acceptance Criteria:**
- Given a guest browses the hotel list or contact page
- When viewing any of the 3 hotel branches
- Then an interactive Google Maps iframe is displayed showing the branch location
- And no paid Google Maps JavaScript API key is loaded

## Design Notes

Embed iframe format:
`<iframe width="100%" height="220" style="border:0; border-radius: 6px;" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps?q={$hotel['latitude']},{$hotel['longitude']}&hl=vi&z=15&output=embed"></iframe>`

Branch coordinates:
- Cơ sở 1 (TP.HCM): Lat `10.77250000`, Lng `106.69800000` (123 Lê Lợi, Q1)
- Cơ sở 2 (Đà Nẵng): Lat `16.06010000`, Lng `108.24350000` (456 Võ Nguyên Giáp, Sơn Trà)
- Cơ sở 3 (Hà Nội): Lat `21.03070000`, Lng `105.85040000` (789 Hàng Trống, Hoàn Kiếm)

## Verification

**Commands:**
- `curl -s http://localhost:8080/our-properties | grep -i "maps?q="` -- expected: iframe elements found
- `curl -s http://localhost:8080/contact-us | grep -i "maps?q="` -- expected: iframe elements found

## Suggested Review Order

**Google Maps Iframe Integration**

- Hotel branch location map embed on properties listing
  [`themes/hotel-reservation-theme/our-properties.tpl:80`](../../themes/hotel-reservation-theme/our-properties.tpl#L80)

- Hotel branch location map embed on contact page
  [`themes/hotel-reservation-theme/contact-form.tpl:230`](../../themes/hotel-reservation-theme/contact-form.tpl#L230)

- Script populating real Lat/Lng coordinates for 3 branches
  [`scripts/update_hotel_coordinates.php:1`](../../scripts/update_hotel_coordinates.php#L1)
