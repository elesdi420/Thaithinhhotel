---
title: 'Story 4.1: Chuẩn hóa Tiện ích 3 Cơ sở & Gỡ widget thừa'
type: 'feature'
created: '2026-09-07'
status: 'done'
review_loop_iteration: 0
context: []
baseline_commit: a64a218
---

<frozen-after-approval reason="human-owned intent — do not modify unless human renegotiates">

## Intent

**Problem:** 
1. Branch 1 lists fake/demo amenities inherited from QloApps sample data ("Casino", "Amphitheatre", "DiscoTheatre", "Camel Ride"...), while Branch 2 and Branch 3 have zero amenities. This confuses guests and creates legal/operational risks (Action Item AI-5).
2. The homepage features block (`qlo_htl_features_block_data_lang`) still contains raw English demo marketing copy ("World class chefs", "Gym & Spa").
3. A dummy chat widget mockup was added to the footer for a previous demo, which the client explicitly requested to drop ("bỏ qua phần chatbot đi").

**Approach:**
1. Clean up `qlo_htl_branch_amenity` and configure standard, realistic, Vietnamese-localized hotel amenities (Wifi miễn phí tốc độ cao, Điều hòa 2 chiều, Smart TV, Ăn sáng buffet, Dọn phòng hàng ngày, Minibar, Lễ tân 24/7, Bãi đỗ xe) across all 3 branches (`id_hotel = 1, 2, 3`).
2. Update `qlo_htl_features_block_data_lang` with realistic Vietnamese hotel highlights (Phòng nghỉ tiện nghi, Vị trí đắc địa, Dịch vụ chu đáo, Thanh toán VietQR).
3. Remove dummy chatbot widget inclusion (`chat-widget-mockup.tpl`) from `themes/hotel-reservation-theme/footer.tpl`.

## Boundaries & Constraints

**Always:**
- Use QloApps native amenity data structures (`qlo_htl_amenity`, `qlo_htl_amenity_lang`, `qlo_htl_branch_amenity`).
- Localize all amenity names and feature descriptions into Vietnamese (`id_lang = 2`) with English fallback (`id_lang = 1`).
- Ensure all 3 branches have consistent, realistic amenities.

**Never:**
- Do not keep unverified or speculative claims (Casino, Amphitheatre).
- Do not render disconnected chatbot mockups on user-facing pages.

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| Branch 1 Detail View | Visitor views Branch 1 page | Realistic amenities displayed in Vietnamese (Wifi, AC, TV...); zero Casino/Disco | Fallback to default amenities |
| Branch 2 & 3 Detail View | Visitor views Branch 2 / 3 | Same realistic amenities displayed properly | No empty amenity sections |
| Frontend Footer | Any page footer loaded | No floating dummy chat widget on bottom right | Clean layout |

</frozen-after-approval>

## Code Map

- `scripts/normalize_branch_amenities.php` -- Populates realistic amenities and assigns to all 3 branches in DB.
- `themes/hotel-reservation-theme/footer.tpl` -- Removes dummy chat widget mockup.

## Tasks & Acceptance

**Execution:**
- [x] `scripts/normalize_branch_amenities.php` -- clean up fake amenities and assign realistic amenities to 3 branches in DB.
- [x] `themes/hotel-reservation-theme/footer.tpl` -- remove chat widget mockup include.
- [x] `docker/db/init.sql` -- dump updated database so fresh deploy includes standardized amenities.
- [x] `verification check` -- inspect frontend branch pages, home features block, and footer.

**Acceptance Criteria:**
- Given a guest visits any of the 3 hotel branch pages
- When viewing the amenities section
- Then realistic amenities in Vietnamese are rendered (Wifi, Điều hòa, Smart TV...)
- And no demo facilities (Casino, Amphitheatre) appear
- And the homepage features block is fully translated in Vietnamese
- And the footer no longer displays the dummy chat widget

