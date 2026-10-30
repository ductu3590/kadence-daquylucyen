# Đá Quý Lục Yên — child theme

Child theme của **Kadence**, dành cho blog chia sẻ kiến thức đá quý và trang sức
tại daquylucyen.net.vn.

## Nguyên tắc quan trọng nhất

**Không bao giờ sửa file trong thư mục `kadence/`.** Mọi thay đổi đặt ở đây.
Kadence cập nhật sẽ ghi đè toàn bộ thư mục của nó.

## Màu sắc và chữ được quyết định ở đâu

Đây là điểm hay gây nhầm lẫn, đọc kỹ trước khi sửa màu:

1. Kadence đọc màu từ **Customizer**, rồi bơm ra biến CSS `--global-palette1..15`.
2. `theme.json` của Kadence chỉ *trỏ tới* các biến đó, không chứa mã màu.
3. File `inc/brand.php` đặt **giá trị mặc định** — dùng khi Customizer chưa lưu gì.

Nghĩa là:

| Muốn làm gì | Sửa ở đâu |
|---|---|
| Đổi màu, thử nhanh, xem trực tiếp | Customizer → Colors |
| Đổi mặc định của site (áp dụng khi cài mới) | `inc/brand.php` |
| Thêm CSS cho một thành phần cụ thể | `assets/css/main.css` |

Sửa trong Customizer sẽ **ghi đè** `inc/brand.php`. Đó là hành vi đúng, không
phải lỗi. Nếu bạn sửa `inc/brand.php` mà không thấy gì đổi, nguyên nhân gần như
chắc chắn là Customizer đã có giá trị lưu sẵn.

## Bộ nhận diện

Bản 2.0: ngôn ngữ **"Đá Thô"** — viên đá chưa mài. Nền đá hoa trắng Lục Yên có
hạt, điểm nhấn ruby dùng tiết chế (liên kết, nút, nhãn; không bao giờ phủ nền
rộng), chữ serif có "độ mềm" của đá bào. Khung sườn theo Apple Human Interface
Guidelines: lưới 8pt, đích bấm 44px, header vật liệu mờ khi cuộn, chế độ tối.
Bản thiết kế đã chốt nằm trên canvas Claude Design (Page 1), xem `docs/thiet-ke.md`.

| Vai trò | Mã màu | Khe Kadence |
|---|---|---|
| Ruby Lục Yên (link, nút, nhãn) | `#A3122E` | palette1 |
| Ruby đậm (di chuột) | `#6E0B20` | palette2 |
| Than đá (tiêu đề) | `#1C1917` | palette3 |
| Đá đen ấm (nội dung bài) | `#44403B` | palette4 |
| Đá xám (chú thích) | `#5E5750` | palette5 |
| Bụi đá (meta, ngày tháng) | `#6F6861` | palette6 |
| Vôi (viền, nền hộp) | `#ECE6DC` | palette7 |
| Đá hoa trắng (nền trang) | `#F6F3EE` | palette8 |
| Mặt thẻ | `#FFFFFF` | palette9 |

Mọi cặp chữ/nền đạt WCAG AA (≥ 4.5:1), kể cả nhãn 12px chữ hoa. Màu đá của 16
chuyên mục (`--dql-da-*`) nằm ngoài bảng Kadence, khai báo ở đầu
`assets/css/main.css` và phải khớp với `dql_mau_da_admin()` trong `inc/icons.php`.

Chữ: **Fraunces** (serif, weight 500–700) cho tiêu đề, **Be Vietnam Pro** cho
nội dung, 17px / 1.65. Kadence tải từ Google Fonts theo weight tĩnh; **bắt buộc
bật bộ ký tự `vietnamese`** — nếu tắt, chữ có dấu sẽ rơi về font dự phòng của hệ
điều hành và trông lệch hẳn so với chữ không dấu. Trục "SOFT" của Fraunces chỉ
có trong mockup, không có trong file Kadence tải về; tự host font biến thiên là
việc để sau.

Khi nâng cấp từ 1.x, Customizer vẫn giữ bảng màu Sapphire cũ: vào **Customizer →
Colors → khôi phục mặc định** để nhận bảng Đá Thô.

## Giao diện Đá Thô: ba tùy chọn và bộ icon

**Customizer → Giao diện Đá Thô** có ba công tắc, xử lý ở `inc/theme-mode.php`:

| Tùy chọn | Mặc định | Ghi chú |
|---|---|---|
| Chế độ tối | Theo hệ điều hành | Script ở `<head>` đặt `data-dql-theme` trước khi trang vẽ, không nháy. Nút đổi tay: shortcode `[dql_doi_nen]` đặt trong header builder; không đặt thì `ui.js` tự chèn vào phần phải của header. Lựa chọn tay lưu trong trình duyệt. `assets/css/dark.css` chỉ đặt lại biến `--global-palette*` và ghi đè những chỗ Kadence in mã màu cố định. |
| Dải "Khám phá" | Mọi trang | Hàng chip chuyên mục có huy hiệu đá dưới header (`inc/header.php`). Chọn chỉ trang chủ, hoặc tắt. |
| Kết cấu hạt đá | Bật | Nhiễu SVG, không ảnh, không request. Nền trang 4%, hero và dải tối 12%. Tự tắt trên điện thoại ở nền trang. |

**Icon chuyên mục** (`inc/icons.php`): 16 biểu tượng nét 1.75 trên lưới 24pt,
mỗi chuyên mục nằm trong huy hiệu đá sáu cạnh lệch tô một màu đá. Theme tự khớp
icon theo slug chuyên mục (`da-quy`, `phong-thuy`, `van-hoa`, `du-lich`, `am-thuc`…);
chủ site đổi icon và màu ở **Bài viết → Chuyên mục → Sửa**. Chuyên mục không
khớp slug nào nhận icon và màu mặc định, không bao giờ trống.

## Cấu trúc

```
functions.php               Chỉ nạp inc/, không chứa logic
inc/setup.php               Bản dịch, kích thước ảnh, hỗ trợ trình soạn thảo
inc/brand.php               Màu, chữ, chiều rộng nội dung
inc/enqueue.php             Nạp CSS/JS
inc/template-tags.php       Hàm dùng trong template (thời gian đọc, trụ, icon)
inc/customizer.php          Tùy chọn nội dung trang chủ
inc/sidebar.php             Cột bên trang chủ và cột bên bài viết
inc/archive.php             Các khối của trang chuyên mục, và phần cắm vào Kadence
inc/icons.php               16 icon chuyên mục, huy hiệu đá, màu đá, trường chọn ở màn Sửa chuyên mục
inc/theme-mode.php          Chế độ tối, tùy chọn Customizer "Giao diện Đá Thô"
inc/header.php              Dải "Khám phá" dưới header, dải tiến độ đọc
front-page.php              Trang chủ, gọi các khối bên dưới
category.php                Trang chuyên mục, tự dựng toàn bộ
home.php                    Trang danh sách bài, dùng chung khối với category.php
search.php                  Trang tìm kiếm, tự dựng toàn bộ
template-parts/home/        featured · hero · pillars · menh-tool · spotlight · latest · newsletter
assets/css/main.css         CSS chung
assets/css/home.css         CSS trang chủ, chỉ nạp ở trang chủ
assets/css/archive.css      CSS chuyên mục · danh sách bài · tìm kiếm
assets/css/sidebar.css      CSS cột bên, dùng chung cho cả ba trang
assets/css/dark.css         Chế độ tối: đặt lại biến màu, ghi đè khung Kadence
assets/css/editor.css       CSS trong trình soạn thảo
assets/js/ui.js             Nút đổi chế độ, tiến độ đọc, header đã cuộn
docs/thiet-ke.md            Quyết định thiết kế và lý do
docs/kiem-thu-localwp.md    Danh mục kiểm thử trên LocalWP trước khi deploy
```

## Cấu trúc nội dung

Ba chuyên mục cha, mỗi cha hai con. Ba trụ trang chủ trỏ vào ba cha:

```
Kiến thức đá quý (53)        Trang sức và ứng dụng (10)   Tư vấn chọn mua (16)
├── Đá quý (25)              ├── Trang sức đá quý (9)     ├── Tư vấn Đá quý (14)
└── Đá bán quý (28)          └── Tranh đá quý (1)         └── Tư vấn phong thuỷ (2)
```

Số trong ngoặc ở chuyên mục cha là **tổng cả bài của con** — WordPress không tự
tính con số này, `dql_category_deep_count()` trong `inc/template-tags.php` lo việc
đó. Dùng thẳng `$term->count` sẽ hiện "0 bài viết" dưới một trụ đang chứa 53 bài.

Chưa có nội dung về **kiểm định và chứng nhận** — đây là mảng bạn từng muốn làm
trụ nhưng chưa có bài nào. Khi viết đủ vài bài, tạo chuyên mục mới và đổi trụ
trong Customizer.

## Trang chủ

Bản 2.0 theo thiết kế bản 1 trên canvas, **nền xen kẽ có chủ đích**:

| Dải | Nền | Nội dung |
|---|---|---|
| featured | Đá hoa trắng | Bài nổi bật, một lớn hai nhỏ, chữ đè ảnh |
| hero | Trắng | Câu định vị, ô tìm kiếm |
| pillars | Đá hoa trắng | Trụ nội dung (tối đa 6), ảnh + huy hiệu đá đè mép dưới ảnh |
| **menh-tool** | **Than đá** | **Điểm nhấn — công cụ tra mệnh, có hạt đá** |
| spotlight | Đá hoa trắng | Chuyên mục tiêu điểm (chọn trong Customizer) |
| latest | Đá hoa trắng | Bài mới nhất + cột bên (đọc nhiều, newsletter, từ điển) |
| footer | Than đá | Bốn cột, nút lên đầu trang |

Khi mọi dải cùng nền trắng, chúng trôi liền một mạch và mắt không nhận ra ranh
giới — đó là nguyên nhân của cảm giác "rời rạc" dù nội dung vẫn đủ. Dải xanh đậm
ở giữa cắt nhịp trang và làm nổi công cụ có giá trị nhất.

Khối bài nổi bật dùng kiểu **chữ đè lên ảnh** với lớp phủ tối chuyển dần. Không
chỉ vì đẹp: bản trước đặt chữ dưới ảnh, khiến hai thẻ nhỏ bên phải chỉ cao bằng
nội dung trong khi cột phải phải kéo dài bằng thẻ trái — sinh ra khoảng trắng
lớn ở giữa. Thẻ kiểu đè chữ luôn cao 100% ô được chia nên không còn lỗi đó.

Lớp phủ là bắt buộc, không phải trang trí: ảnh đá quý thường chụp trên nền trắng,
chữ trắng đặt thẳng lên sẽ không đọc được.

### Chọn bài nổi bật

Mở **Bài viết → Sửa nhanh → tích "Dán bài này lên trang chủ"**. Ghim bao nhiêu
bài thì bấy nhiêu bài lên khối nổi bật, thiếu thì lấy thêm bài mới nhất.

Lưu ý kỹ thuật: WordPress chỉ tự ưu tiên bài ghim ở truy vấn chính. Khối này là
truy vấn riêng nên `dql_featured_post_ids()` phải tự xử lý — đừng gỡ đoạn đó ra,
gỡ là chủ site mất quyền chọn bài nổi bật mà không có lỗi nào báo.

### Hiệu ứng hiện dần khi cuộn

`assets/js/reveal.js`. Nguyên tắc an toàn: CSS **chỉ ẩn** phần tử khi script đã
chạy và gắn class `dql-reveal-ready` lên thẻ `html`. JavaScript hỏng hay bị chặn
thì mọi thứ hiện bình thường — không bao giờ có chuyện trang trắng xóa vì hiệu
ứng lỗi. Tự tắt khi người dùng bật chế độ giảm chuyển động.

Khối nổi bật **không** có hiệu ứng này: ba ảnh đó nằm trên màn hình đầu và ảnh
lớn nhất là LCP của trang, ẩn chúng chờ JavaScript là tự làm trang chậm đi gần
nửa giây. Cả ba ảnh tải `eager`, có `sizes` để trình duyệt chọn đúng cỡ.

Script này nạp ở **đầu trang** chứ không phải cuối, để các khối không hiện ra rồi
mới biến mất.

### Lưới bài chỉ lấy bài có ảnh

Cả khối nổi bật lẫn khối bài mới đều bỏ qua bài không có ảnh đại diện. Một thẻ
thiếu ảnh để lại ô xám giữa các thẻ khác và làm cả khối trông như đang hỏng.

**Nội dung** sửa ở **Customizer → Trang chủ Đá Quý**: câu định vị, ba trụ chuyên
mục, ô tra mệnh, số bài mới hiển thị.

Chưa chọn trụ nào thì trang chủ tự lấy ba chuyên mục nhiều bài nhất, nên nó không
bao giờ hiện ra trống.

Khối tra mệnh do plugin `dql-core` cung cấp. Nếu tắt plugin đó, khối này **tự ẩn
đi** với khách và chỉ quản trị viên thấy chỗ dành sẵn — theme kiểm tra
`function_exists( 'dql_core_menh_form' )` trước khi gọi, nên không bao giờ hiện
một ô nhập liệu bấm vào không có gì xảy ra.

Dữ liệu gợi ý đá theo mệnh nằm ở `wp-content/plugins/dql-core/includes/data/menh-goi-y.php`,
không nằm trong theme. Xem README của plugin.

Muốn bỏ trang chủ tùy biến và quay về mặc định của WordPress: đổi tên hoặc xóa
`front-page.php`.

## Trang bài viết

Bố cục một cột, mục lục dính ở lề trái. Không có cột bên — khổ đọc được ưu tiên,
các lối rẽ sang bài khác đặt ở cuối bài.

Mục lục sinh tự động từ thẻ **H2 và H3** trong bài, **chỉ hiện khi bài có từ 3
tiêu đề trở lên** (`DQL_TOC_MIN_HEADINGS` trong `inc/toc.php`). Bài ít tiêu đề
hơn thì mục lục chỉ gây rối.

Hai ngưỡng cần biết khi sửa:

| Khổ màn hình | Mục lục |
|---|---|
| Từ 1320px | Cột ở lề trái, dính khi cuộn, luôn mở |
| Dưới 1320px | Thanh gập ở đầu bài, mặc định đóng |

Con số 1320px là **đo được**, không phải ước lượng: ở 1240px lề trái chỉ dư 7px.
JavaScript không lặp lại con số này — nó đọc `position` của phần tử để biết đang
ở chế độ nào, nên sửa CSS là JS tự đi theo.

## Trang chuyên mục và danh sách bài

`category.php` và `home.php` **tự dựng toàn bộ trang**, không đi qua khung nội
dung của Kadence — cùng cách làm với `search.php`. Mọi khối nằm ở `inc/archive.php`
và dùng chung cho cả hai trang; trang danh sách bài chỉ khác ở chỗ không có
chuyên mục nào nên truyền `$term = null`.

Thứ tự khối, từ trên xuống:

1. **Đầu trang** — đường dẫn (kèm schema `BreadcrumbList`), tên, mô tả, ba con
   số, hộp "Bắt đầu từ đâu", dải chip chuyên mục.
2. **Danh sách hàng ngang** — ảnh nhỏ bên trái, mô tả hai dòng.
3. **Dải tra đá hợp mệnh** — chèn sau bài thứ tư, chỉ ở trang 1.
4. **Phân trang** — kèm dòng "Trang 1/3 — đang xem bài 1–10 trong 25 bài".
5. **Đọc nhiều nhất** — dải cuối trang, thay cho khối cũ ở cột bên.

**Không có cột bên.** Đó là chủ ý, không phải thiếu sót: 300px đó đổi lấy chiều
rộng cho nội dung. `inc/enqueue.php` vì thế cũng bỏ nạp `sidebar.css` ở hai trang
này.

### Ba bài nền tảng

Hộp "Chưa biết bắt đầu từ đâu?" ở đầu trang chuyên mục. Chọn ở **Bài viết →
Chuyên mục → Sửa**, ô "Ba bài nền tảng".

Chưa chọn thì hộp tự lấy ba bài đọc nhiều nhất trong chuyên mục. Chưa đủ số liệu
lượt xem thì hộp **không hiện** và đầu trang tự thu về một cột — không bao giờ để
lại một ô trống bên phải.

Trang danh sách bài không có hộp này: nó là ba bài nền tảng của *một* chuyên mục,
mà ở đó không có chuyên mục nào.

### Câu dẫn ở đầu trang danh sách bài

Lấy từ **nội dung** của trang "Bài viết" (trang được chọn ở Cài đặt → Đọc). Nội
dung đó bình thường bị WordPress vứt bỏ, nên đem ra làm câu dẫn là chỗ dùng đúng
nhất — sửa ngay trong trình soạn thảo, không cần thêm ô cấu hình.

Với trang chuyên mục, câu dẫn lấy từ ô **Mô tả** của chuyên mục. Để trống thì
khối này không hiện.

### Ngày hiển thị

Chỉ hiện **"Cập nhật T8/2026"**, không hiện ngày đăng. Nội dung chuyển về từ bản
cũ mang ngày đăng 2019–2020 trong khi bài đã soát lại — in ngày đăng gốc làm cả
chuyên mục trông bỏ hoang sáu năm.

### Nhãn chuyên mục trên mỗi dòng

Có ở trang danh sách bài, **không** có ở trang chuyên mục: ở đó nhãn trùng đúng
tiêu đề trang, in lại trên từng dòng là nhiễu.

### Bài ghim

Bài ghim ("Dán bài này lên trang chủ") **chỉ dùng để chọn bài cho khối nổi bật ở
trang chủ**. Chúng không được nhấc lên đầu `/bai-viet/` nữa — mặc định đó của
WordPress khiến ba bài ghim bị in hai lần, 82 dòng cho 79 bài.

### Trang thẻ và lưu trữ theo ngày

Vẫn dùng khung Kadence. Phần cuối `inc/archive.php` giữ những gì cắm vào đó:
thời gian đọc trên thẻ bài, và hai mặc định đã đổi vì cùng một lý do — bớt nhiễu:

| Đổi gì | Vì sao |
|---|---|
| Bỏ nút "Đọc tiếp" | Cả thẻ bài đã là liên kết. Thêm nút chỉ là đích bấm thứ hai tới cùng một chỗ. |
| Bỏ tên tác giả trên thẻ | Site một người viết. "By admin" lặp 81 lần không thêm thông tin gì. |

Một cái bẫy đã mắc phải, đọc trước khi sửa: **bỏ `readmore` khỏi
`post_archive_elements` là không đủ.** `entry_actions.php` đọc từ tùy chọn riêng
`post_archive_element_readmore`, và tự bật lên nếu tùy chọn đó vắng mặt.

## Mẫu khối (block pattern)

Bốn mẫu dùng khi viết bài, nằm trong nhóm **Đá Quý Lục Yên** ở bảng chọn mẫu của
trình soạn thảo:

| Mẫu | Dùng khi nào |
|---|---|
| Hộp tóm tắt đầu bài | Đặt ngay đầu bài, liệt kê 3–5 câu hỏi bài sẽ trả lời |
| Bảng thông số đá | Độ cứng, chiết suất, tỉ trọng… đặt sau đoạn mở đầu |
| Hộp cảnh báo hàng giả | Nhắc thủ đoạn làm giả hoặc sai lầm phổ biến khi mua |
| Bảng so sánh thật và giả | Hai cột đối chiếu đá tự nhiên với đá xử lý |

Mỗi mẫu là một file trong `patterns/`. WordPress tự quét thư mục đó — không cần
gọi `register_block_pattern()`. Chỉ `inc/patterns.php` tạo nhóm.

### Bẫy: thêm mẫu mới mà không thấy nó đâu

WordPress **nhớ tạm danh sách file trong `patterns/`**, và chỉ quét lại khi số
`Version` trong `style.css` đổi. Thêm file mẫu mới mà quên tăng phiên bản thì mẫu
đó không xuất hiện trong trình soạn thảo, **và không có thông báo lỗi nào**.

Quy tắc: thêm hoặc đổi tên file trong `patterns/` thì tăng `Version` trong
`style.css` và `DQL_VERSION` trong `functions.php`. Hai số này phải luôn khớp nhau.

Kiểu dáng của các mẫu nằm ở `assets/css/blocks.css`. File này nạp ở **cả** ngoài
trang lẫn trong trình soạn thảo, nên chỉ có một bản duy nhất phải bảo trì. Mọi
bộ chọn trong đó chỉ được dựa vào class của chính khối, không dựa vào cấu trúc
trang bao ngoài — vì trình soạn thảo không có cấu trúc đó.

## Trang từ điển

`archive-dql_thuat_ngu.php` — danh sách A–Z, nhóm theo chữ cái đầu, có chỉ mục
nhảy nhanh ở đầu trang. Không dùng lưới thẻ như trang chuyên mục: từ điển là thứ
người ta **tra**, không phải thứ người ta lướt.

Chữ cái nhóm được lấy sau khi bỏ dấu, nên *Đa sắc*, *Đá tổng hợp*, *Độ trong* đều
nằm chung nhóm **D** — đúng cách người Việt tra từ điển.

Loại nội dung do plugin `dql-core` đăng ký. Tắt plugin là trang này không còn
được gọi tới.

## Tìm kiếm

### Gợi ý tức thời ở trang chủ

Gõ từ 2 ký tự trở lên, hộp gợi ý hiện ngay dưới ô tìm kiếm, **đã phân sẵn hai
nhóm**: Từ điển trước, Bài viết sau. Từ điển đứng trước vì khi ai đó gõ một thuật
ngữ, thứ họ cần là định nghĩa ngay, không phải danh sách bài có nhắc tới nó.

Dùng REST API (`/wp-json/dql/v1/search?q=…`) chứ không dùng `admin-ajax.php`:
đường dẫn sạch, trả JSON đúng chuẩn, không phải nạp tầng admin cho mỗi phím gõ.

Ba chi tiết quan trọng trong `assets/js/search.js`:

| Chi tiết | Vì sao |
|---|---|
| Chờ 250ms sau phím cuối | Không chờ thì mỗi phím là một truy vấn |
| `AbortController` hủy yêu cầu cũ | Không hủy thì kết quả lần gõ trước có thể về sau và ghi đè, cho gợi ý không khớp chữ đang có |
| Ngưỡng tối thiểu 2 ký tự | Dưới ngưỡng thì gần như mọi bài đều khớp, kết quả vô nghĩa |

Bàn phím: mũi tên lên xuống chọn, Enter mở, Escape đóng. Ô nhập mang
`role="combobox"`, hộp mang `role="listbox"`.

**Tắt JavaScript vẫn dùng được**: ô tìm kiếm vốn là form gửi bình thường, script
chỉ thêm phần gợi ý.

### Trang kết quả

`search.php` **không dùng truy vấn chính**. Truy vấn tìm kiếm mặc định của
WordPress trộn mọi loại nội dung vào một danh sách xếp theo độ liên quan, nên bài
viết và mục từ điển nằm xen kẽ — người đọc không biết mình đang nhìn cái gì. Thay
vào đó chạy hai truy vấn riêng và bày thành hai nhóm.

Đoạn trích cắt còn **24 từ**: đủ để biết bài nói gì, không đủ để danh sách kết
quả dài lê thê. Từ điển chỉ hiện ở trang đầu — sang trang 2 là người đọc đang
duyệt bài viết.

Không tìm thấy gì thì hiện vài từ khóa gợi ý thay vì một trang trống.

## Footer

Footer do `inc/footer.php` dựng, **không phải footer builder của Kadence**. Cách
thay: gỡ hook `Kadence\footer_markup` khỏi action `kadence_footer` rồi gắn hàm
của mình vào cùng chỗ — giữ nguyên mọi wrapper và hook khác của Kadence, và trả
lại footer gốc chỉ bằng việc xóa một dòng trong `dql_replace_footer()`.

**Đánh đổi cần biết: các tùy chọn Footer trong Customizer của Kadence không còn
tác dụng.**

Hai tầng: tầng nội dung 4 cột nền xanh đậm, tầng bản quyền đậm hơn một bậc.
Chuyên mục và liên kết lấy động từ database và menu chân trang, nên không phải
sửa code khi thêm chuyên mục mới.

## Chỗ đặt quảng cáo

Sáu vùng quảng cáo và ba ô tự do, vào **Giao diện → Cấu hình cột tiện ích**:

| Vùng | Vị trí |
|---|---|
| Quảng cáo — trang chủ | Giữa khối bài nổi bật và ba trụ nội dung, chạy ngang trang |
| Quảng cáo — giữa bài viết | Tự chèn sau đoạn văn thứ 3 |
| Quảng cáo — cuối bài viết | Sau nội dung bài, trước bình luận |
| Quảng cáo — cột bên trang chủ | Ô đầu cột bên trang chủ |
| Quảng cáo — cột bên bài viết (trên) | Ô đầu cột bên bài viết, ngang tầm đoạn mở bài |
| Quảng cáo — cột bên bài viết (dưới, dính) | Ô cuối cột bên bài viết, dính khi cuộn |
| Cột bên trang chủ — ô tự do | Cuối cột bên trang chủ |
| Cột bên bài viết — ô tự do | Trước ô quảng cáo dính |
| Cột bên — trang chuyên mục | Toàn bộ cột bên trang chuyên mục và tìm kiếm |

Vị trí đáng tiền nhất là **ô đầu cột bên** (thấy được mà không phải cuộn) và **ô
cuối cột bên bài viết** (dính lại nên theo người đọc suốt bài dài).

Dùng widget chứ không dùng ô nhập trong Customizer, để nhận được mọi thứ: mã
AdSense, ảnh banner tự đặt, hay một khối HTML bất kỳ. **Vùng không có widget thì
không in ra gì**, không để lại khoảng trống.

Mỗi vùng quảng cáo tự động có nhãn "Quảng cáo" phía trên. Đây là yêu cầu về minh
bạch — người đọc có quyền biết đâu là nội dung, đâu là quảng cáo. Nhãn nằm ở
`.dql-ad::before` trong `main.css`.

### Bẫy: `display: flex` làm AdSense không hiện

**`.dql-ad__item` phải là `display: block`.** Đã mắc lỗi này một lần.

Quảng cáo responsive của Google là thẻ `<ins class="adsbygoogle">` **rỗng**.
Script của Google đo chiều rộng phần tử cha để chọn cỡ quảng cáo. Trong một flex
container, thẻ ins rỗng thành flex item và co về **chiều rộng 0** — AdSense đọc
được 0px nên không trả quảng cáo nào về.

Lỗi hoàn toàn im lặng: không thông báo, không lỗi console, chỉ là chỗ đó trống
mãi. Căn giữa bằng `text-align` và `margin-inline: auto`, không bằng flex.

### Vị trí chèn giữa bài

Sau đoạn văn thứ 3, và **bỏ qua bài dưới 6 đoạn** — chèn quảng cáo vào giữa một
bài ngắn là cắt đôi nó. Đổi hai con số này bằng bộ lọc `dql_ad_after_paragraph`
và `dql_ad_min_paragraphs`.

## Logo

Bản 2.0 (29/10/2026): phương án "Ruby trong đá hoa trắng". Huy hiệu sáu cạnh
lệch đúng dấu hiệu nhận diện trên canvas (gradient ruby có hạt, icon viên đá nét
trắng), đáy huy hiệu là đá hoa trắng Lục Yên có vân, viên đá mọc liền từ đá. Tên
site một dòng Fraunces 600, "Lục Yên" màu ruby. Bản gốc SVG nằm ở `assets/brand/`
(chữ đã chuyển thành đường nét, không cần font); PNG tải lên Thư viện media.

| File | Dùng ở đâu | Gắn qua |
|---|---|---|
| `logo-header.png` (1200×231) | Header, nền sáng | Customizer → Site Identity → Logo |
| `logo-header-trang.png` (1200×231) | Footer nền than đá | Customizer → Footer → "Logo footer" (theme mod `dql_footer_logo`) |
| `favicon.png` (512×512) | Biểu tượng trình duyệt | Customizer → Site Identity → Site Icon |

Tỉ lệ 5,2:1, bằng logo xanh của bản 1.x, nên `logo_width` 165/150/135 trong
`inc/brand.php` giữ nguyên và menu không đổi ở màn 1045px. PNG xuất 1200px
(gấp đôi cỡ hiển thị) để nét trên màn Retina; WordPress tự tạo bản nhỏ.

### Hai bẫy đã mắc

**1. Kadence đọc tùy chọn bằng `get_theme_mod( $key )` trực tiếp.**
Không lồng trong mảng `kadence_settings`. Ghi
`set_theme_mod( 'kadence_settings', [...] )` là vô tác dụng và không báo lỗi gì.

**2. Có logo rồi vẫn phải tắt tên site dạng chữ.**
Mặc định Kadence là `logo_layout.include = 'logo_title'` — in cả logo lẫn tên
site. Tên site ở đây dài, khiến khối thương hiệu chiếm **591px** trong khi ảnh
logo chỉ 165px, và menu bị đẩy tràn xuống **ba dòng**. Đã đặt `'logo'`.

Cả hai mặc định nằm trong `inc/brand.php`, đổi được trong Customizer.

## Cột bên

**Ba trang, ba cột bên khác nhau.** Trước đây cả ba dùng chung một vùng widget
`sidebar-primary`, và hậu quả rất cụ thể: widget "Bài viết mới nhất" đặt vào đó
hiện ngay cạnh khối "Bài mới nhất" của trang chủ — cùng một danh sách in hai lần,
cách nhau vài centimet. Và vì chỉ có một vùng nên không thể cho trang chủ và
trang bài hai nội dung khác nhau.

Nội dung cột bên do `inc/sidebar.php` dựng, kiểu dáng ở `assets/css/sidebar.css`.

| Trang | Gồm những gì (từ trên xuống) |
|---|---|
| **Trang chủ** | Quảng cáo · Khám phá theo chủ đề · Đăng ký nhận bài · Từ điển đá quý · Ô tự do |
| **Bài viết** | Quảng cáo · Cùng chuyên mục · Đăng ký nhận bài · Tra đá hợp mệnh · Ô tự do · Quảng cáo dính |
| **Chuyên mục** | Đọc nhiều nhất · rồi tới widget — vùng "Cột bên — trang chuyên mục" |

Ràng buộc khi chọn các khối này: **không khối nào lặp lại nội dung của cột chính
bên cạnh nó.** Vì vậy cột bên trang chủ liệt kê chuyên mục **con** chứ không phải
ba chuyên mục cha — ba cái đó đã có nguyên một dải riêng ở giữa trang. Và cột bên
bài viết dùng "cùng chuyên mục" chứ không dùng "bài mới nhất": người đọc đã tự
chọn chủ đề bằng việc mở bài này, danh sách bài mới trả về đúng thứ họ vừa bỏ qua.

Bật/tắt từng khối ở **Customizer → Cột bên**. Tắt hết khối của trang chủ và không
đặt widget nào thì khối "Bài mới nhất" tự về một cột, không để lại khoảng hở.

### Khối "Đọc nhiều nhất"

Số liệu do **plugin `dql-core`** đếm, không do theme — lượt xem là dữ liệu, phải
sống sót khi đổi theme. Theme chỉ đọc `DQL_Luot_Xem::top()` và hiển thị.

Khối chỉ hiện khi có **ít nhất 3 bài** đã có lượt xem. Chưa đủ thì khách không
thấy gì, quản trị viên thấy một ô nhắc — một bảng "đọc nhiều" chỉ có một dòng
trông như trang đang hỏng.

Danh sách này **bỏ ảnh, thay bằng số thứ tự**. Hai danh sách ảnh vuông đứng gần
nhau trong một cột hẹp trông như một danh sách bị lặp; con số vừa phân biệt được
hai khối, vừa nói đúng điều khối này muốn nói.

Kiểm tra việc đếm có chạy không: **Bài viết → cột "Lượt xem"** trong trang quản
trị, bấm được để sắp xếp.

Bố cục hai cột vẫn là của Kadence, không tự dựng lại — bật tắt được trong
**Customizer → Bài viết / Trang lưu trữ → Bố cục**. Mặc định trong `inc/brand.php`:
`post_layout` và `post_archive_layout` đều là `'right'`.

### Ô cuối cột dính khi cuộn

Ô **cuối cùng** của cột bên dính lại khi cuộn, nên nó theo người đọc suốt một bài
dài. Nhắm bằng `:last-child` chứ không bằng class gắn tay, để luôn đúng dù chủ
site đã đặt widget vào ô quảng cáo cuối hay chưa.

**Bẫy:** `position: sticky` hỏng hoàn toàn im lặng nếu một tổ tiên có `overflow`
khác `visible`. Kadence đặt `#wrapper { overflow: clip }` để chặn tràn ngang, đủ
để giết sticky ở mọi nơi trong trang — quy tắc `.dql-latest__cot-dinh` ở bản
trước chưa bao giờ chạy vì lý do này. `sidebar.css` mở lại `overflow` cho
`.has-sidebar #wrapper` và `.home #wrapper`; đó cũng là đúng thứ Kadence tự làm
khi bật tùy chọn cột bên dính của nó.

### Bẫy: độ ưu tiên của CSS động Kadence

Kadence in ra khối `<style>` động dùng bộ chọn ba class như
`.primary-sidebar.widget-area .widget`. Mọi khai báo một class trong `sidebar.css`
đều thua nó — **và chỉ thua ở trang bài viết**, vì trang chủ không có lớp bọc
`.primary-sidebar`. Kiểu lỗi này rất dễ lọt: thẻ trông đúng ở một trang, sai ở
trang kia.

Hai cách xử lý đã dùng, ghi ngay trong file:

- **Nền và viền** đi qua biến `--dql-sb-nen` / `--dql-sb-vien`. Giá trị đặt trên
  chính phần tử luôn thắng, bất kể quy tắc dùng biến có bộ chọn dài tới đâu.
- **Màu chữ** đặt ở lớp bọc bên trong (`.dql-sb-menh`), nơi bộ chọn của Kadence
  không với tới.

### Hệ quả: mục lục mất chế độ cột lề trái

Khi bài viết có cột bên, cột đó chiếm phần lề và **mục lục buộc phải giữ dạng
thanh gập** ở đầu bài. Đo thực tế ở màn 1440px khi chưa xử lý: mục lục rơi xuống
`left: -113px`, hơn nửa cột nằm ngoài màn hình.

CSS trong `single.css` đã khóa chế độ cột lề bằng `body:not(.has-sidebar)`. Tắt
cột bên cho bài viết trong Customizer thì mục lục tự quay lại lề trái —
JavaScript đi theo vì nó đọc `position` thật của phần tử.

## Khối đăng ký email

Theme chỉ dựng khung: tiêu đề, mô tả, và chỗ đặt form. Bản thân form do plugin
lo — theme không nên đụng vào việc lưu email của người đọc, vì đó là dữ liệu phải
sống sót khi đổi theme.

Tạo form bằng **WPForms** (đã cài sẵn), rồi dán mã nhúng vào
**Customizer → Trang chủ Đá Quý → Mã form đăng ký email**, dạng `[wpforms id="123"]`.

Chưa dán mã thì khối này **chỉ quản trị viên thấy**.

**Dán một lần, hiện ba chỗ**: dải lớn cuối trang chủ, thẻ trong cột bên trang chủ,
thẻ trong cột bên bài viết. Chữ của hai thẻ cột bên ngắn hơn và sửa riêng ở
**Customizer → Cột bên** — câu mô tả của dải lớn xuống bảy dòng trong một cột
rộng 300px.

Màu, bo góc và màu nút của form đặt qua **biến `--wpforms-*`** trong `sidebar.css`,
không viết đè lên CSS của plugin. Đó là đường mà WPForms dự tính, nên bản cập nhật
sau không phá. Ngoại lệ: cỡ chữ, chiều cao ô và đệm khung được WPForms gán thẳng
vào `#wpforms-N` bằng bộ chọn ID — không ghi đè được bằng class, phải chỉnh trong
phần **Giao diện** của chính WPForms.

## Quy ước code

- Tiền tố `dql_` cho hàm PHP, `.dql-` cho class CSS. Không đụng tên với Kadence.
- Trong CSS luôn dùng biến `--global-palette*`, không viết mã màu trực tiếp.
- Không dùng `!important`. CSS của child theme đã được nạp sau Kadence.
  Ngoại lệ duy nhất: khối `prefers-reduced-motion`.
- Không có bước build. Sửa file CSS/JS là thấy kết quả ngay.

## Đang làm dở

- [x] Giai đoạn 1 — nền tảng: màu, chữ, CSS cơ sở
- [x] Giai đoạn 2a — trang chủ "Cổng kiến thức"
- [x] Cấu trúc nội dung, trang tĩnh, menu chính
- [x] Giai đoạn 2b — trang bài viết đơn: mục lục tự động, thời gian đọc
- [x] Trang chuyên mục và trang danh sách bài
- [x] Giai đoạn 3 — block patterns (tóm tắt, thông số đá, cảnh báo, bảng so sánh)
- [x] Giai đoạn 4a — plugin `dql-core`: công cụ tra đá hợp mệnh
- [x] Giai đoạn 4b — từ điển thuật ngữ đá quý
- [x] Logo, favicon, logo footer bản chữ trắng
- [x] Tìm kiếm tức thời + trang kết quả phân nhóm
- [x] Cột bên, các vùng quảng cáo, khối đăng ký email
- [x] Cột bên riêng cho trang chủ và cho trang bài viết
- [x] Rà soát toàn bộ trước khi đưa lên chạy thật
- [x] Logo chữ và favicon
- [x] Đã chạy thật trên daquylucyen.net.vn, đồng bộ code qua GitHub + WP Pusher
- [x] 2.0 — thiết kế "Đá Thô" chốt bản 1 (28/10/2026), code theo canvas, rà WCAG/hiệu năng
- [ ] 2.0 — kiểm thử trên LocalWP theo `docs/kiem-thu-localwp.md`, rồi mới deploy
- [ ] 2.0 — tự host Fraunces biến thiên (trục SOFT, opsz), preload 2 file

**Màu viên đá trong logo cố ý lệch khỏi bảng màu site** — là quyết định, không
phải sai sót. Xem `docs/thiet-ke.md`.

**Nội dung từ điển nằm trong database, không nằm trong file.** Sửa ở menu
**Từ điển** trong trang quản trị. Trước đây plugin có `includes/data/thuat-ngu-mau.php`
chứa bản sao 20 thuật ngữ, nhưng không dòng code nào đọc nó — sửa file đó không
đổi được gì trên site, nên đã xoá ở `dql-core` 1.1.1. Cần gieo lại từ đầu cho một
site khác thì dùng bản xuất WXR, không dùng file PHP.

Còn `dql-core/includes/data/menh-goi-y.php` thì ngược lại: đó **là** nguồn dữ
liệu thật của công cụ tra mệnh, sửa file là đổi ngay.
