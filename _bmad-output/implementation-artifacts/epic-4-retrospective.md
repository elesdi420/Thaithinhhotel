# Retrospective: Epic 4 - Trải nghiệm Nâng cao & Tự động hóa Vận hành

**Ngày hoàn thành:** 08/09/2026  
**Trạng thái:** Hoàn tất (Done)

---

## 1. Tổng kết Phạm vi & Thành quả (What We Delivered)

### Story 4.1: Chuẩn hóa Tiện ích 3 Cơ sở & Gỡ Widget Demo
- **Loại bỏ Widget Demo:** Đã gỡ bỏ toàn bộ widget chat mockup tĩnh ở footer frontend theo chỉ đạo dứt khoát của ban quản trị ("bỏ qua phần chatbot đi"), triệt tiêu sự hiểu lầm của khách hàng.
- **Làm sạch Tiện ích Phi thực tế (AI-5):** Gỡ bỏ các tiện ích demo như Casino, Trường đấu La Mã (Amphitheatre), Vũ trường (DiscoTheatre), Cưỡi lạc đà...
- **Bổ sung 10 Tiện ích Thực tế:** Tạo và kích hoạt đồng bộ 10 tiện ích khách sạn Việt Nam thực tế cho cả 3 chi nhánh (Wifi tốc độ cao, Thang máy, Điều hòa 2 chiều, Lễ tân 24/7, Dọn phòng hàng ngày, Chỗ để xe, Bình nóng lạnh, Smart TV & Truyền hình cáp, Trà & Cà phê miễn phí, Máy sấy tóc).
- **White-label & Localize:** Thay thế hoàn toàn "Hotel Prime" thành "CMS Hotel" trong cơ sở dữ liệu và cấu hình hệ thống; dọn dẹp cache template Smarty.

### Story 4.2: Đồng bộ Lịch Hai Chiều OTA (Airbnb, Agoda, Booking.com) qua chuẩn RFC 5545 iCal/ICS
- **Chiều Xuất (Outbound Export Feed):**
  - Cung cấp endpoint công khai chuẩn RFC 5545 tại `/index.php?fc=module&module=qlovietqr&controller=ical&id_hotel={id}`.
  - Phục vụ header `text/calendar; charset=utf-8` chứa đầy đủ sự kiện VEVENT từ đơn đặt phòng (`qlo_htl_booking_detail`) và ngày khóa phòng (`qlo_htl_room_disable_dates`).
- **Chiều Nhập (Inbound Import Sync):**
  - Tạo bảng `qlo_ota_sync_feed` quản lý các kết nối iCal từ Airbnb, Agoda, Booking.com.
  - Engine `classes/OtaCalendarSync.php`: Tải và parse feed `.ics` từ xa, trích xuất sự kiện `VEVENT`, tự động khóa ngày vào `qlo_htl_room_disable_dates`, đồng thời cập nhật `id_status = 3` (STATUS_TEMPORARY_INACTIVE) trong `qlo_htl_room_information`.
  - Kiểm thử overbooking: Khi có booking OTA, số phòng khả dụng trên trang đặt phòng lập tức giảm từ 10 xuống 9 cho các ngày bị trùng lịch, và tự động hồi phục đủ 10 phòng ngoài các ngày đó.
- **Giao diện Quản trị Vận hành (Back Office):**
  - Chuyển đổi giao diện tĩnh `AdminOtaSyncController` thành trang vận hành thực tế: Thêm URL feed, bấm "Đồng bộ ngay", xem lịch sử thời gian và số ngày khóa đã sync, xóa feed giải phóng ngày khóa.
- **Tự động hóa Định kỳ (Background Cron):**
  - Kịch bản `scripts/sync_ota_calendars.php` hỗ trợ cronjob chạy định kỳ (mỗi 15 phút) trên server.

---

## 2. Bài học Kỹ thuật (Key Technical Learnings)
- **QloApps Room Availability Architecture:** QloApps phân biệt phòng khả dụng dựa trên `htl_booking_detail` và `htl_room_disable_dates`. Tuy nhiên, bảng `htl_room_disable_dates` chỉ được engine tính toán khi phòng đó có trạng thái `id_status = 3` (`STATUS_TEMPORARY_INACTIVE`). Việc phối hợp cập nhật `id_status = 3` khi phòng có lịch khóa và tự động trả lại `id_status = 1` (`STATUS_ACTIVE`) khi hết lịch khóa giúp tận dụng 100% logic lõi nguyên bản của QloApps mà không cần sửa core hay can thiệp bẩn vào database.
- **MariaDB `Db::getRow()` Query Syntax:** Hàm `getRow()` của PrestaShop tự động nối thêm `LIMIT 1`. Do đó các câu lệnh SQL truyền vào `getRow()` không được chứa từ khóa `LIMIT 1` để tránh lỗi cú pháp `LIMIT 1 LIMIT 1`.

---

## 3. Trạng thái Sẵn sàng Đưa vào Sản xuất (Production Readiness)
- Đã export sạch toàn bộ schema và dữ liệu cập nhật ra `docker/db/init.sql`.
- 100% mã nguồn không sử dụng thư viện bên ngoài nặng nề, hoàn toàn theo triết lý Tối giản - Ổn định - Tự nhiên (YAGNI / Ponytail).
