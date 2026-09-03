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
├── _bmad-output/                   # Toàn bộ tài liệu PRD, Architecture và Specs
├── _bmad/                          # Khung cấu hình phương pháp luận BMAD
└── .agents/ / .claude/ / .agent/   # Kỹ năng và workflows phục vụ AI Co-Dev
```

---

## 📚 Tài Liệu Kỹ Thuật Phục Vụ Co-Dev (Documentation & Architecture)

Toàn bộ tài liệu phân tích, kiến trúc và đặc tả chi tiết đều được lưu trữ trực tiếp trong repository để phục vụ làm việc nhóm (co-dev) hoặc bàn giao:

### 1. Kế Hoạch & Kiến Trúc
- **Yêu cầu sản phẩm (PRD):** [`_bmad-output/planning-artifacts/prds/prd-CMS-2026-08-30/prd.md`](_bmad-output/planning-artifacts/prds/prd-CMS-2026-08-30/prd.md)
- **Kiến trúc hệ thống (Architecture Spine):** [`_bmad-output/planning-artifacts/architecture/architecture-CMS-2026-09-02/ARCHITECTURE-SPINE.md`](_bmad-output/planning-artifacts/architecture/architecture-CMS-2026-09-02/ARCHITECTURE-SPINE.md)
- **Phân rã tính năng (Epics Breakdown):** [`_bmad-output/planning-artifacts/epics.md`](_bmad-output/planning-artifacts/epics.md)
- **Bảng theo dõi tiến độ (Sprint Status):** [`_bmad-output/implementation-artifacts/sprint-status.yaml`](_bmad-output/implementation-artifacts/sprint-status.yaml)

### 2. Chi Tiết Đặc Tả Kỹ Thuật (Story Specs)
- **Epic 1: Khởi tạo QloApps & Vận hành Đa cơ sở**
  - [Spec 1.1: Triển khai Docker QloApps](_bmad-output/implementation-artifacts/spec-1-1-trien-khai-ma-nguon-qloapps.md)
  - [Spec 1.2: Thiết lập Đa chi nhánh & 50 Phòng](_bmad-output/implementation-artifacts/spec-1-2-thiet-lap-da-chi-nhanh-danh-muc-phong.md)
  - [Spec 1.3: Cấu hình Phân quyền Nhân viên](_bmad-output/implementation-artifacts/spec-1-3-cau-hinh-phan-quyen-nhan-vien.md)
  - [Spec 1.4: Kích hoạt Booking Engine & Email SMTP](_bmad-output/implementation-artifacts/spec-1-4-kich-hoat-booking-engine-email.md)
- **Epic 2: Thanh Toán QR Tự Động**
  - [Spec 2.1: Tích hợp Giao diện Thanh toán VietQR](_bmad-output/implementation-artifacts/spec-2-1-tich-hop-giao-dien-thanh-toan-qr-frontend-addon.md)
  - [Spec 2.2: Xử lý Webhook Đối soát Tự động](_bmad-output/implementation-artifacts/spec-2-2-xu-ly-webhook-doi-soat-tu-dong-backend-addon.md)
- **Epic 3: Trải Nghiệm Người Dùng & Tối Ưu**
  - [Spec 3.1: Nhúng Bản đồ Vị trí 3 Chi nhánh](_bmad-output/implementation-artifacts/spec-3-1-nhung-ban-do-vi-tri-chi-nhanh.md)
  - [Spec 3.2: Tối ưu SEO & Tải trang Di động (CCC)](_bmad-output/implementation-artifacts/spec-3-2-toi-uu-seo-tai-trang-di-dong.md)

