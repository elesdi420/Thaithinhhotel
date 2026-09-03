---
title: Phần mềm Quản lý Khách sạn (Hotel PMS)
status: final
created: 2026-08-30
updated: 2026-08-30
---

# 1. Vision and Goals
Hệ thống Quản lý Khách sạn (Hotel PMS) là giải pháp phần mềm "đo ni đóng giày" cho một chuỗi khách sạn tư nhân quy mô vừa (3 cơ sở, tổng 50 phòng). Mục tiêu của hệ thống là tự động hóa toàn bộ quy trình vận hành từ đặt phòng, nhận/trả phòng, quản lý doanh thu, đến chăm sóc khách hàng đa kênh. 

Hệ thống giúp loại bỏ sai sót do làm thủ công (như overbooking), đồng bộ dữ liệu theo thời gian thực giữa các kênh bán phòng (OTA) và website tự có, đồng thời cung cấp công cụ thanh toán tự động và báo cáo doanh thu trực quan cho chủ doanh nghiệp.

# 2. User Journeys

- **UJ-1. Khách hàng tự đặt phòng qua Website:** Khách hàng (Guest) truy cập website khách sạn trên điện thoại, chọn ngày check-in/out, xem ảnh và giá phòng (cập nhật real-time). Guest chọn phòng, thanh toán trực tiếp qua mã QR VNPay/Momo. Hệ thống tự động xác nhận giao dịch, gửi email confirm và cập nhật trạng thái phòng trên PMS.
- **UJ-2. Lễ tân xử lý check-in nhanh:** Lễ tân (Receptionist) tại Cơ sở 1 đăng nhập vào hệ thống. Xem danh sách khách đến trong ngày (gồm khách đặt qua Website và qua OTA như Agoda/Booking). Khi khách đến, Lễ tân tìm tên, quét giấy tờ, thu phụ phí (nếu có) và nhấn "Check-in". Trạng thái phòng đổi sang "Đang sử dụng" trên toàn hệ thống.
- **UJ-3. Quản lý chuỗi xem báo cáo:** Người quản lý (Manager) mở Dashboard trên máy tính. Xem tỷ lệ lấp đầy (Occupancy rate) của cả 3 cơ sở hôm nay. Phát hiện Cơ sở 2 đang vắng khách, Manager quyết định tạo mã giảm giá nhanh và Chatbot tự động gợi ý mã này cho khách hàng đang hỏi trên Fanpage Zalo/Facebook.
- **UJ-4. Chatbot tư vấn nửa đêm:** Lúc 2h sáng, một khách hàng nhắn tin vào Zalo OA hỏi "Khách sạn có cho mang thú cưng không?". AI Chatbot tự động quét chính sách và trả lời "Dạ không ạ", đồng thời gợi ý luôn link đặt phòng cho ngày mai kèm báo giá.

# 3. Glossary
- **Cơ sở (Branch):** Một địa điểm vật lý của khách sạn. Hệ thống hiện quản lý 3 Cơ sở.
- **PMS (Property Management System):** Phần mềm quản trị lõi của khách sạn.
- **OTA (Online Travel Agent):** Các kênh bán phòng trung gian như Agoda, Booking.com, Airbnb, Traveloka.
- **Phòng (Room):** Đơn vị cho thuê vật lý, thuộc một Hạng phòng (Room Category) và một Cơ sở cụ thể.
- **Occupancy Rate (Công suất phòng):** Tỷ lệ phần trăm số phòng có khách ở trên tổng số phòng đang hoạt động.

# 4. Features

### 4.1. Core PMS (Hệ thống quản lý lõi)
**Description:** Module cốt lõi giúp Lễ tân và Quản lý thao tác nghiệp vụ hàng ngày. Quản lý trạng thái vật lý của 50 phòng.

**Functional Requirements:**
- **FR-1: Quản lý Phòng & Hạng phòng:** Admin có thể tạo, sửa, xóa phòng và thiết lập Hạng phòng kèm giá cơ bản.
- **FR-2: Quản lý Đặt phòng (Booking):** Lễ tân có thể tạo mới, cập nhật (check-in, check-out, đổi/hủy phòng) các booking.
- **FR-3: Lưới phòng thời gian thực:** Trạng thái phòng (Trống, Đang ở, Đang dọn, Đang bảo trì) hiển thị trên lưới lịch (Calendar view) và cập nhật real-time.
- **FR-4: Hồ sơ Khách hàng (CRM):** Lưu trữ thông tin khách hàng, lịch sử lưu trú. Quản lý gán nhãn VIP thủ công (manual). `[ponytail: 50 phòng không cần rules engine cho VIP auto-tiering]`
- **FR-5: Phân quyền nhân viên:** Admin có thể tạo tài khoản và gán Role (Lễ tân, Quản lý, Buồng phòng). `[ASSUMPTION: Lễ tân chỉ xem được dữ liệu của cơ sở mình làm việc, Quản lý xem được toàn hệ thống]`. `[ponytail: Mọi cơ sở chung 1 database table, chỉ filter bằng branch_id, cấm làm multi-tenant phức tạp cho 50 phòng]`

### 4.2. Quản lý Đa chi nhánh
**Description:** Khả năng mở rộng và quản trị tập trung cho chuỗi 3 cơ sở.

**Functional Requirements:**
- **FR-6: Đồng bộ dữ liệu chi nhánh:** Dữ liệu phòng, khách hàng, và doanh thu được đồng bộ tập trung, có gắn tag `BranchID`.
- **FR-7: Master Dashboard:** Quản lý cấp cao xem được tổng quan toàn hệ thống hoặc filter lọc theo từng cơ sở.

### 4.3. Website Đặt phòng & Channel Manager
**Description:** Cổng bán hàng trực tiếp (Direct Booking) và đồng bộ kênh bán hộ (OTA).

**Functional Requirements:**
- **FR-8: Direct Booking Engine:** Cung cấp giao diện đặt phòng cho khách (Responsive Web). Hiển thị giá phòng, hình ảnh, tiện ích và tình trạng trống thực tế.
- **FR-9: Channel Manager (Đồng bộ OTA):** Kết nối 2 chiều với các nền tảng OTA (Agoda, Airbnb, Booking, Traveloka). Khi có phòng đặt trên OTA, hệ thống nội bộ tự khóa phòng tương ứng. `[ASSUMPTION: Hệ thống sẽ dùng API chính thức của các OTA hoặc tích hợp qua 1 middleware thứ 3 để giảm chi phí phát triển]`.
- **FR-10: Email tự động:** Chỉ gửi email xác nhận đặt phòng cơ bản (transactional). `[ponytail: Cắt bỏ email marketing, nhắc nhở, xin review. Dùng Mailgun/Sendgrid API thường, YAGNI cho marketing flow]`
- **FR-11: SEO & Maps:** Tích hợp Google Maps hiển thị vị trí 3 cơ sở trên Website; hỗ trợ thẻ Meta SEO cơ bản.

### 4.4. Cổng thanh toán trực tuyến
**Description:** Tự động hóa khâu thu tiền khách đặt qua website.

**Functional Requirements:**
- **FR-12: Thanh toán QR Code động:** Tích hợp duy nhất 1 cổng (như VietQR/PayOS) để tạo mã QR động hỗ trợ mọi ngân hàng/Momo/ZaloPay. `[ponytail: 1 cổng quét tất cả, không tích hợp lẻ tẻ từng ví]`
- **FR-13: Đối soát tự động (Webhook):** Nhận webhook từ cổng trung gian trên để tự đổi trạng thái đơn.

### 4.5. AI Chatbot
**Description:** Trợ lý ảo tư vấn tự động 24/7.

**Functional Requirements:**
- **FR-14: Trả lời tự động (FAQ):** AI Chatbot trả lời các câu hỏi về giá, tiện ích, chính sách khách sạn.
- **FR-15: Gợi ý đặt phòng:** Chatbot có thể gợi ý link phòng phù hợp và chốt sale trực tiếp.
- **FR-16: Đa kênh:** Chatbot tích hợp trên Website, Fanpage Facebook, và Zalo OA. `[ASSUMPTION: Chưa tích hợp Chatbot vào trong hệ thống inbox của Airbnb/Agoda do giới hạn API của OTA, chỉ tập trung vào kênh Direct]`.

### 4.6. Báo cáo & Thống kê
**Description:** Công cụ BI (Business Intelligence) đơn giản cho chủ đầu tư.

**Functional Requirements:**
- **FR-17: Báo cáo Doanh thu:** Xem doanh thu theo Ngày/Tháng/Quý, lọc theo từng cơ sở hoặc tổng hệ thống.
- **FR-18: Chỉ số vận hành:** Biểu đồ thể hiện công suất lấp đầy (Occupancy rate) và doanh thu trung bình/phòng (RevPAR).
- **FR-19: Export File:** Chỉ xuất dữ liệu ra định dạng CSV/Excel. `[ponytail: Bỏ PDF export, vẽ PDF trên backend rất cực và không cần thiết]`

### 4.7. Đào tạo & Vận hành (Non-Software)
- **NFR-1:** Nhà phát triển phải cung cấp tài liệu hướng dẫn (User Manual).
- **NFR-2:** Tổ chức đào tạo trực tiếp cho Lễ tân & Quản lý tại 3 cơ sở.
- **NFR-3:** Có giai đoạn UAT (User Acceptance Testing) và hỗ trợ vận hành thử trước khi Go-live.

# 5. Non-Goals (Explicit)
- Không xây dựng Mobile App (iOS/Android) Native cho khách đặt phòng (chỉ dùng Web Responsive để tiết kiệm chi phí).
- Không làm hệ thống kế toán nội bộ phức tạp (chỉ dừng ở việc quản lý doanh thu dịch vụ).
- Không tự build hệ thống thẻ từ mở cửa phòng (IoT Integration) trong giai đoạn này.

# 6. MVP Scope
### 6.1 In Scope
- Core PMS (FR 1-5).
- Multi-branch structure (FR 6-7).
- Website Đặt phòng Direct (FR 8, 11).
- Cổng thanh toán tự động qua QR Code (FR 12-13).
- Báo cáo cơ bản (FR 17, 19).

### 6.2 Out of Scope for MVP
- Channel Manager đồng bộ OTA (FR 9). `[NOTE FOR PM: Để tiết kiệm chi phí tối đa theo yêu cầu, hoãn tích hợp API/Middleware sang v2. Nếu bắt buộc cần đồng bộ ở v1, sẽ chỉ dùng iCal sync (đồng bộ lịch một chiều, miễn phí nhưng chậm).]`.
- AI Chatbot đa kênh (FR 14-16). `[NOTE FOR PM: Hoãn lại v2. Lễ tân có thể trực chat Zalo/Fanpage bằng sức người trong giai đoạn đầu]`.
- Module Quản lý Dịch vụ phụ (Minibar, F&B, Spa...). `[NOTE FOR PM: Khách hàng cần thêm thời gian để suy nghĩ về luồng nghiệp vụ này. Tạm gác sang v2]`.

# 7. Success Metrics
**Primary**
- **SM-1:** 100% không xảy ra lỗi Overbooking (đặt trùng phòng) trên hệ thống Direct Booking. Validates FR-3, FR-8.
- **SM-2:** 100% giao dịch thanh toán QR Code được đối soát và đổi trạng thái tự động thành công (thời gian trễ < 2 phút). Validates FR-13.

**Secondary**
- **SM-3:** Lễ tân có thể hoàn thành việc Check-in cho khách dưới 3 phút. Validates FR-2, FR-4.

# 8. Open Questions
- Không có câu hỏi nào đang mở. Tất cả đã được giải quyết hoặc chuyển vào backlog v2.

# 9. Assumptions Index
- **[ASSUMPTION]** Phân quyền hiển thị (FR-5): Lễ tân chỉ xem được dữ liệu của cơ sở mình làm việc, Quản lý xem được toàn hệ thống.
- **[ASSUMPTION]** OTA AI Chat (FR-16): Chưa tích hợp Chatbot vào inbox của Airbnb/Agoda/Booking do giới hạn API, chỉ tập trung vào kênh Direct (Website, FB, Zalo).
