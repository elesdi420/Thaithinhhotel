---
title: 'Story 1.2: Thiết lập Đa chi nhánh & Danh mục Phòng'
type: 'feature'
created: '2026-09-02'
status: 'done'
review_loop_iteration: 0
context: []
baseline_commit: NO_VCS
---

<frozen-after-approval reason="human-owned intent — do not modify unless human renegotiates">

## Intent

**Problem:** The default QloApps installation has only 1 demo hotel with 20 sample rooms, whereas the client requires 3 distinct branches and exactly 50 operational rooms.

**Approach:** Develop an automated PHP seeding script using native QloApps ObjectModel classes (`HotelBranchInformation`, `HotelRoomType`, `HotelRoomInformation`, `Product`) to configure the 3 branches (Downtown, Beachfront, Old Quarter) and seed the 50 rooms across standard/deluxe/suite categories with realistic VND pricing.

## Boundaries & Constraints

**Always:**
- Use QloApps native ObjectModel classes and hooks; no raw DB corruption (AD-1).
- Follow flat multi-branch architecture: all 3 branches exist in a single database separated by `id_hotel` (AD-2).
- Total rooms across 3 branches must equal exactly 50 rooms.

**Ask First:**
- Purging initial sample/demo rooms vs updating them.

**Never:**
- Do not create multi-tenant isolated databases or external API dependencies.

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| Branch Listing | Admin Back Office > Hotel Reservation > Manage Hotel | Exactly 3 active hotel branches displayed | Script validates branch creation count |
| Room Count Audit | Database query on `qlo_htl_room_information` | Exactly 50 rooms partitioned across the 3 branches | Assert total == 50; abort on mismatch |
| Frontend Branch Search | Search dropdown on Homepage | All 3 branches appear as selectable locations | Re-index search cache via QloApps |

</frozen-after-approval>

## Code Map

- `scripts/seed_branches_and_rooms.php` -- CLI PHP script bootstrapping QloApps environment and seeding the 3 branches and 50 rooms.
- `modules/hotelreservationsystem/classes/HotelBranchInformation.php` -- Native ObjectModel managing branch properties.
- `modules/hotelreservationsystem/classes/HotelRoomType.php` -- Native ObjectModel linking room products to branches.
- `modules/hotelreservationsystem/classes/HotelRoomInformation.php` -- Native ObjectModel representing individual rooms.

## Tasks & Acceptance

**Execution:**
- [x] `scripts/seed_branches_and_rooms.php` -- create automated seed script -- bootstrap QloApps and create 3 branches and 50 rooms
- [x] `docker exec seed execution` -- execute seed script inside container -- populate database with 3 branches and 50 rooms
- [x] `verification check` -- verify branch and room counts via CLI and web -- confirm 3 branches and 50 rooms exist

**Acceptance Criteria:**
- Given QloApps is installed and running
- When the branch and room seed script executes
- Then exactly 3 branches are active in the system
- And exactly 50 rooms exist across the 3 branches with room numbers and room types assigned
- And the calendar view in the admin back office reflects the rooms

## Design Notes

Room distribution:
- Branch 1 (Cơ sở 1 - Trung tâm): 20 rooms (10 Deluxe @ 800,000 VND, 10 Suite @ 1,500,000 VND)
- Branch 2 (Cơ sở 2 - Bờ biển): 15 rooms (8 Standard @ 500,000 VND, 7 Deluxe @ 800,000 VND)
- Branch 3 (Cơ sở 3 - Phố cổ): 15 rooms (8 Standard @ 500,000 VND, 7 Suite @ 1,500,000 VND)
Total: 50 rooms. Currency set to VND format.

## Verification

**Commands:**
- `docker exec qloapps_db mariadb -u qloapps -pqloappspass qloapps -e "SELECT count(*) FROM qlo_htl_branch_info WHERE active = 1;"` -- expected: `3`
- `docker exec qloapps_db mariadb -u qloapps -pqloappspass qloapps -e "SELECT count(*) FROM qlo_htl_room_information;"` -- expected: `50`

## Suggested Review Order

**Branch & Room Seeding**

- Automated bootstrap and seeding script for 3 hotel branches and 50 operational rooms
  [`scripts/seed_branches_and_rooms.php:1`](../../scripts/seed_branches_and_rooms.php#L1)
