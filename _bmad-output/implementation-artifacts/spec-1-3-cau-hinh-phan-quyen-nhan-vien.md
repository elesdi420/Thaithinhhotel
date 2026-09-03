---
title: 'Story 1.3: Cấu hình Phân quyền Nhân viên'
type: 'feature'
created: '2026-09-02'
status: 'done'
review_loop_iteration: 0
context: []
baseline_commit: NO_VCS
---

<frozen-after-approval reason="human-owned intent — do not modify unless human renegotiates">

## Intent

**Problem:** Currently only 1 SuperAdmin account exists. Receptionist staff need individual accounts per branch, restricted to only view and process bookings for their assigned hotel, while hiding global revenue reports.

**Approach:** Develop an automated configuration script creating 3 Receptionist profiles (one per branch) and 1 Chain Manager profile, configuring granular tab permissions in `qlo_access` (granting room bookings/orders/customers; denying revenue stats/dashboard), setting hotel segregation in `qlo_htl_access`, and generating active employee accounts for each role.

## Boundaries & Constraints

**Always:**
- Use QloApps native permission system (`Profile`, `Employee`, `qlo_access`, `qlo_htl_access`) (AD-2).
- Restrict branch access so Branch 1 staff cannot access Branch 2 or 3 data.
- Hide `AdminStats` and `AdminDashboard` from receptionist profiles.

**Ask First:**
- Custom password policies beyond standard minimum 8 characters.

**Never:**
- Do not bypass QloApps authentication or build custom session middleware.

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| Branch 1 Receptionist Access | `reception1@hotel.local` logged in | Can see rooms and bookings for Hotel 1 ONLY; Hotel 2 and 3 omitted | `htl_access` query filter enforced |
| Revenue Dashboard Block | Receptionist accesses `AdminStats` / `AdminDashboard` | Denied / Access Forbidden | `qlo_access` view = 0 |
| Chain Manager Access | `manager@hotel.local` logged in | Can view all 3 branches and consolidated reports | Full access to all 3 hotels |

</frozen-after-approval>

## Code Map

- `scripts/seed_roles_and_employees.php` -- Script setting up profiles, permissions, hotel scoping, and employee accounts.
- `classes/Profile.php` -- Native ObjectModel managing user roles and permission tables.
- `classes/Employee.php` -- Native ObjectModel managing staff credentials and profiles.
- `modules/hotelreservationsystem/classes/HotelBranchInformation.php` -- Manages `qlo_htl_access` per profile.

## Tasks & Acceptance

**Execution:**
- [x] `scripts/seed_roles_and_employees.php` -- create automated role and employee seed script -- create profiles, assign hotel access, set tab permissions, create employees
- [x] `docker exec role execution` -- run script inside container -- seed receptionist and manager accounts
- [x] `permission verification` -- verify DB permissions and hotel scoping -- assert receptionists cannot see other branches or stats

**Acceptance Criteria:**
- Given accounts for 3 receptionists and 1 manager are seeded
- When Receptionist 1 queries accessible hotels via `HotelBranchInformation::getProfileAccessedHotels`
- Then only Hotel 1 is returned with active access
- And `AdminStats` / `AdminDashboard` are denied (view = 0)
- And Chain Manager has access to all 3 hotels and reports

## Design Notes

Accounts created:
- `reception1@hotel.local` / `Password123!` (Profile: Lễ tân - Cơ sở 1, Hotel 1 only)
- `reception2@hotel.local` / `Password123!` (Profile: Lễ tân - Cơ sở 2, Hotel 2 only)
- `reception3@hotel.local` / `Password123!` (Profile: Lễ tân - Cơ sở 3, Hotel 3 only)
- `manager@hotel.local` / `Password123!` (Profile: Quản lý chuỗi, All hotels)

## Verification

**Commands:**
- `docker exec qloapps_db mariadb -u qloapps -pqloappspass qloapps -e "SELECT ha.id_profile, pl.name, ha.id_hotel, ha.access FROM qlo_htl_access ha JOIN qlo_profile_lang pl ON ha.id_profile = pl.id_profile WHERE pl.id_lang = 1;"` -- expected: correct hotel mapping per profile
- `docker exec qloapps_db mariadb -u qloapps -pqloappspass qloapps -e "SELECT count(*) FROM qlo_employee WHERE active = 1;"` -- expected: `5` (1 admin + 3 receptionists + 1 manager)

## Suggested Review Order

**Role & Employee Seeding**

- Automated seed script setting up 4 profiles, hotel scoping in htl_access, tab permissions in access, and staff accounts
  [`scripts/seed_roles_and_employees.php:1`](../../scripts/seed_roles_and_employees.php#L1)
