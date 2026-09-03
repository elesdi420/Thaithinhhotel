# Epic 1 Context: Cấu hình QloApps "Out-of-the-box"

<!-- Compiled from planning artifacts. Edit freely. Regenerate with compile-epic-context if planning docs change. -->

## Goal

Deploy open-source QloApps (PrestaShop fork) and configure it to manage 50 rooms across 3 hotel branches with multi-branch segregation, staff roles, and direct booking engine with confirmation email. This fulfills ~80% of system requirements without writing custom application code.

## Stories

- Story 1.1: Triển khai mã nguồn QloApps
- Story 1.2: Thiết lập Đa chi nhánh & Danh mục Phòng
- Story 1.3: Cấu hình Phân quyền Nhân viên
- Story 1.4: Kích hoạt Booking Engine & Email

## Requirements & Constraints

- Core PMS functionality: Room & category management, real-time room status grid, check-in/check-out bookings, guest CRM with manual VIP tagging.
- Branch segregation: 3 physical hotel branches, total 50 rooms. Receptionist staff restricted to view/manage only their assigned branch. Managers have system-wide view.
- Direct booking: Responsive web interface for guests to view room availability and basic information.
- Transactional email: Send booking confirmation email on successful booking creation.
- Zero overbooking: Room state must be consistently tracked.

## Technical Decisions

- Architecture Pattern: Modular Monolith on QloApps (PHP >8.0 <8.5, MySQL/MariaDB 8.0+).
- Invariant (AD-1): No core patching. Keep QloApps core clean for maintenance/upgrades.
- Invariant (AD-2): Flat multi-branch model using QloApps native Hotel entity (`id_hotel`). Single shared database; no complex multi-tenant database isolation.
- Invariant (AD-4): Docker Compose single-server deployment (`nginx`, `php-fpm`, `mysql`). Single VPS.

## Cross-Story Dependencies

- Story 1.1 provides the running containerized environment and DB connection for all subsequent stories.
- Story 1.2 creates the branch and room entities required by Story 1.3 (staff role assignment) and Story 1.4 (guest booking on frontend).
