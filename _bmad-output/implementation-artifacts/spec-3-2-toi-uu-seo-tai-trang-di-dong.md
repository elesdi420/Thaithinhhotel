---
title: 'Story 3.2: Tối ưu SEO & Tải trang Di động'
type: 'feature'
created: '2026-09-02'
status: 'done'
review_loop_iteration: 0
context: []
baseline_commit: NO_VCS
---

<frozen-after-approval reason="human-owned intent — do not modify unless human renegotiates">

## Intent

**Problem:** The default QloApps homepage has empty SEO meta title/description tags, lacks Open Graph / social sharing meta tags for Zalo and Facebook, has non-standard mobile viewport scaling, and performance optimizations (CCC - Combine, Compress, Cache) are inactive.

**Approach:** 
1. Populate Vietnamese and English SEO meta titles and descriptions for key pages (`index`, `our-properties`, `contact`) in `qlo_meta_lang`.
2. Enhance `themes/hotel-reservation-theme/header.tpl` with full Open Graph (`og:*`), Twitter Card (`twitter:*`), and mobile-responsive viewport (`width=device-width, initial-scale=1.0`).
3. Activate CCC performance optimizations in `qlo_configuration` (`PS_CSS_THEME_CACHE`, `PS_JS_THEME_CACHE`, `PS_SMARTY_CACHE`, `PS_HTACCESS_CACHE_CONTROL`).

## Boundaries & Constraints

**Always:**
- Use QloApps native CCC and Smarty template caching.
- Standards-compliant Open Graph tags compatible with Zalo, Facebook, and Google SERP snippets.
- Mobile viewport must allow normal pinch-to-zoom for accessibility (`maximum-scale=5.0`).

**Ask First:**
- Purchasing commercial CDN services (e.g. Cloudflare Enterprise).

**Never:**
- Do not install heavyweight third-party SEO extensions when native meta tags suffice.

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| Homepage Social Sharing | Share `http://localhost:8080` on Zalo/FB | Open Graph snippet with Title, Description, and Hotel logo preview | Fallback to default shop name |
| Mobile Viewport Inspection | DevTools mobile device emulation | Proper 1:1 scale without horizontal overflow or tiny zoom | Responsive viewport enforced |
| CCC Activation | Asset requests (`/themes/.../css/`, `js`) | Combined and cached assets loaded | Fallback to unminified if compilation fails |

</frozen-after-approval>

## Code Map

- `scripts/optimize_seo_and_performance.php` -- Populates meta tags in DB and activates CCC performance settings.
- `themes/hotel-reservation-theme/header.tpl` -- Injects Open Graph, Twitter Cards, and optimized mobile viewport.

## Tasks & Acceptance

**Execution:**
- [x] `scripts/optimize_seo_and_performance.php` -- populate SEO meta tags and activate CCC -- update qlo_meta_lang and qlo_configuration
- [x] `themes/hotel-reservation-theme/header.tpl` -- add Open Graph and optimize viewport -- inject og:* tags and mobile viewport
- [x] `verification check` -- inspect homepage HTML and response headers -- verify meta tags, og tags, and CCC asset minification

**Acceptance Criteria:**
- Given a visitor or search engine crawler accesses the website
- When viewing the page source of the homepage or contact page
- Then `<meta property="og:title">`, `<meta property="og:description">`, `<meta property="og:image">` exist
- And `<meta name="viewport" content="width=device-width, initial-scale=1.0...` is present
- And CCC caching (`PS_CSS_THEME_CACHE`, `PS_JS_THEME_CACHE`) is active

## Design Notes

SEO Default Copy:
- Title: `CMS Hotel - Hệ thống Khách sạn Chuỗi 3 Cơ sở | Đặt phòng Trực tuyến`
- Description: `Đặt phòng trực tiếp tại chuỗi khách sạn TP.HCM, Đà Nẵng, Hà Nội. Giá tốt nhất thị trường, hỗ trợ thanh toán mã QR VietQR 24/7.`

## Verification

**Commands:**
- `curl -s http://localhost:8080/ | grep -i "og:"` -- expected: og:title, og:description, og:image present
- `docker exec qloapps_db mariadb -u qloapps -pqloappspass qloapps -e "SELECT name, value FROM qlo_configuration WHERE name LIKE '%CACHE%';"` -- expected: CCC caches = 1

## Suggested Review Order

**SEO & Mobile Optimization**

- Injected Open Graph, Twitter Cards, theme-color, and mobile viewport
  [`themes/hotel-reservation-theme/header.tpl:40`](../../themes/hotel-reservation-theme/header.tpl#L40)

- Performance tuning and SEO metadata population script (CCC activation & meta_lang)
  [`scripts/optimize_seo_and_performance.php:1`](../../scripts/optimize_seo_and_performance.php#L1)
