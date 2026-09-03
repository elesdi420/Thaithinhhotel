---
stepsCompleted: ["01-validate-prerequisites", "02-design-epics", "03-create-stories", "04-final-validation"]
inputDocuments: ["_bmad-output/planning-artifacts/prds/prd-CMS-2026-08-30/prd.md"]
---

# CMS - Epic Breakdown

## Overview

This document provides the complete epic and story breakdown for CMS, decomposing the requirements from the PRD, UX Design if it exists, and Architecture requirements into implementable stories.

## Requirements Inventory

### Functional Requirements

- **FR-1**: Quản lý Phòng & Hạng phòng (Admin có thể tạo, sửa, xóa phòng và thiết lập Hạng phòng kèm giá cơ bản).
- **FR-2**: Quản lý Đặt phòng (Booking) (Lễ tân có thể tạo mới, cập nhật các booking).
- **FR-3**: Lưới phòng thời gian thực (Trạng thái phòng hiển thị trên lưới lịch và cập nhật real-time).
- **FR-4**: Hồ sơ Khách hàng (CRM) (Lưu trữ thông tin khách hàng, lịch sử lưu trú. Quản lý gán nhãn VIP thủ công).
- **FR-5**: Phân quyền nhân viên (Admin tạo tài khoản và gán Role. Lễ tân xem cơ sở mình, Quản lý xem toàn hệ thống).
- **FR-6**: Đồng bộ dữ liệu chi nhánh (Dữ liệu gắn tag BranchID).
- **FR-7**: Master Dashboard (Quản lý cấp cao xem tổng quan toàn hệ thống hoặc filter theo cơ sở).
- **FR-8**: Direct Booking Engine (Giao diện đặt phòng online cho khách, responsive, hiển thị real-time).
- **FR-9**: Channel Manager (Đồng bộ OTA) - *[Deferred to v2]*
- **FR-10**: Email tự động (Gửi email xác nhận đặt phòng cơ bản).
- **FR-11**: SEO & Maps (Tích hợp Google Maps 3 cơ sở trên Website; hỗ trợ thẻ Meta SEO cơ bản).
- **FR-12**: Thanh toán QR Code động (Tích hợp duy nhất 1 cổng aggregator tạo mã QR động).
- **FR-13**: Đối soát tự động (Webhook) (Nhận webhook từ cổng thanh toán để tự đổi trạng thái đơn).
- **FR-14**: Trả lời tự động (FAQ) - *[Deferred to v2]*
- **FR-15**: Gợi ý đặt phòng (Chatbot) - *[Deferred to v2]*
- **FR-16**: Đa kênh (Chatbot Website, Fanpage, Zalo OA) - *[Deferred to v2]*
- **FR-17**: Báo cáo Doanh thu (Theo Ngày/Tháng/Quý, lọc theo cơ sở).
- **FR-18**: Chỉ số vận hành (Biểu đồ Occupancy rate và RevPAR).
- **FR-19**: Export File (Xuất dữ liệu ra CSV/Excel).

### NonFunctional Requirements

- **NFR-1**: Nhà phát triển phải cung cấp tài liệu hướng dẫn (User Manual).
- **NFR-2**: Tổ chức đào tạo trực tiếp cho Lễ tân & Quản lý tại 3 cơ sở.
- **NFR-3**: Có giai đoạn UAT (User Acceptance Testing) và hỗ trợ vận hành thử trước khi Go-live.
- **SM-1**: 100% không xảy ra lỗi Overbooking trên hệ thống Direct Booking.
- **SM-2**: 100% giao dịch thanh toán QR Code được đối soát tự động thành công (< 2 phút).
- **SM-3**: Lễ tân hoàn thành việc Check-in dưới 3 phút.

### Additional Requirements

- **Base Template (Architecture):** Sử dụng mã nguồn mở **QloApps** (https://github.com/qloapps/qloapps) làm nền tảng cốt lõi (Base codebase). 
- **Tác động:** QloApps đã cung cấp sẵn ~80% các tính năng cơ bản (Quản lý phòng, Website booking, Dashboard). Epic 1 Story 1 sẽ tập trung vào việc setup, deploy QloApps. Các Epic tiếp theo sẽ tập trung vào việc customize QloApps (sửa giao diện, viết Addon thanh toán QR, điều chỉnh luồng phân quyền đa chi nhánh) để khớp 100% với PRD.

### FR Coverage Map

- FR-1, FR-2, FR-3, FR-4, FR-5, FR-6, FR-7, FR-8, FR-10, FR-17, FR-18, FR-19: Epic 1 - Cấu hình QloApps "Out-of-the-box"
- FR-12, FR-13: Epic 2 - Code Addon Thanh toán QR Động
- FR-11: Epic 3 - Gắn Maps & Tinh chỉnh

## Epic List

### Epic 1: Cấu hình QloApps "Out-of-the-box"
Cài đặt xong phần mềm, setup xong 3 cơ sở, 50 phòng, phân quyền lễ tân và email mặc định của QloApps. 80% PRD hoàn thành tại đây (Không code).
**FRs covered:** FR-1, FR-2, FR-3, FR-4, FR-5, FR-6, FR-7, FR-8, FR-10, FR-17, FR-18, FR-19

### Epic 2: Code Addon Thanh toán QR Động (VietQR/PayOS)
Module duy nhất thực sự phải code backend: Sinh mã QR động cho khách đặt phòng và bắt Webhook đối soát tự động.
**FRs covered:** FR-12, FR-13

### Epic 3: Gắn Maps & Tinh chỉnh (Minor Tweaks)
Thêm script Google Maps vào theme QloApps và điền meta tag SEO.
**FRs covered:** FR-11

## Epic 1: Cấu hình QloApps "Out-of-the-box"

Cài đặt xong phần mềm, setup xong 3 cơ sở, 50 phòng, phân quyền lễ tân và email mặc định của QloApps. 80% PRD hoàn thành tại đây (Không code).

### Story 1.1: Triển khai mã nguồn QloApps

As a Quản trị viên,
I want cài đặt QloApps lên server,
So that tôi có một hệ thống nền tảng sẵn sàng hoạt động.

**Acceptance Criteria:**

**Given** một server trống và mã nguồn QloApps
**When** quản trị viên chạy lệnh deploy
**Then** QloApps chạy thành công ở trang chủ
**And** có thể truy cập được vào Admin Dashboard

### Story 1.2: Thiết lập Đa chi nhánh & Danh mục Phòng

As a Quản trị viên,
I want thiết lập 3 cơ sở và cấu hình 50 phòng (kèm giá/hạng phòng),
So that hệ thống phản ánh đúng quy mô kinh doanh thực tế.

**Acceptance Criteria:**

**Given** hệ thống QloApps đã cài đặt
**When** quản trị viên import danh sách phòng
**Then** hệ thống hiển thị 3 cơ sở riêng biệt
**And** lưới lịch phòng hiển thị đủ 50 phòng

### Story 1.3: Cấu hình Phân quyền Nhân viên

As a Quản trị viên,
I want tạo tài khoản Lễ tân cho từng cơ sở,
So that Lễ tân chỉ xem và xử lý booking của cơ sở mình.

**Acceptance Criteria:**

**Given** các tài khoản nhân viên được tạo
**When** nhân viên đăng nhập bằng tài khoản Lễ tân Cơ sở 1
**Then** Lễ tân chỉ thấy phòng và booking của Cơ sở 1
**And** không xem được báo cáo doanh thu tổng

### Story 1.4: Kích hoạt Booking Engine & Email

As a Khách hàng,
I want xem danh sách phòng trống trên web và nhận email sau khi đặt,
So that tôi có thể tự đặt phòng và nhận xác nhận.

**Acceptance Criteria:**

**Given** khách hàng thao tác trên frontend website
**When** khách đặt phòng thành công
**Then** booking được ghi nhận vào hệ thống
**And** email xác nhận được gửi tự động tới khách hàng

## Epic 2: Code Addon Thanh toán QR Động (VietQR/PayOS)

Module duy nhất thực sự phải code backend: Sinh mã QR động cho khách đặt phòng và bắt Webhook đối soát tự động.

### Story 2.1: Tích hợp Giao diện Thanh toán QR (Frontend Addon)

As a Khách hàng,
I want thấy mã QR thanh toán động kèm số tiền khi đến bước check-out,
So that tôi có thể dùng app ngân hàng quét và chuyển tiền ngay lập tức.

**Acceptance Criteria:**

**Given** khách hàng đến trang checkout
**When** chọn phương thức thanh toán "Chuyển khoản QR"
**Then** hệ thống hiển thị một mã QR động
**And** QR chứa sẵn số tài khoản, số tiền, và nội dung chuyển khoản là mã booking

### Story 2.2: Xử lý Webhook Đối soát tự động (Backend Addon)

As a Quản trị viên,
I want hệ thống tự nhận webhook từ cổng thanh toán,
So that trạng thái đơn hàng tự cập nhật sang "Đã thanh toán" mà lễ tân không cần check tay.

**Acceptance Criteria:**

**Given** một đơn booking đang ở trạng thái "Chờ thanh toán"
**When** hệ thống nhận được webhook báo thành công từ cổng thanh toán
**Then** trạng thái booking tự động chuyển thành "Đã thanh toán"
**And** gửi email xác nhận thành công cho khách

## Epic 3: Gắn Maps & Tinh chỉnh (Minor Tweaks)

Thêm script Google Maps vào theme QloApps và điền meta tag SEO.

### Story 3.1: Tích hợp Bản đồ Vị trí 3 Cơ sở

As a Khách hàng,
I want xem bản đồ hiển thị vị trí của 3 chi nhánh trên trang chủ,
So that tôi dễ dàng ước lượng khoảng cách và chọn khách sạn phù hợp.

**Acceptance Criteria:**

**Given** khách truy cập vào trang chủ hoặc trang liên hệ
**When** kéo xuống phần thông tin vị trí
**Then** hiển thị một bản đồ Google Maps rõ nét
**And** bản đồ có đánh dấu (pin) vị trí của cả 3 cơ sở

### Story 3.2: Cấu hình SEO Meta Tags cơ bản

As a Quản trị viên,
I want website được tối ưu thẻ title và description,
So that trang web dễ dàng xuất hiện trên kết quả tìm kiếm của Google.

**Acceptance Criteria:**

**Given** người dùng hoặc bot tìm kiếm đọc mã nguồn trang web
**When** kiểm tra thẻ head
**Then** các thẻ meta title, meta description và keywords hiển thị chính xác
**And** nội dung khớp với cấu hình quản trị viên đã nhập trong QloApps Admin
