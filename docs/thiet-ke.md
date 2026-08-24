# Thiết kế theme Đá Quý Lục Yên

Ngày chốt: 03/08/2026. Ghi lại **quyết định và lý do**, không phải hướng dẫn sử dụng
(phần đó ở `README.md`).

## Bối cảnh

Blog chia sẻ kiến thức về đá quý và trang sức, vùng Lục Yên. Xây lại từ đầu trên
nền Kadence. Không bán hàng trực tuyến — mục tiêu là người đọc hiểu đúng trước
khi mua ở nơi khác.

## Quyết định nền

| Quyết định | Lý do |
|---|---|
| Child theme của Kadence, không viết theme từ số không | Kadence lo header/footer builder, tối ưu tốc độ, tương thích plugin. Viết lại những thứ đó không mang lại giá trị nào cho người đọc. |
| Tính năng dữ liệu nằm ở plugin `dql-core`, không nằm trong theme | Từ điển thuật ngữ và dữ liệu tra mệnh phải sống sót khi đổi theme. WordPress coi Custom Post Type là chức năng của theme đang bật — để trong theme là tự đặt bẫy cho mình. |
| Không có bước build (không npm) | Chủ site muốn tự sửa code. Thêm một bước build là thêm một rào cản mỗi lần muốn đổi một dòng CSS. |
| Màu mặc định đặt bằng PHP, không đặt trong `theme.json` | Kadence lấy màu từ Customizer rồi bơm ra biến `--global-palette*`. Khai báo lại bảng màu trong `theme.json` sẽ cắt liên kết đó. |

## Bộ nhận diện — phương án "Sapphire sáng"

Yêu cầu ban đầu có mâu thuẫn: muốn *tươi sáng thân thiện* nhưng chọn màu
*sapphire* — vốn trầm và lạnh. Cách hòa giải: **sapphire chỉ làm điểm nhấn trên
nền trắng**, không phủ xanh khắp trang.

Màu và chữ: xem bảng trong `README.md`.

Hai ràng buộc kỹ thuật không được phá:

1. **Bộ ký tự `vietnamese` phải bật.** Tắt là chữ có dấu rơi về font hệ điều hành,
   nhìn lệch hẳn so với chữ không dấu — lỗi rất khó phát hiện bằng mắt.
2. **`heading_font['variant']` phải là mảng.** Kadence chỉ quét weight của h1–h6
   khi giá trị này là mảng; truyền chuỗi thì chỉ tải weight 400 và trình duyệt
   bôi đậm giả. Đã mắc lỗi này một lần, đã sửa, đã ghi chú trong `inc/brand.php`.

## Bố cục trang chủ — phương án "Cổng kiến thức"

Người vào blog đá quý thường mang theo **một câu hỏi cụ thể** ("đá này thật hay
giả", "mệnh Kim hợp đá gì"), chứ không vào để xem có gì mới. Nên trang chủ dẫn
bằng **chủ đề**, không dẫn bằng danh sách bài mới.

Thứ tự khối, từ trên xuống:

1. **Câu định vị + ô tìm kiếm** — trả lời ngay "đây là chỗ gì".
2. **Ba trụ nội dung** — Kiến thức đá quý · Phong thủy, hợp mệnh · Kiểm định.
   Mỗi trụ là một chuyên mục, chọn được trong Customizer.
3. **Ô tra đá hợp mệnh** — công cụ có giá trị nhất, đặt nơi dễ thấy.
4. **Bài mới nhất** — cuối cùng, vì đây là thứ ít quan trọng nhất với người mới.

Đã cân nhắc và loại:

- **Tạp chí ba trục** (hero lớn + nhiều dải bài): cần 9–12 bài mới đầy màn hình đầu.
  Sẽ trông trống nếu blog còn ít bài.
- **Dòng chảy đọc** (danh sách + cột bên): đơn giản nhất, nhưng ba trục nội dung
  chìm nghỉm — mất đúng thứ tạo khác biệt cho site này.

Ưu điểm quyết định của phương án đã chọn: **không bị trống khi ít bài**, vì ba trụ
là chuyên mục chứ không phải bài viết.

## Cột bên: vì sao tách làm ba, và vì sao theme tự dựng nội dung

Bản đầu dùng **một** vùng widget `sidebar-primary` cho cả trang chủ, trang bài
viết và trang chuyên mục. Hai hậu quả, cái sau nặng hơn cái trước:

1. Widget "Bài viết mới nhất" trong đó hiện **ngay cạnh** khối "Bài mới nhất" của
   trang chủ. Cùng một danh sách in hai lần, cách nhau vài centimet.
2. Không có cách nào cho trang chủ và trang bài hai nội dung khác nhau. Một vùng
   widget chỉ có một nội dung.

Đã cân nhắc ba hướng:

| Hướng | Vì sao loại / chọn |
|---|---|
| Giữ một vùng, chỉ đổi widget bên trong | Không giải quyết được vấn đề 2. Chủ site vẫn phải chọn một nội dung dùng cho mọi trang. |
| Ba vùng widget rỗng, chủ site tự lắp | Giải quyết cả hai, nhưng cột bên mặc định **trống trơn**. Yêu cầu là "đẹp, chuyên nghiệp ngay", không phải "có chỗ để tự làm cho đẹp". |
| **Theme dựng sẵn các khối, xen giữa là vùng widget** | ✔ Đã chọn. Cột bên đẹp ngay khi chưa cấu hình gì, mà vẫn còn chỗ trống để đặt quảng cáo và nội dung riêng. |

Hướng đã chọn đi cùng cách theme này vốn làm ở chỗ khác: trang chủ, footer và các
vùng quảng cáo đều là khối do PHP dựng, còn widget chỉ dùng cho phần chủ site
thật sự cần tự do.

### Ràng buộc khi chọn khối: không lặp lại cột chính bên cạnh

Đây là ràng buộc quyết định nội dung của cả hai cột bên, không phải một lưu ý phụ.

- Cột bên **trang chủ** liệt kê chuyên mục **con**, không liệt kê ba chuyên mục
  cha — ba cái đó đã có nguyên một dải riêng ("ba trụ nội dung") ở giữa trang.
- Cột bên **bài viết** dùng "cùng chuyên mục", không dùng "bài mới nhất". Người
  đọc đã tự chọn chủ đề bằng việc mở bài này; danh sách bài mới trả về đúng thứ
  họ vừa bỏ qua ở trang chủ.

### Đếm lượt xem: đã làm, nhưng không theo cách bị loại từ đầu

Khối "bài đọc nhiều" ban đầu bị loại vì lý do ghi ở mục cuối tài liệu này: ghi
database mỗi lần tải trang sẽ giết bộ nhớ đệm. **Lý do đó vẫn đúng** — thứ được
làm lại là cách đếm, không phải quyết định về bộ nhớ đệm.

Cách hay gặp là tăng biến đếm ngay trong lúc PHP dựng trang. Nó hỏng theo hai
chiều ngược nhau, không chiều nào chấp nhận được:

| Tình huống | Hậu quả |
|---|---|
| Có bộ nhớ đệm toàn trang | PHP không chạy từ lượt xem thứ hai trở đi. Con số đứng im — càng đông người đọc càng sai. |
| Tắt bộ nhớ đệm để đếm cho đúng | Mỗi lần tải trang một lệnh ghi database. Đúng thứ đã bị loại. |

Cách đã dùng: trang vẫn được đệm bình thường, việc đếm do **một request nhỏ
riêng** lo, gửi sau khi trang đã hiện xong (`navigator.sendBeacon`). Người đọc
không phải chờ nó, và bộ nhớ đệm không bị đụng tới. Toàn bộ nằm ở plugin
`dql-core`, vì lượt xem là dữ liệu — phải sống sót khi đổi theme.

Ba lớp chặn đếm trùng, từ rẻ tới đắt: `sessionStorage` ở trình duyệt → khóa tạm
6 giờ phía máy chủ (IP được **băm một chiều** cùng muối của site, tự hết hạn,
không lưu ở dạng đọc được) → bỏ qua người có quyền sửa bài.

Xếp hạng theo **tổng lượt xem từ trước tới nay**, nên bài cũ có lợi thế tích lũy.
Đánh đổi có ý thức: xếp theo cửa sổ 30 ngày cần lưu số liệu từng ngày cho từng
bài, đắt hơn hẳn và chưa xứng ở quy mô này. Đổi bằng bộ lọc
`dql_top_luot_xem_args`.

**Bẫy đã sập một lần:** bảng xếp hạng được nhớ tạm một giờ. Lúc site vừa bật tính
năng, bảng đang rỗng — và cái rỗng đó cũng bị nhớ một giờ, nên lượt xem đổ về
ngay sau đó không làm khối hiện ra. Người đi tìm lỗi sẽ soi chỗ đếm, nơi không có
lỗi nào. Đã sửa bằng hai lớp: xóa bảng khi có bài chưa nằm trong đó nhận lượt
xem, và kết quả rỗng chỉ nhớ 5 phút thay vì một giờ.

### Những gì cố ý KHÔNG đưa vào cột bên

- **Bình luận gần đây.** Blog kiến thức, không phải diễn đàn. Số bình luận chưa
  đủ để khối này có gì đáng đọc, và một khối gần như rỗng làm cả cột trông hỏng.
- **Nhúng thẳng công cụ tra mệnh.** Công cụ trả kết quả bằng cách tải lại trang
  kèm tham số; nhúng vào cột bên sẽ ném người đọc ra khỏi bài họ đang đọc dở.
  Thay bằng một thẻ mời bấm sang công cụ ở trang chủ.

### Chỉ một thẻ được nổi

Toàn bộ cột là thẻ trắng viền mảnh, trừ hai ngoại lệ có tính toán: thẻ đăng ký
email nền xám nhạt, và thẻ tra mệnh nền xanh đậm — và hai thẻ này không bao giờ
xuất hiện cùng vai trò ở một cột. Ba bốn thẻ cùng nổi thì không thẻ nào nổi.

## Trang chuyên mục — phương án "Cổng chủ đề" (04/08/2026)

Bản trước dùng nguyên khung của Kadence: hero chỉ có mỗi H1, dải chip trôi lửng
trong vùng nội dung, lưới thẻ ba cột bị cột bên bóp còn hai, và mỗi thẻ in lại
nhãn chuyên mục đúng bằng tiêu đề trang.

Đã dựng mockup hai phương án (`docs/mockup-chuyen-muc.html`) và chốt phương án B.

| Quyết định | Lý do |
|---|---|
| `category.php` tự dựng toàn bộ, không đi qua khung Kadence | Bố cục là danh sách hàng ngang không cột bên; Kadence chỉ dựng lưới thẻ có cột bên. Uốn cái này thành cái kia bằng CSS thì mỗi lần Kadence đổi markup là một lần trang gãy. Cùng cách làm với `search.php`. |
| Danh sách hàng ngang thay lưới thẻ | Mỗi màn hình hiện 4–5 bài kèm mô tả, so với 3 bài của lưới. Người vào blog kiến thức quét tiêu đề để tìm đúng bài, không lướt xem ảnh. |
| Bỏ cột bên ở trang chuyên mục | Đúng mục 6.2 của bản spec. Chủ site xác nhận không bán quảng cáo ở đây, nên 300px đó đổi lấy chiều rộng cho nội dung là lãi. Khối "Đọc nhiều nhất" chuyển xuống dải cuối trang, vẫn bật/tắt bằng đúng tùy chọn cũ. |
| Chỉ hiện **ngày cập nhật**, bỏ ngày đăng | Nội dung chuyển về từ bản cũ mang ngày đăng 2019–2020 trong khi bài đã soát lại. In ngày đăng gốc làm chuyên mục trông bỏ hoang sáu năm — một thông tin sai về thứ người đọc sắp đọc. Chỉ tới tháng: "T8/2026" đã trả lời đúng câu hỏi, ngày cụ thể chỉ thêm nhiễu. |
| Hộp "Chưa biết bắt đầu từ đâu?" chọn tay | "Bài nền tảng" là một đánh giá về nội dung, máy không suy ra được. Chọn ở màn Sửa chuyên mục. Chưa chọn thì lùi về ba bài đọc nhiều nhất trong chuyên mục; chưa đủ số liệu thì hộp ẩn và đầu trang tự thu về một cột — không bao giờ để lại ô trống. |
| Dải "Đọc nhiều nhất" ưu tiên bài **trong chuyên mục** | Người đọc đã tự chọn chủ đề bằng việc mở trang này. Chỉ khi chuyên mục chưa đủ 4 bài có lượt xem mới lùi về bảng xếp hạng toàn site, và khi đó tiêu đề đổi thành "trên trang" — không nhận vơ phạm vi. |
| Thêm breadcrumb kèm schema `BreadcrumbList` | Site không cài plugin SEO nào, ở đây không in thì không nơi nào in. |

Đã cân nhắc và loại phương án A ("Tinh gọn"): giữ lưới Kadence, chỉ sửa đầu trang
và thẻ bài. Ít code riêng hơn, nhưng không giải quyết được việc người mới vào một
chuyên mục 25 bài không biết bài nào là gốc.

### Trang danh sách bài dùng chung bộ khối này (04/08/2026)

Ban đầu chỉ chuyển trang chuyên mục, để trang `/bai-viet/` lại cho khung Kadence.
Sai: hai trang cùng là danh sách bài mà một bên danh sách hàng ngang không cột
bên, một bên lưới thẻ có cột bên — đi từ trang này sang trang kia tưởng lạc sang
site khác. `home.php` giờ gọi đúng các hàm của `inc/archive.php`, chỉ truyền
`$term = null`.

Ba chỗ khác nhau, đều suy ra từ việc không có chuyên mục nào:

| | Trang chuyên mục | Trang danh sách bài |
|---|---|---|
| Hộp "Bắt đầu từ đâu" | Có — ba bài nền tảng của chuyên mục | Không. Dải chip ba chuyên mục cha đã làm việc dẫn lối |
| Nhãn chuyên mục trên mỗi dòng | Không — trùng đúng tiêu đề trang | **Có** — bài đến từ mọi chuyên mục nên nhãn mới có việc để làm |
| Số liệu đầu trang | Trong chuyên mục, tính cả bài của con | Toàn bộ bài viết của site |

Mô tả ở đầu trang lấy từ **nội dung** của trang "Bài viết" — thứ WordPress bình
thường vứt bỏ khi một trang được chọn làm trang danh sách bài. Chủ site sửa câu
dẫn ngay trong trình soạn thảo quen thuộc, không phải thêm một ô cấu hình mới.
Không chạy qua bộ lọc `the_content`: chỉ cần một đoạn dẫn ngắn, để nguyên bộ lọc
thì một shortcode hay một khối ảnh lọt vào sẽ phá vỡ đầu trang.

### Bài ghim không còn nhảy lên đầu /bai-viet/

Lỗi có sẵn từ trước, chỉ lộ ra khi dòng đếm vị trí cho thấy trang 1 dài 13 bài
còn các trang khác 10. Đo ngày 04/08/2026: **82 dòng in ra cho 79 bài** — ba bài
ghim hiện ở đầu trang 1 rồi hiện lại đúng vị trí theo ngày đăng ở trang sau.

Nguyên nhân là mặc định của WordPress: nó nhấc bài ghim lên đầu chỉ mục blog.
Trên site này việc đó sai ngay từ ý nghĩa — chủ site tích "Dán bài này lên trang
chủ" để chọn bài cho khối nổi bật ở **trang chủ**, không phải để ghim vào danh
sách bài. `dql_archive_bo_uu_tien_ghim()` đặt `ignore_sticky_posts` cho truy vấn
chính của `is_home`.

Khối nổi bật ở trang chủ không bị ảnh hưởng: nó chạy truy vấn riêng qua
`dql_featured_post_ids()`, đã kiểm lại sau khi sửa.

Trang thẻ và trang lưu trữ theo ngày **vẫn dùng khung Kadence** — gần như không
có lưu lượng, và mỗi trang thẻ hiện chỉ có một bài.

## Ảnh đại diện bài viết: vì sao không kéo full màn hình

Kadence mặc định đặt ảnh ở chế độ `behind` — ảnh rộng hơn khung bài, khung bài
bị margin âm kéo lên trùm lên mép dưới ảnh. Kiểu đó xung đột với cột mục lục ở
lề trái: phần ảnh thừa hai bên tràn vào làn mục lục và đè lên nó, đo được 95px
ngang và 73px dọc. Đã chuyển sang `above`.

Có cân nhắc kéo ảnh full chiều ngang màn hình, và **đã loại vì dữ liệu thật**.
Đo 472 ảnh gốc trong thư viện:

| Chiều rộng ảnh gốc | Số lượng | Tỉ lệ |
|---|---|---|
| Từ 1920px | 20 | 4% |
| 1440–1919px | 15 | 3% |
| 1200–1439px | 21 | 4% |
| 1024–1199px | 32 | 7% |
| 800–1023px | 121 | 26% |
| Dưới 800px | 263 | 56% |

82% ảnh dưới 1200px. Kéo full màn hình sẽ phóng to phần lớn ảnh lên 2–3 lần và
làm chúng nhòe. Ảnh hiện hiển thị ở 712px — khớp khổ đọc, và đa số ảnh không còn
bị phóng quá cỡ gốc như khi ở 972px.

Nếu về sau thay được ảnh độ phân giải cao, đổi `post_feature_position` về
`behind` và đặt `post_feature_width` thành `full` trong `inc/brand.php`.

## Logo: bản xanh, thay ngày 04/08/2026

Bản đầu dùng viên đá đỏ `#E00020` và hồng cánh sen `#800060`, cố ý lệch khỏi
bảng màu Sapphire sáng để làm điểm nhấn. **Chủ site đã bỏ hướng đó** và vẽ lại
logo hoàn toàn trong dải xanh, khớp với bảng màu giao diện. Ghi chú cũ về việc
"giữ nguyên màu đỏ, đừng sửa" không còn hiệu lực.

Ba tệp đang dùng, đều cắt ra từ một file gốc 2000×2000 nền trong suốt:

| Tệp | Dùng ở đâu | Đặt tại |
|---|---|---|
| `logo-header-xanh.png` (660×127) | Header | Customizer → Site Identity → Logo |
| `favicon-xanh.png` (512×512) | Tab trình duyệt, biểu tượng ứng dụng | Customizer → Site Identity → Site Icon |
| `logo-header-trang-xanh.png` (660×127) | Footer nền xanh đậm | Customizer → mục Footer, "Logo footer" |

Hai điều cần biết trước khi đổi lại logo:

1. **Tỉ lệ đổi từ 3,4:1 sang 5,2:1.** Bản cũ có thêm dòng tagline nên cao hơn.
   `logo_width` vẫn để 165px như cũ (giá trị này đã đo, xem `inc/brand.php`),
   nên logo giờ cao khoảng 32px thay vì 48px. Phần chữ trong logo cao ~17px —
   vẫn đọc tốt, nhưng nếu muốn to hơn thì phải tăng `logo_width` và **đo lại
   menu ở màn 1045px**, vì đó là chỗ menu từng tràn xuống ba dòng.
2. **Bản footer là bản đảo màu.** Chữ chuyển trắng đặc, viên đá trộn 50% về phía
   trắng để không chìm vào nền `palette2` (`#0F2E5F`) — chỗ tối nhất của viên đá
   gốc là `#031448`, gần như trùng nền. Sau khi trộn, chỗ tương phản kém nhất
   vẫn đạt 3,79:1, trên ngưỡng 3:1 cho thành phần đồ họa.

## Bốn tính năng chuyên biệt, chia bốn giai đoạn

Chia giai đoạn vì chúng không cùng độ khó. Hai cái đầu là code thuần. Hai cái sau
là **hệ thống dữ liệu** — code chỉ là nửa việc, nửa còn lại là soạn nội dung.

| Giai đoạn | Nội dung | Ở đâu |
|---|---|---|
| 1 | Nền tảng: màu, chữ, CSS cơ sở | theme — **xong** |
| 2 | Bố cục trang chủ, trang bài, trang chuyên mục | theme |
| 3 | Mục lục tự động, thời gian đọc, block patterns | theme |
| 4 | Từ điển thuật ngữ, công cụ tra đá hợp mệnh | plugin `dql-core` |

Ô tra mệnh ở trang chủ (giai đoạn 2) ban đầu chỉ là khung giao diện. Nó sẽ được
nối vào công cụ thật ở giai đoạn 4. Làm vậy để bố cục trang chủ hoàn chỉnh ngay,
không phải chờ phần dữ liệu.

## Những gì cố ý không làm

- **Không slider tự chạy.** Ăn tốc độ, hại khả năng tiếp cận, và người đọc gần như
  không bao giờ xem tới slide thứ hai.
- **Không popup thu email.** Site chưa có gì để gửi cho người đăng ký.
- **Không bình luận Facebook.** Gửi dữ liệu người đọc sang bên thứ ba, và mất
  toàn bộ bình luận nếu Facebook đổi chính sách.
- **Không đếm lượt xem trong theme.** Vẫn đúng: việc đếm nằm ở plugin `dql-core`,
  và không chạy lúc dựng trang. Xem mục "Đếm lượt xem" bên dưới.
