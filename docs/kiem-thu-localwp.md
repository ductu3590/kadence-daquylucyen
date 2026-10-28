# Kiểm thử theme 2.0 trên LocalWP trước khi deploy

Theme 2.0 chưa chạy trên WordPress thật. Đây là danh mục kiểm thử; **không
deploy lên daquylucyen.net.vn khi chưa qua hết mục "Phải đạt"**.

## Cài

1. LocalWP → Create a new site → WordPress mới nhất, PHP 8.1+.
2. Cài theme cha **Kadence** (Appearance → Themes → Add New → tìm "Kadence").
3. Chép thư mục child theme này vào `app/public/wp-content/themes/kadence-daquylucyen`
   (hoặc `git clone` nhánh `claude/laughing-maxwell-8n5480` vào đó).
4. Kích hoạt child theme. Cài và bật plugin `dql-core` nếu muốn thử ô tra mệnh và từ điển.
5. **Customizer → Colors → khôi phục mặc định** (site mới thì bỏ qua). Không làm
   bước này thì vẫn thấy bảng màu Sapphire cũ và tưởng theme lỗi.
6. Tạo dữ liệu thử: khoảng 12 bài có ảnh đại diện, chia vào ít nhất 6 chuyên mục
   có slug `da-quy`, `phong-thuy`, `kiem-dinh`, `van-hoa`, `du-lich`, `am-thuc`.
   Ghim 3 bài (Sửa nhanh → Dán bài này lên trang chủ).
7. Settings → Reading → "A static page" → trang chủ là một trang tĩnh bất kỳ
   (để `front-page.php` chạy), trang bài viết là một trang khác (để `home.php` chạy).

## Phải đạt

| # | Kiểm | Cách kiểm | Đạt khi |
|---|---|---|---|
| 1 | Không lỗi PHP | `define('WP_DEBUG', true)` trong wp-config; mở trang chủ, chuyên mục, bài viết, tìm kiếm, 404 | Không có notice/warning nào trên trang hay trong `debug.log` |
| 2 | Bảng màu | Trang chủ ở chế độ sáng | Nền `#F6F3EE`, liên kết ruby `#A3122E`, không còn xanh sapphire |
| 3 | Icon chuyên mục | Dải "Khám phá" và trụ trang chủ | Mỗi chuyên mục có icon riêng trong huy hiệu sáu cạnh lệch, màu đá đúng; chuyên mục slug lạ vẫn có icon mặc định |
| 4 | Huy hiệu trụ không bị cắt | Trụ "Lục Yên qua từng góc nhìn" | Huy hiệu đè nửa trên ảnh, nửa dưới nền thẻ, thấy đủ hình |
| 5 | Chế độ tối | Bật tối trên hệ điều hành, tải lại; rồi bấm nút đổi | Không nháy trắng khi tải; nút đổi được và nhớ sau khi tải lại; nhãn nổi trên ảnh thẻ đọc được; ô tìm kiếm, phân trang, bình luận không còn nền trắng |
| 6 | Header khi cuộn | Cuộn trang chủ | Ở đầu trang header đục; cuộn qua 8px header mờ, menu vẫn đọc được khi trôi qua ảnh |
| 7 | Dải "Khám phá" trên điện thoại | DevTools 390px | Dải không bị header che; chip cao 44px; mép phải mờ dần |
| 8 | Khối nổi bật | Trang chủ, DevTools Network | Ba ảnh tải ngay (không `loading=lazy`), ảnh lớn có `fetchpriority=high`, có `sizes`; không chờ hiệu ứng hiện dần |
| 9 | Bàn phím | Tab qua trang chủ | Vòng focus ruby thấy rõ trên mọi thẻ, kể cả thẻ nổi bật và trụ |
| 10 | Giảm chuyển động | Bật "Reduce motion" trên hệ điều hành | Không còn hiện dần, không còn chuyển động nào |
| 11 | Trang chuyên mục | Mở một chuyên mục có > 8 bài | Ba bài mới nhất dạng thẻ, phần còn lại hàng ngang, phân trang chạy, chip chuyên mục liên quan đúng |
| 12 | Bài viết | Mở bài dài có H2 | Mục lục dính, tiến độ đọc chạy, chữ 17/28, không cột bên |
| 13 | Lighthouse mobile | Trang chủ, Incognito | Performance ≥ 85, Accessibility ≥ 95, LCP ≤ 2,5 s trên "Slow 4G" |
| 14 | Tắt JavaScript | DevTools → Disable JavaScript | Trang vẫn đủ nội dung; chế độ tối theo hệ điều hành vẫn đúng; không có khối nào bị ẩn |

## Nên kiểm

- Logo PNG chữ tối trên nền tối: dark.css đảo sáng tạm; nên tải logo bản trắng
  vào Customizer → Header → Logo (tối).
- Plugin `dql-core` tắt: ô tra mệnh tự ẩn với khách, chỉ admin thấy chỗ dành sẵn.
- Bài không có ảnh đại diện: không hiện trong khối nổi bật và bài mới (cố ý).
- Trình soạn thảo: `editor.css` cho màu và chữ khớp ngoài trang.

## Khi phát hiện lỗi

Ghi vào issue trên GitHub kèm: trang nào, chế độ sáng/tối, kích thước màn hình,
ảnh chụp, và dòng trong `debug.log` nếu có. Sửa trên nhánh của PR #1, không sửa
trực tiếp trên `main`.
