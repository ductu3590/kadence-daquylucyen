# Báo cáo kiểm thử theme 2.0.0 trên LocalWP — 2026-10-28

Site: http://daquylucyen.local/, theme "Đá Quý Lục Yên" 2.0.0 đang bật, Customizer → Colors đã khôi phục mặc định.
Dữ liệu thật của site (chuyên mục phân cấp, `kien-thuc-da-quy/da-ban-quy` 28 bài), không phải bộ dữ liệu mẫu trong tài liệu.
URL đã kiểm: `/`, `/category/kien-thuc-da-quy/da-ban-quy/` (+ trang 2), `/bach-kim-la-gi/`, `/?s=ruby`, `/khong-ton-tai-xyz-123/`.
Công cụ: Playwright (Chrome hệ thống), Lighthouse 12.6.1 mobile.

Đã sửa ngay trên máy (anh Tú đồng ý), **chưa commit/push** — xem mục "Thay đổi" cuối file.

## Bảng 14 mục (sau khi sửa)

| # | Mục | Kết quả | Ghi chú |
|---|---|---|---|
| 1 | Không lỗi PHP | Đạt | 5 trang, 0 lỗi từ theme. debug.log chỉ có Deprecated của plugin WP Pusher (dưới). |
| 2 | Bảng màu | Đạt | Nền #F6F3EE, liên kết #A3122E. |
| 3 | Icon chuyên mục | Đạt | Chip và trụ đều có huy hiệu svg, màu theo chuyên mục. Chưa thử slug lạ. |
| 4 | Huy hiệu trụ | Đạt | Đè mép dưới ảnh, không bị cắt. |
| 5 | Chế độ tối | Đạt | Script init trước CSS, không nháy; nút đổi nhớ sau tải lại; ô tìm kiếm/phân trang/bình luận nền tối. |
| 6 | Header khi cuộn | **Đạt (đã sửa)** | Trước: nền đục #fff vì Kadence in `#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) > .site-header-row-container-inner{background:#ffffff}` nặng hơn quy tắc của theme. Sau: nền 92% + blur(20px), cả sáng và tối. |
| 7 | Dải Khám phá 390px | Đạt | Không bị che, chip 44px, mép phải mờ, không tràn ngang. |
| 8 | Khối nổi bật | Đạt | eager, ảnh lớn fetchpriority=high, có sizes. |
| 9 | Bàn phím | Đạt | Mọi phần tử có vòng focus #A3122E. |
| 10 | Giảm chuyển động | Đạt | Không ẩn, không animation. |
| 11 | Trang chuyên mục | Đạt | 3 thẻ + 7 hàng, phân trang chạy, chip đúng. |
| 12 | Bài viết | **Đạt (đã sửa)** | Trước: có cột bên vì `inc/brand.php` đặt `post_layout = 'right'`. Sau: `'narrow'` (760px). Từ 1320px mục lục là cột dính bên trái; dưới 1320px là khối gập — đúng README. Chữ 17/28, thanh tiến độ chạy. |
| 13 | Lighthouse mobile | **Một phần** | Accessibility **98 → 100** (sửa skip-link). Performance **74–78**, LCP **5,1–5,3 s**, CLS 0 — chưa đạt ≥85 / ≤2,5 s. Xem phân tích dưới. |
| 14 | Tắt JavaScript | **Đạt (đã sửa)** | Trước: OS tối mà không JS thì trang vẫn sáng. Sau: nền #15120F. |

## Mục 13 — vì sao chưa đạt và đã thử gì

- Phần tử LCP: ảnh ruby khối nổi bật (đã có fetchpriority=high, eager, phát hiện được ngay trong HTML).
- LocalWP phục vụ HTTP/1.1 (42 request), không cache tĩnh, TTFB 0,3–1,3 s. Lighthouse mô phỏng Slow 4G trên 6 kết nối nên ~12 file CSS/JS chặn hiển thị bị xếp hàng.
- Đã thử và **bỏ** vì không đáng:
  - Preload ảnh LCP ở đầu `<head>`: ảnh bắt đầu tải từ 0,9 s thay vì 4,1 s nhưng LCP không đổi.
  - Nhúng toàn bộ CSS theme vào HTML (`wp_maybe_inline_styles`): chỉ được 0,3–0,5 s, đổi lại mất bộ nhớ đệm CSS.
  - Tắt preload font: FCP tệ hơn (2,4 → 3,1 s).
- Đã giữ: `sizes` đúng cho ảnh trụ, ảnh "Bài mới nhất", ảnh nổi bật nhỏ.
- Đề xuất: đo lại trên staging/production có HTTP/2 + page cache trước khi kết luận. Nếu vẫn thiếu, bước tiếp theo đáng làm là gộp CSS theme thành một file (cần bước build).

## Lỗi PHP (debug.log, nguyên văn)

```
PHP Deprecated:  Creation of dynamic property Pusher\Log\Logger::$file is deprecated in C:\Users\ductu\Local Sites\daquylucyen\app\public\wp-content\plugins\wppusher\Pusher\Log\Logger.php on line 18
PHP Deprecated:  Creation of dynamic property Pusher\Dashboard::$pusher is deprecated in C:\Users\ductu\Local Sites\daquylucyen\app\public\wp-content\plugins\wppusher\Pusher\Dashboard.php on line 68
```

Không có dòng nào từ theme. Bật WP_DEBUG_DISPLAY thì hai dòng này in ra đầu trang và gây tràn ngang ở 390px — không phải lỗi theme.

## Thay đổi (chưa commit)

| File | Thay đổi |
|---|---|
| `assets/css/main.css` | Thêm bộ chọn sticky của Kadence vào quy tắc `.dql-da-cuon` để header đã cuộn thật sự mờ 92%. |
| `inc/brand.php` | `post_layout` mặc định `'right'` → `'narrow'`. |
| `inc/theme-mode.php` | `dql_dark_mode_noscript()`: in dark.css trong `<noscript>` với `html:not([data-dql-theme])` + `media="(prefers-color-scheme: dark)"`, ở cuối `<head>`. Một nguồn CSS duy nhất. |
| `front-page.php`, `category.php`, `home.php`, `search.php`, `archive-dql_thuat_ngu.php` | id khung ngoài → `id="main"` (đích của skip-link Kadence gắn cứng `#main`). Các id cũ `dql-home`… không được CSS/JS nào dùng. |
| `template-parts/home/latest.php`, `pillars.php`, `featured.php` | `sizes` khớp bề rộng hiển thị thật. |

Lưu ý cho phiên gốc: README bảng quảng cáo vẫn liệt kê "cột bên bài viết (trên/dưới, dính)" — với bố cục một cột, các ô này không còn hiện trừ khi chủ site bật lại cột bên trong Customizer. Cần chốt và sửa README.

## Ảnh chụp (docs/kiem-thu/, chụp sau khi sửa)

trang-chu-1440-sang.png, trang-chu-1440-toi.png, trang-chu-1440-toi-khong-js.png (OS tối, tắt JS), trang-chu-390.png, chuyen-muc-1440.png, bai-viet-1440.png, bai-viet-1440-toi.png, bai-viet-390.png
