# Thaithinh Hotel PMS — Hệ Thống Quản Trị Khách Sạn Chuỗi & Đặt Phòng Trực Tuyến

Hệ thống Quản lý Khách sạn Chuỗi (Property Management System - PMS) kết hợp Booking Engine trực tuyến, hỗ trợ thanh toán tự động qua VietQR và quản lý phân quyền đa chi nhánh.

---

## 🌟 Tính Năng Nổi Bật

1. **Quản lý Đa Chi Nhánh & 50 Phòng Tiêu Chuẩn:**
   - **Cơ sở 1 - Trung tâm:** 20 phòng (Deluxe, Suite).
   - **Cơ sở 2 - Bờ biển:** 15 phòng (Standard, Deluxe).
   - **Cơ sở 3 - Phố cổ:** 15 phòng (Standard, Suite).
   - Đơn vị tiền tệ: VNĐ (500.000đ - 1.500.000đ/đêm).
2. **Phân Quyền & Cách Ly Dữ Liệu:**
   - Lễ tân từng chi nhánh chỉ thấy lịch phòng và đơn đặt của cơ sở được phân công.
   - Ẩn hoàn toàn doanh thu và báo cáo tài chính đối với tài khoản lễ tân.
   - Tài khoản Quản lý chuỗi có góc nhìn tổng hợp và toàn quyền kiểm soát.
3. **Thanh Toán Tự Động VietQR (`modules/qlovietqr`):**
   - Tự động sinh mã QR chuyển khoản ngân hàng động (`img.vietqr.io`) kèm đúng số tiền và mã đơn hàng.
   - Webhook đối soát tự động: cập nhật trạng thái đơn sang *Đã thanh toán (Payment accepted)* ngay khi tiền vào tài khoản.
   - Tự động gửi email xác nhận đặt phòng tới khách hàng.
4. **Bản Đồ Vị Trí Chi Nhánh (Google Maps):**
   - Nhúng Google Maps iframe tương thích di động theo tọa độ thực tế của từng chi nhánh (0đ chi phí API key).
5. **Tối Ưu Tải Trang & SEO Di Động:**
   - Hỗ trợ đầy đủ thẻ Open Graph (Zalo, Facebook preview), Twitter Cards và viewport mobile responsive.
   - Kích hoạt cơ chế CCC (Combine, Compress, Cache) tối ưu tốc độ tải trang.
6. **Giao Diện Tiếng Việt & Thương Hiệu Riêng (White-label):**
   - Toàn bộ giao diện Frontend và Back Office được bản địa hóa tiếng Việt.
   - Gỡ bỏ hoàn toàn dấu vết thương hiệu bên thứ 3, mang phong cách phần mềm PMS chuyên nghiệp.

---

## 🚀 Khởi Động Nhanh (Docker Compose)

### Yêu cầu:
- Docker & Docker Compose

### Cài đặt và chạy:
```bash
# 1. Khởi tạo tệp môi trường
cp .env.example .env

# 2. Khởi chạy toàn bộ dịch vụ (Web, Database, Mailpit)
docker compose up -d
```

Hệ thống sẽ tự động import cơ sở dữ liệu mẫu (`docker/db/init.sql`) chứa sẵn 3 chi nhánh, 50 phòng, phân quyền và cấu hình thanh toán.

---

## 🔗 Đường Dẫn Truy Cập

- **Website Đặt phòng (Frontend):** [http://localhost:8080/](http://localhost:8080/)
- **Trang Quản trị (Back Office):** [http://localhost:8080/admin/](http://localhost:8080/admin/)
- **Hộp thư kiểm tra Email (Mailpit):** [http://localhost:8025/](http://localhost:8025/)

---

## 🔑 Tài Khoản Thử Nghiệm

> Mật khẩu chung cho tất cả tài khoản quản trị: `Password123!`

| Vai trò | Email đăng nhập | Quyền hạn |
|---------|-----------------|-----------|
| **SuperAdmin** | `admin@hotel.local` | Toàn quyền cấu hình hệ thống |
| **Quản lý chuỗi** | `manager@hotel.local` | Xem toàn bộ 3 chi nhánh & báo cáo doanh thu |
| **Lễ tân Cơ sở 1** | `reception1@hotel.local` | Chỉ quản lý phòng Cơ sở 1 (TP.HCM) |
| **Lễ tân Cơ sở 2** | `reception2@hotel.local` | Chỉ quản lý phòng Cơ sở 2 (Đà Nẵng) |
| **Lễ tân Cơ sở 3** | `reception3@hotel.local` | Chỉ quản lý phòng Cơ sở 3 (Hà Nội) |

**Tài khoản khách hàng test:**
- Email: `guest.webhook@example.com`
- Mật khẩu: `Password123!`

---

## 🛠️ Cấu Trúc Dự Án

```
├── docker-compose.yml              # Cấu hình container PHP 8.2, MariaDB 10.11, Mailpit
├── Dockerfile                      # Image Apache + PHP mở rộng cho PMS
├── docker/db/init.sql              # Bản dump CSDL khởi tạo sẵn 3 cơ sở & 50 phòng
├── modules/qlovietqr/              # Module thanh toán VietQR & Webhook tự động
├── scripts/                        # Bộ script bảo trì, phân quyền, SEO và chuyển ngôn ngữ
├── themes/hotel-reservation-theme/ # Giao diện đặt phòng tùy biến, bản đồ Google Maps & SEO
└── _bmad-output/                   # Tài liệu đặc tả kỹ thuật và lịch sử triển khai
```
