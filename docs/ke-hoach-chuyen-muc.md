# Kế hoạch sắp lại chuyên mục và thẻ cho daquylucyen.net.vn

Ngày 2026-10-29. Đối chiếu với thiết kế đã chốt: canvas "Đá Quý Lục Yên — Thiết
kế theme Đá Thô", trang **Bản 1 — gốc** (artboard 01 Ngôn ngữ thiết kế, 02 Bộ
icon, 03 Trang chủ, 04 Chuyên mục "Văn hóa Lục Yên", 05 Bài viết, 06 Điện thoại,
07 Chế độ tối).

> Theme mang tên `kadence-daquylucyen-v2`, nhưng thiết kế được chốt là **bản 1
> (trước thẩm định)**. Bản 2 trên canvas **không** áp dụng: không bốn họ màu,
> không mega-menu, không ảnh cắt vát. Mọi tinh chỉnh đi theo bản 1.

Số liệu lấy từ production qua REST API (chỉ đọc). **Chưa thay đổi gì trên
production.** Đây là kế hoạch để duyệt.

Danh sách 47 bài cần viết để lấp các chuyên mục còn trống:
[danh-sach-bai-can-viet.md](danh-sach-bai-can-viet.md).

## Tóm tắt

1. **Ba chuyên mục cha, đúng như breadcrumb của bản 1**:
   - *Kiến thức đá quý* (artboard 05 có `Trang chủ › Kiến thức đá quý › Đá quý`)
   - *Phong thủy & hợp mệnh*
   - *Lục Yên* (artboard 04 có `Trang chủ › Lục Yên › Văn hóa & con người`)

   Dưới ba chuyên mục cha là 15 chuyên mục con, khớp với 16 icon của bản 1
   (Phong thủy & hợp mệnh dùng chính icon phong thủy).
2. **Mỗi bài thuộc đúng một chuyên mục.** Những gì cắt ngang nhiều chuyên mục
   (loại đá, hợp mệnh, loại trang sức, chất liệu) làm **thẻ**. Mẫu bài viết bản 1
   có dòng "Thẻ: ruby · đá thô · chợ đá Lục Yên · kiểm định".
3. **47/89 bài đổi chuyên mục.** Chuyên mục "Đá quý" hiện chứa cả bài kiểm định,
   du lịch, vật phẩm phong thủy và đá bán quý.
4. **Rủi ro SEO thấp.** URL bài là `/slug/` nên đổi chuyên mục không làm đổi
   URL bài. Toàn bộ trang chuyên mục và trang thẻ trên production đang
   `noindex`. Chỉ 6 URL chuyên mục cũ đổi chỗ, cần 301.
5. **Chuyên mục "Lục Yên" gần như trống**: 3 bài cho 9 chuyên mục con. Danh sách
   bài cần viết đi kèm kế hoạch này.
6. **Theme v2 phần lớn đã theo bản 1** (mỗi chuyên mục một màu đá, 16 icon).
   Còn ba chỗ cần sửa nhỏ, ghi ở mục 10:
   - dải chip "Khám phá" phải liệt kê chuyên mục con thay vì chuyên mục cha;
   - gán icon cho chuyên mục cha "Lục Yên";
   - đổi thứ tự bảng từ khóa icon.

## 1. Hiện trạng production

| | |
|---|---|
| Bài | 87 đã đăng + 2 nháp (`thach-anh-tinh-the` bản trùng, `bang-gia-da-quy`) |
| Chuyên mục | 6, phẳng: Đá quý (`da-quy`, 30) · Đá bán quý (`da-ban-quy`, 28) · Tư vấn Đá quý (`tu-van`, 14) · Trang sức đá quý (`trang-suc`, 11) · Tư vấn phong thuỷ (`tu-van-phong-thuy`, 2) · Tranh đá quý (`tranh-da-quy`, 2) |
| Thẻ | 29 thẻ, **14 thẻ 0 bài, 15 thẻ 1 bài**. Phần lớn là biến thể từ khóa SEO (`quà tặng kỷ niệm ngày cưới cho bố mẹ`, `khuyên tai nam vàng trắng`, `Vị trí bấm lỗ tai của BLACKPINK`…) |
| URL | Bài `/slug/`. Chuyên mục `/slug/` (Rank Math bỏ `category/`). Thẻ `/tag/slug/` |
| Index | Rank Math: chuyên mục và thẻ **noindex** toàn site. Sitemap chỉ có bài và trang |
| Chuyển hướng | Module Redirections đang bật, 77 quy tắc. Sáu quy tắc đang trỏ vào URL chuyên mục (`san-pham/tranh-da-quy.html` 510 lượt, `san-pham/nhan-mat-da.html` 236 lượt…) |

## 2. Bản 1 yêu cầu gì

| Chỗ | Bản 1 vẽ | Cần gì từ chuyên mục |
|---|---|---|
| Menu chính (03, 04, 05, 07) | Trang chủ · Đá quý · Phong thủy · Kiểm định · Lục Yên · Từ điển | Một chuyên mục cho mỗi mục menu, trừ "Từ điển" (trang lưu trữ của post type) |
| Dải "Khám phá" dưới header, **mọi trang** | Chip có huy hiệu: Đá quý · Phong thủy · Kiểm định · Trang sức · Văn hóa · Du lịch · Ẩm thực · Chợ đá · Lịch sử | Chuyên mục **con** (cấp lá), mỗi cái một màu đá riêng |
| Trang chủ "Lục Yên qua từng góc nhìn" | 3–6 trụ: Kiến thức đá quý (53) · Phong thủy & hợp mệnh (16) · Văn hóa & con người (12) | Chọn trụ trong Customizer. Mỗi trụ cần đủ bài để không trống |
| Trang chủ "Chuyên mục tiêu điểm" | Văn hóa Lục Yên: Hát Then, chợ đá Yên Thế, Khai Trung, cam sành | Một chuyên mục Lục Yên có ≥ 4 bài có ảnh |
| Trang chuyên mục (04) | Breadcrumb `Trang chủ › Lục Yên › Văn hóa & con người`, mô tả, hộp "Chưa biết bắt đầu từ đâu?" với 3 bài nền tảng, chip anh em: Du lịch · Ẩm thực · Làng nghề · Chợ đá quý · Nông sản | Cấu trúc cha/con, có mô tả, ≥ 3 bài để chọn bài nền tảng |
| Bài viết (05) | Breadcrumb `Trang chủ › Kiến thức đá quý › Đá quý`, nhãn chuyên mục, dòng "Thẻ:", "Đọc tiếp trong Đá quý" | Đúng 1 chuyên mục mỗi bài, thẻ cuối bài |
| Bộ icon (02) | 16 chuyên mục, **mỗi chuyên mục một màu đá** | Slug chuyên mục khớp bảng icon của theme |
| Footer | Khám phá: Giới thiệu · Từ điển đá quý · Tra đá hợp mệnh · Liên hệ | Không liên quan chuyên mục |

## 3. Nguyên tắc: khi nào là chuyên mục, khi nào là thẻ

| | Chuyên mục | Thẻ |
|---|---|---|
| Trả lời câu hỏi | Bài này thuộc **khu nào** của site? | Bài này **nói về cái gì** cụ thể? |
| Số lượng mỗi bài | Đúng **1** | 0–4 |
| Số lượng toàn site | Ít, ổn định, khớp menu và bộ icon | Nhiều, mọc dần theo nội dung |
| Có màu, icon, breadcrumb, menu | Có | Không |
| Ví dụ | Đá bán quý, Phong thủy & hợp mệnh, Chợ đá quý | ruby, thạch anh, hợp mệnh, nhẫn |

Ba quy tắc phụ:

- **Thẻ không trùng tên chuyên mục.** Mẫu bài bản 1 có thẻ `kiểm định` và `chợ
  đá Lục Yên`, nhưng đó là chữ minh họa. Trên site thật, hai thẻ này trùng với
  chuyên mục Kiểm định và Chợ đá quý, sinh ra hai trang lưu trữ gần như giống nhau.
- **Chỉ tạo thẻ khi có ≥ 2 bài**, hoặc chắc chắn sẽ có. Ngoại lệ: `spinel`, đá
  đặc trưng Lục Yên.
- **Không làm thẻ theo biến thể từ khóa SEO.** Biến thể từ khóa đặt trong focus
  keyword của Rank Math.

Áp vào nội dung hiện có:

| Ứng viên | Làm | Lý do |
|---|---|---|
| Từng loại đá (ruby, sapphire, spinel, thạch anh…) | **Thẻ** | Ruby có mặt ở Đá quý, Kiểm định, Tư vấn chọn mua, Chợ đá. Làm chuyên mục thì phải có hàng chục chuyên mục con, mỗi mục một bài. |
| Ngũ hành mệnh (Kim, Mộc, Thủy, Hỏa, Thổ) | **Thẻ** `hợp mệnh` | Mỗi mệnh hiện có 1 bài. Có thể tách thẻ theo từng mệnh ở giai đoạn 2. |
| Tỳ hưu, thiềm thừ, Phật Di Lặc, quả cầu, vòng chỉ ngũ sắc | Chuyên mục Phong thủy & hợp mệnh + **thẻ** `vật phẩm phong thủy` | Đây là vật phẩm, không phải đá. Hiện đang nằm lẫn trong Đá quý và Trang sức. |
| Nhẫn, vòng tay, khuyên tai, vàng/bạc/bạch kim | **Thẻ** | Cắt ngang Trang sức, Tư vấn chọn mua và Phong thủy. |
| Địa danh trong Lục Yên (Yên Thế, Thác Bà, Khai Trung, Tân Lĩnh…) | **Thẻ**, chỉ tạo khi có ≥ 2 bài | Không tạo thẻ "Lục Yên" vì trùng tên chuyên mục cha Lục Yên. |
| Ẩm thực · Nông sản & sản vật | Giữ **hai chuyên mục** như bản 1 | Bản 1 vẽ hai icon riêng, trang chuyên mục có chip "Nông sản 3". |
| Tin tức & sự kiện | Chuyên mục con của Lục Yên | Icon bản 1 mô tả là "thông tin mới của huyện". Chỉ viết khi có sự kiện thật. |
| Từ điển đá quý | Giữ **post type** `dql_thuat_ngu` | Mục menu riêng, không phải bài viết. |

## 4. Cấu trúc đề xuất

URL theo cách Rank Math bỏ `category/`, nên chuyên mục con mang đường dẫn cha:
`/<cha>/<con>/`. **Cần kiểm chứng trên Local trước** (bước 2, mục 9). Số bài là
số sau khi xếp lại, chỉ đếm bài đã đăng. Màu lấy từ bản 1 (artboard 02), đã có
sẵn trong `inc/icons.php`.

| Chuyên mục cha | Chuyên mục con | Slug | ID production | Màu đá | Bài |
|---|---|---|---|---|---|
| **Kiến thức đá quý** `kien-thuc-da-quy` (mới) | Đá quý | `da-quy` | 2 (giữ) | ruby | 7 |
| | Đá bán quý | `da-ban-quy` | 10 (giữ) | garnet | 45 |
| | Kiểm định | `kiem-dinh` | mới | tourmaline | 6 |
| | Trang sức | `trang-suc` | 4 (giữ, đổi tên từ "Trang sức đá quý") | hổ phách | 9 |
| | Tranh đá quý | `tranh-da-quy` | 3 (giữ) | thạch anh tím | 2 |
| | Tư vấn chọn mua | `tu-van` | 6 (giữ, đổi tên từ "Tư vấn Đá quý") | spinel | 3 (+1 nháp) |
| **Phong thủy & hợp mệnh** | *(chưa chia con)* | `phong-thuy` | 41 (đổi tên, đổi slug từ `tu-van-phong-thuy`) | sapphire | 12 |
| **Lục Yên** `luc-yen` (mới) | Văn hóa & con người | `van-hoa` | mới | tourmaline | 0 |
| | Du lịch | `du-lich` | mới | spinel | 1 |
| | Lịch sử & di tích | `lich-su` | mới | garnet | 0 |
| | Địa chất & khoáng sản | `dia-chat` | mới | thạch anh khói | 1 |
| | Chợ đá quý | `cho-da-quy` | mới | topaz | 1 |
| | Làng nghề & khai thác | `lang-nghe` | mới | đá hoa | 0 |
| | Ẩm thực | `am-thuc` | mới | hổ phách | 0 |
| | Nông sản & sản vật | `san-vat` | mới | ngọc bích | 0 |
| | Tin tức & sự kiện | `tin-tuc` | tạo khi có tin thật | than đá | 0 |

Tổng: **Kiến thức đá quý 72 · Phong thủy & hợp mệnh 12 · Lục Yên 3**.

Ghi chú:

- Giữ ID cũ cho 6 chuyên mục đang có (đổi tên, đổi slug, gắn cha). Nhờ vậy menu,
  Customizer và dữ liệu Rank Math của từng chuyên mục không mất.
- **Phong thủy & hợp mệnh** là một chuyên mục, không có con. Bản 1 chỉ vẽ một
  icon phong thủy, và 12 bài hiện có chưa đủ để tách. Mục "Phong thủy" trên
  menu trỏ thẳng vào đây.
- **Tư vấn chọn mua** đặt dưới Kiến thức đá quý, vì mua đá là kiến thức về đá.
- **Tạo sẵn 8 chuyên mục con của Lục Yên** dù đang trống, vì danh sách bài cần
  viết dựa vào chúng. Theme cần ẩn chuyên mục 0 bài khỏi dải chip (mục 10). Rank
  Math đã bật `noindex_empty_taxonomies`. Riêng **Tin tức** để tới khi có tin
  thật mới tạo.

**Quy tắc Đá quý / Đá bán quý** (để người viết sau tự xếp được): *Đá quý* = kim
cương, nhóm corundum (ruby, sapphire), emerald, spinel. Còn lại là *Đá bán
quý*, kể cả đá hữu cơ (hổ phách) và hóa thạch (gỗ hóa thạch). Áp quy tắc này thì
17 bài đang ở "Đá quý" chuyển sang "Đá bán quý". Phương án khác ở mục 11,
quyết định 2.

**Mô tả chuyên mục** là bắt buộc. Theme in mô tả làm câu dẫn ở đầu trang
chuyên mục (artboard 04), và mô tả quyết định trang có đáng index hay không.
Viết 1–2 câu cho mỗi mục, theo giọng bản 1 ("Chuyện nghề, chuyện bản, chuyện
một vùng đất sống bằng đá và bằng lúa").

## 5. Sơ đồ cây

### 5.1 Menu chính (header)

Sáu mục, đúng thứ tự bản 1. Mục có `▾` mở menu con khi rê chuột. Bản 1 không vẽ
menu con, nên phần menu con là **đề xuất thêm**, để đi tới chuyên mục con mà
không phải qua dải chip. Bỏ menu con thì header vẫn đúng bản 1.

```
Header
├── Trang chủ ............................. /
├── Đá quý ▾ .............................. /kien-thuc-da-quy/
│   ├── Đá quý ............................ /kien-thuc-da-quy/da-quy/
│   ├── Đá bán quý ........................ /kien-thuc-da-quy/da-ban-quy/
│   ├── Trang sức ......................... /kien-thuc-da-quy/trang-suc/
│   ├── Tranh đá quý ...................... /kien-thuc-da-quy/tranh-da-quy/
│   └── Tư vấn chọn mua ................... /kien-thuc-da-quy/tu-van/
├── Phong thủy ............................ /phong-thuy/
├── Kiểm định ............................. /kien-thuc-da-quy/kiem-dinh/
├── Lục Yên ▾ ............................. /luc-yen/
│   ├── Văn hóa & con người ............... /luc-yen/van-hoa/
│   ├── Du lịch ........................... /luc-yen/du-lich/
│   ├── Lịch sử & di tích ................. /luc-yen/lich-su/
│   ├── Địa chất & khoáng sản ............. /luc-yen/dia-chat/
│   ├── Chợ đá quý ........................ /luc-yen/cho-da-quy/
│   ├── Làng nghề & khai thác ............. /luc-yen/lang-nghe/
│   ├── Ẩm thực ........................... /luc-yen/am-thuc/
│   └── Nông sản & sản vật ................ /luc-yen/san-vat/
└── Từ điển ............................... /tu-dien/   (post type dql_thuat_ngu)

Nút bên phải: Tìm kiếm · Đổi sáng/tối
```

"Kiểm định" đứng riêng ở menu chính (như bản 1) dù nằm dưới Kiến thức đá quý,
nên không lặp lại trong menu con của "Đá quý".

### 5.2 Dải "Khám phá" (dưới header, mọi trang)

Thứ tự theo bản 1, chỉ hiện chuyên mục có bài, cuộn ngang trên điện thoại:

```
Khám phá  [Đá quý] [Phong thủy] [Kiểm định] [Trang sức] [Văn hóa] [Du lịch] [Ẩm thực] [Chợ đá] [Lịch sử] …
```

### 5.3 Cây chuyên mục và thẻ

```
Chuyên mục (mỗi bài đúng 1)                    bài hiện có → sau khi viết thêm
├── Kiến thức đá quý                                72 → 82
│   ├── Đá quý                                       7 → 11
│   ├── Đá bán quý                                  45 → 45
│   ├── Kiểm định                                    6 → 9
│   ├── Trang sức                                    9 → 9
│   ├── Tranh đá quý                                 2 → 4
│   └── Tư vấn chọn mua                              3 → 4   (+1 nháp)
├── Phong thủy & hợp mệnh                           12 → 12
└── Lục Yên                                          3 → 40
    ├── Văn hóa & con người                          0 → 6
    ├── Du lịch                                      1 → 6
    ├── Lịch sử & di tích                            0 → 4
    ├── Địa chất & khoáng sản                        1 → 5
    ├── Chợ đá quý                                   1 → 5
    ├── Làng nghề & khai thác                        0 → 5
    ├── Ẩm thực                                      0 → 5
    ├── Nông sản & sản vật                           0 → 4
    └── Tin tức & sự kiện                            0 → 0   (chỉ khi có tin thật)

Thẻ (0–4 mỗi bài, cắt ngang chuyên mục)
├── Loại đá ........ ruby · sapphire · spinel · thạch anh
├── Phong thủy ..... hợp mệnh · vật phẩm phong thủy
├── Trang sức ...... nhẫn · vòng tay · khuyên tai · kim loại quý
└── Mua bán ........ giá đá quý
```

Nhóm thẻ ở trên chỉ để đọc cho dễ, WordPress không có thẻ cha.

### 5.4 Trụ trang chủ "Lục Yên qua từng góc nhìn"

Theo bản 1, ba trụ: **Kiến thức đá quý** · **Phong thủy & hợp mệnh** · **Văn hóa
& con người**. Có thể thêm tới 6 trụ (Du lịch, Chợ đá quý, Ẩm thực) khi các mục
đó có bài. Khối "Chuyên mục tiêu điểm" ở trang chủ chọn chuyên mục cha **Lục Yên**.

## 6. Thẻ

**Bộ thẻ khi chạy** (11 thẻ). Số bài là số bài đã đăng được gắn thẻ:

| Thẻ | Slug | Bài | Ghi chú |
|---|---|---|---|
| hợp mệnh | `hop-menh` | 8 | 5 bài mệnh + chọn đá theo mệnh/tuổi + vòng tay |
| thạch anh | `thach-anh` | 6 | Gồm citrine, mã não, canxedon (đều thuộc họ thạch anh). Giữ ID 38 |
| vật phẩm phong thủy | `vat-pham-phong-thuy` | 5 | |
| ruby | `ruby` | 3 | Giữ ID 34 |
| nhẫn | `nhan` | 3 | Giữ ID 37 (đang 0 bài) |
| kim loại quý | `kim-loai-quy` | 3 | Vàng, bạc, bạch kim. Thay thẻ `vàng`, `bạc` |
| sapphire | `sapphire` | 2 | Giữ ID 39 (đang 0 bài) |
| vòng tay | `vong-tay` | 2 | |
| khuyên tai | `khuyen-tai` | 2 | |
| giá đá quý | `gia-da-quy` | 1 (+1 nháp) | |
| spinel | `spinel` | 1 | Giữ ID 28. Đá đặc trưng Lục Yên |

**Xoá 24 thẻ còn lại**: `bạc`, `vàng`, `charm`, `trang sức`, `citrine`, `opal`,
`topaz`, `saphia`, `kiểm định đá quý`, và các thẻ biến thể từ khóa (bấm lỗ tai,
khuyên tai nam…, quà kỷ niệm ngày cưới…, cách soi đá quý…). Trang thẻ đều đang
`noindex` và có 0–1 bài, nên không cần chuyển hướng. Module 404 của Rank Math sẽ
báo nếu có link ngoài trỏ vào.

**Giai đoạn 2** (khi các bài trong danh sách cần viết được đăng):
- thẻ theo từng mệnh (`mệnh Hỏa`…), gắn cả vào bài đá hợp mệnh đó;
- thẻ địa danh (`Yên Thế`, `Thác Bà`, `Tân Lĩnh`, `Khai Trung`);
- thẻ dân tộc (`người Tày`, `người Dao`).

Danh sách bài cần viết đã ghi sẵn thẻ cho từng bài.

Trang thẻ giữ `noindex` cho tới khi thẻ có ≥ 5 bài và có mô tả.

## 7. Bảng xếp từng bài hiện có

"Từ" = chuyên mục cũ, `giữ` = không đổi. Bài nháp đánh dấu *(nháp)*.

#### Kiến thức đá quý › Đá quý — `da-quy` (7 bài)

| ID | Bài | Từ | Thẻ |
|---|---|---|---|
| 934 | Corundum là gì? Bạn đã biết gì về Corundum? | giữ | ruby, sapphire |
| 736 | Kim cương là gì? Bạn biết gì về kim cương? | giữ |  |
| 642 | Đá Emerald là gì? Người nào có thể sử dụng Emerald? | giữ |  |
| 357 | Danh sách các loại đá quý thiên nhiên được chuộng nhất tại Việt Nam | `tu-van` |  |
| 304 | Sapphire – VIÊN ĐÁ QUÝ ĐẾN TỪ THIÊN ĐƯỜNG | giữ | sapphire |
| 167 | Đá Spinel là gì? Những kiến thức phải biết về đá Spinel [Update 2023] | giữ | spinel |
| 106 | 6 Kiến thức chuyên sâu về Đá Ruby mà có thể bạn chưa biết | giữ | ruby |

#### Kiến thức đá quý › Đá bán quý — `da-ban-quy` (45 bài)

| ID | Bài | Từ | Thẻ |
|---|---|---|---|
| 1097 | Đá Canxit là gì? Hãy cùng vén bức màn bí mật [2026 Updated] | giữ |  |
| 1067 | Thạch anh tóc – Lá bùa hộ mệnh tài lộc và tình duyên | `da-quy` | thạch anh |
| 1058 | Thạch anh tinh thể là gì? Phân loại thạch anh tinh thể | `da-quy` | thạch anh |
| 1014 | Đá Diopside – Khám phá vẻ đẹp thanh khiết, hữu xạ tự nhiên hương | `da-quy` |  |
| 1006 | Đá Canxedon – Khám phá phép màu của viên đá niềm vui | giữ | thạch anh |
| 988 | Thạch anh tinh thể là gì? Phân loại thạch anh tinh thể *(nháp)* | `da-quy` | thạch anh |
| 935 | Cordierit là gì? Bạn biết gì về Cordierit? | `da-quy` |  |
| 917 | Gỗ hóa thạch là gì? Bạn đã biết gì về hỗ hóa thạch? | giữ |  |
| 916 | Đá Prehnite là gì? Bạn đã biết gì về đá Prehnite? | giữ |  |
| 897 | Đá Hematite là gì? Bạn đã biết gì về đá Hematite? | giữ |  |
| 896 | Đá đào hoa Rhodochrosite là gì? Bạn biết gì về đá đào hoa Rhodochrosite? | giữ |  |
| 876 | Đá Malachite là gì? Bạn đã biết gì về đá Malachite? | giữ |  |
| 872 | Đá lepidolite là gì? Bạn đã biết gì về đá lepidolite? | giữ |  |
| 871 | Đá Bronzite là gì? Bạn đã biết gì về đá Bronzite? | giữ |  |
| 836 | Đá phiến Steatite là gì? Bạn đã biết gì về đá Steatite? | giữ |  |
| 835 | Đá Agalmatolite là gì? Bạn đã biết gì về đá Agalmatolite? | giữ |  |
| 834 | Đá Serpentine là gì? Bạn đã biết gì về Serpentine? | giữ |  |
| 833 | Ngọc Bích nephrite Jade là gì? 5 sai lầm cần tránh khi đeo | `da-quy` |  |
| 808 | Đá Onyx là gì? Những điều bạn cần biết về đá Onyx | giữ |  |
| 807 | Đá Hổ Phách Amber là gì? Bạn biết gì về đá Hổ Phách Amber? | giữ |  |
| 806 | Đá Diopside là gì? Bạn đã biết gì về đá Diopside? | giữ |  |
| 766 | Đá Lapis Lazuli là gì? Bạn đã biết gì về đá Lapis Lazuli? | giữ |  |
| 738 | Đá Chiastolite là gì? Bạn đã biết gì về đá Chiastolite? | `da-quy` |  |
| 737 | Đá Idocrase là gì? Bạn biết gì về đá Idocrase? | `da-quy` |  |
| 727 | Đá Peridot là gì? Bạn biết gì về đá Peridot? | `da-quy` |  |
| 710 | Đá Staurolite là gì? Bạn đã biết gì về đá Staurolite? | `da-quy` |  |
| 709 | Đá Axinite là gì? Bạn biết gì về đá Axinite? | `da-quy` |  |
| 694 | Đá Epidote là gì? Bạn biết gì về đá Epidote? | `da-quy` |  |
| 693 | Đá Andalusite là gì? Bạn đã biết gì về đá Andalusite? | `da-quy` |  |
| 678 | Đá Kyanite là gì? Bạn đã biết gì về Kyanite? | giữ |  |
| 677 | Đá Obsidian là gì? Bạn đã biết gì về Obsidian? | giữ |  |
| 645 | Đá Labradorite là gì? Bạn đã biết gì về  Labradorite? | `da-quy` |  |
| 644 | Đá Turquoise là gì? Bạn đã biết gì về Turquoise? | `da-quy` |  |
| 643 | Đá Zircon là gì? Những bí mật ít biết về đá Zircon | `da-quy` |  |
| 363 | Đá Tourmaline – lá bùa hộ mệnh đa sắc của nhân loại | giữ |  |
| 373 | Đá mã não là gì? Nguồn gốc và thành tạo đá mã não | giữ | thạch anh |
| 379 | Ý nghĩa và công dụng của đá Granite có thể bạn chưa biết! | giữ |  |
| 377 | Điều ít ai biết về đá mặt trăng và ý nghĩa phong thủy của nó | giữ |  |
| 334 | Đá Garnet và những thông tin cần biết trước khi sử dụng | `da-quy` |  |
| 325 | Đá AQUAMARINE – Kho báu của nàng tiên cá | `da-quy` |  |
| 276 | Những điều phải biết về đá thạch anh trong phong thủy | giữ | thạch anh |
| 258 | Khám phá đá mắt mèo với những phát hiện thú vị | giữ |  |
| 239 | Tìm hiểu về tính ứng dụng của đá mắt hổ trong cuộc sống | giữ |  |
| 186 | Tổng hợp kiến thức về đá Opal từ A-Z cho người chưa biết gì! | giữ |  |
| 153 | Những điều thú vị về đá Topaz và ý nghĩa của đá Topaz | giữ |  |
| 141 | Những điều bạn chưa biết về đá Citrine – Đá trang sức siêu HOT | giữ | thạch anh |

#### Kiến thức đá quý › Kiểm định — `kiem-dinh` (6 bài)

| ID | Bài | Từ | Thẻ |
|---|---|---|---|
| 90204 | Địa chỉ kiểm định đá quý uy tín tại Việt Nam bạn nên biết | `da-quy` |  |
| 90148 | Đèn soi đá quý loại nào tốt? Top 5 mẫu chuyên dụng tốt nhất hiện nay | `da-quy` |  |
| 90140 | Cách phân biệt đá quý thật giả bằng mắt thường tại nhà | `da-quy` |  |
| 90047 | Quy chuẩn kiểm định đá quý Việt Nam 2026: Những thay đổi người mua cần biết | `da-quy` |  |
| 1279 | Cách Soi Đá Quý và Hướng Dẫn Sử Dụng Đèn Soi Đá Quý | `tu-van` |  |
| 953 | Carat là gì? Bạn có biết sự thật về Carat? | `tu-van` |  |

#### Kiến thức đá quý › Trang sức — `trang-suc` (9 bài)

| ID | Bài | Từ | Thẻ |
|---|---|---|---|
| 90220 | Trang sức đá quý thiết kế riêng từ Lục Yên độc bản | giữ |  |
| 1410 | Bạc 925 là gì? Sự khác biệt giữa Bạc 925 và Bạc ta | giữ | kim loại quý |
| 1364 | 5 mẫu Khuyên tai nam, bông tai nam đẹp cho phái mạnh | giữ | khuyên tai |
| 1354 | Tặng quà kỷ niệm ngày cưới cho bố mẹ và cách tổ chức buổi kỷ niệm đáng nhớ | `tu-van-phong-thuy` |  |
| 1350 | 3 Vị Trí Bấm Lỗ Tai Đẹp Cho Nữ Ý Nghĩa Và Phong Thủy | `tu-van-phong-thuy` | khuyên tai |
| 1109 | Bạch kim là gì? Khám phá nét đẹp quyền lực của “ông hoàng kim loại” | giữ | kim loại quý |
| 371 | Đá Swarovski là gì? Khám phá bí ẩn đặc biệt của đá Swarovski | `da-quy` |  |
| 375 | Bật mí ý nghĩa ngón tay đeo nhẫn khiến nhiều người ngã ngửa | `tu-van` | nhẫn |
| 174 | Charm là gì? Những điều thú vị về Charm mà bạn phải biết! | giữ | kim loại quý |

#### Kiến thức đá quý › Tranh đá quý — `tranh-da-quy` (2 bài)

| ID | Bài | Từ | Thẻ |
|---|---|---|---|
| 90169 | Tranh đá quý Lục Yên giá bao nhiêu? Bảng giá tham khảo 2026 | giữ | giá đá quý |
| 346 | Sở hữu ngay một bức tranh đá quý bởi những điều sau đây | giữ |  |

#### Kiến thức đá quý › Tư vấn chọn mua — `tu-van` (3 bài)

| ID | Bài | Từ | Thẻ |
|---|---|---|---|
| 544 | [Tổng hợp] Bảng giá đá quý thiên nhiên thường gặp trên thị trường *(nháp)* | `da-quy` | giá đá quý |
| 367 | Chọn mua vòng tay đá phong thủy thế nào cho chuẩn? | `trang-suc` | vòng tay, hợp mệnh |
| 369 | Cách chọn những kiểu nhẫn đẹp phù hợp với bàn tay nữ giới | giữ | nhẫn |
| 365 | Chọn mua nhẫn ruby nam nên chú ý điều gì? | `trang-suc` | nhẫn, ruby |

#### Phong thủy & hợp mệnh — `phong-thuy` (12 bài)

| ID | Bài | Từ | Thẻ |
|---|---|---|---|
| 90189 | Cách chọn đá phong thủy theo tuổi chuẩn xác nhất năm 2026 | `trang-suc` | hợp mệnh |
| 1088 | Quả cầu phong thủy có thực sự mang lại năng lượng cho bạn? | `trang-suc` | vật phẩm phong thủy |
| 1040 | Phật Di Lặc – Món đồ phong thủy mang lại may mắn, tài lộc và bình an | `trang-suc` | vật phẩm phong thủy |
| 1023 | Thiềm Thừ – Cóc 3 chân miệng ngậm đồng tiền, nhả tài lộc | `trang-suc` | vật phẩm phong thủy |
| 991 | Tỳ Hưu – Linh vật phong thủy chiêu tài lộc, công danh, sức khỏe | `da-quy` | vật phẩm phong thủy |
| 952 | Vòng chỉ ngũ sắc là gì? Những thông tin về vòng chỉ ngũ sắc | `tu-van` | vật phẩm phong thủy, vòng tay |
| 613 | Người mệnh Thổ hợp đá gì? Cách chọn đá cho nam nữ mệnh Thổ | `tu-van` | hợp mệnh |
| 612 | Người mệnh Hỏa hợp đá gì? Cách chọn đá cho nam nữ mệnh Hỏa | `tu-van` | hợp mệnh |
| 611 | Người mệnh Thủy hợp đá gì? Cách chọn đá cho nam nữ mệnh Thủy | `tu-van` | hợp mệnh |
| 590 | Người mệnh Mộc hợp đá gì? Những thông tin bạn nên biết khi chọn đá phong thủy | `tu-van` | hợp mệnh |
| 589 | Người mệnh Kim hợp đá gì? Những thông tin bạn nên biết khi chọn đá phong thủy | `tu-van` | hợp mệnh |
| 361 | Cách chọn đá phong thủy theo mệnh đơn giản nhất | `tu-van` | hợp mệnh |

#### Lục Yên › Du lịch — `du-lich` (1 bài)

| ID | Bài | Từ | Thẻ |
|---|---|---|---|
| 90058 | Kinh nghiệm du lịch trải nghiệm chợ đá quý Lục Yên từ A-Z | `da-quy` |  |

#### Lục Yên › Địa chất & khoáng sản — `dia-chat` (1 bài)

| ID | Bài | Từ | Thẻ |
|---|---|---|---|
| 359 | Hướng dẫn cách tìm thấy đá quý trong tự nhiên | `tu-van` |  |

#### Lục Yên › Chợ đá quý — `cho-da-quy` (1 bài)

| ID | Bài | Từ | Thẻ |
|---|---|---|---|
| 355 | Chợ đá quý Lục Yên: Nét độc đáo của vùng đất Yên Bái | `tu-van` |  |

Các bài đáng chú ý khi xếp:

- **Tỳ Hưu, Thiềm Thừ, Phật Di Lặc, quả cầu, vòng chỉ ngũ sắc** → Phong thủy &
  hợp mệnh. Đây là vật phẩm, không phải đá.
- **Swarovski** → Trang sức. Swarovski là pha lê nhân tạo; để trong Đá quý là
  ngược với tinh thần "hiểu đúng về đá quý".
- **Carat là gì** → Kiểm định (đơn vị đo trong giấy kiểm định).
- **Cách chọn đá phong thủy theo tuổi** (đang ở Trang sức) → Phong thủy & hợp mệnh.
- **Granite** → Đá bán quý, vì bài viết về granite làm phụ kiện chứ không viết
  về địa chất.
- **Tìm đá quý trong tự nhiên** → Lục Yên › Địa chất & khoáng sản.
- **Kinh nghiệm du lịch chợ đá Lục Yên** → Lục Yên › Du lịch. **Chợ đá quý Lục
  Yên** → Lục Yên › Chợ đá quý.
- **Quà kỷ niệm ngày cưới cho bố mẹ** → tạm để Trang sức. Bài lạc đề với site
  kiến thức; nên cân nhắc `noindex` hoặc gỡ (ngoài phạm vi kế hoạch này).

## 8. Chuyển hướng URL

URL bài **không đổi**. Chỉ đổi 6 URL chuyên mục cũ, cùng các quy tắc cũ đang trỏ
vào chúng. Tạo trong **Rank Math → Redirections**: REST của Rank Math chỉ đọc
được chuyển hướng, không ghi được.

**Quy tắc mới** (301, gồm cả trang phân trang):

| Nguồn (regex) | Đích |
|---|---|
| `^da-quy(/page/\d+)?/?$` | `/kien-thuc-da-quy/da-quy$1/` |
| `^da-ban-quy(/page/\d+)?/?$` | `/kien-thuc-da-quy/da-ban-quy$1/` |
| `^trang-suc(/page/\d+)?/?$` | `/kien-thuc-da-quy/trang-suc$1/` |
| `^tranh-da-quy(/page/\d+)?/?$` | `/kien-thuc-da-quy/tranh-da-quy$1/` |
| `^tu-van(/page/\d+)?/?$` | `/kien-thuc-da-quy/tu-van$1/` |
| `^tu-van-phong-thuy(/page/\d+)?/?$` | `/phong-thuy$1/` |
| `^category/(.+)$` | Thử trước trên Local. Hiện WordPress đoán `/category/da-quy/` thành **bài** `/cho-da-quy-luc-yen/`, sai chỗ |

**Sửa đích** của quy tắc cũ để không thành chuỗi hai lần chuyển:

| Nguồn | Đích hiện tại | Đích mới | Lượt |
|---|---|---|---|
| `san-pham/tranh-da-quy.html` | `/tranh-da-quy` | `/kien-thuc-da-quy/tranh-da-quy/` | 510 |
| `san-pham/nhan-mat-da.html` | `/trang-suc` | `/kien-thuc-da-quy/trang-suc/` | 236 |
| `danh-muc/da-quy`, `tag/da-quy` | `/da-quy` | `/kien-thuc-da-quy/da-quy/` | 0 |
| `danh-muc/trang-suc` | `/trang-suc` | `/kien-thuc-da-quy/trang-suc/` | 0 |
| `danh-muc/tranh-da-quy` | `/tranh-da-quy` | `/kien-thuc-da-quy/tranh-da-quy/` | 0 |
| `danh-muc/travel` | `/danh-muc/da-ban-quy` (**đích chết**) | `/luc-yen/du-lich/` | 0 |
| `khuyen-tai-nam-vang-trang` | `https://khuyen-tai-nam` (**đích hỏng, thiếu tên miền**) | `/khuyen-tai-nam/` | **120** |

Hai dòng cuối là lỗi có sẵn, phát hiện lúc rà. Riêng quy tắc
`khuyen-tai-nam-vang-trang` đang đưa 120 lượt truy cập tới một địa chỉ không
tồn tại.

**Kiểm tra va chạm slug**: chuyên mục cấp cao nhất chiếm `/<slug>/`, ngang hàng
với bài và trang. Ba slug cha (`kien-thuc-da-quy`, `phong-thuy`, `luc-yen`) không
trùng bài hay trang nào trên production (đã đối chiếu 89 bài, 6 trang).

## 9. Các bước thực hiện

| # | Việc | Ở đâu | Cách làm |
|---|---|---|---|
| 1 | Sao lưu chuyên mục, thẻ, quan hệ bài–chuyên mục–thẻ, menu, chuyển hướng ra JSON. Kèm script khôi phục | production → `deploy/` | REST, chỉ đọc |
| 2 | Chạy thử toàn bộ trên **Local**, khớp bài theo slug (Local có bản sao các bài cùng slug). Kiểm chứng URL `/<cha>/<con>/` với Rank Math, breadcrumb, dải chip, số đếm của chuyên mục cha | Local | Script PHP dùng `wp_insert_term` / `wp_set_post_terms`, chạy lại nhiều lần vẫn cho cùng kết quả |
| 3 | Viết mô tả cho 3 chuyên mục cha + 14 chuyên mục con | — | Chủ site duyệt câu chữ |
| 4 | Production: tạo 2 chuyên mục cha mới (`kien-thuc-da-quy`, `luc-yen`); đổi tên/slug/cha cho 6 chuyên mục cũ; tạo 9 chuyên mục con mới; gắn mô tả | production | REST `wp/v2/categories`. Có chế độ chạy thử, chỉ in ra thay đổi |
| 5 | Xếp lại 89 bài (mỗi bài đúng 1 chuyên mục), gắn thẻ, tạo 6 thẻ mới, xoá 24 thẻ | production | REST `wp/v2/posts`, `wp/v2/tags` |
| 6 | Thêm 6 quy tắc chuyển hướng mới, sửa 8 quy tắc cũ | production | **Làm tay** trong wp-admin → Rank Math → Redirections |
| 7 | Dựng menu theo sơ đồ 5.1 | production | wp-admin → Giao diện → Menu (hoặc REST `wp/v2/menu-items`) |
| 8 | Kiểm tra: 6 URL cũ trả 301 một bước; mọi bài có đúng 1 chuyên mục; dải chip không có chuyên mục rỗng; 404 log của Rank Math sạch sau 7 ngày | production | Script + Rank Math |
| 9 | Giao [danh sách bài cần viết](danh-sach-bai-can-viet.md) cho agent viết bài | — | Sau bước 4, khi các chuyên mục con đã tồn tại |
| 10 | Bật `index` riêng cho 3 chuyên mục cha và các chuyên mục con có ≥ 5 bài và có mô tả. Thêm chuyên mục vào sitemap | production | Rank Math → từng chuyên mục (robots riêng). Sitemap qua ability `set-sitemap-settings` |

Bước 4–5 chạy được với theme **v1 đang chạy** trên production. Theme v1 vốn
dựng trên dữ liệu thử có chuyên mục cha/con, nên dải chip con (đang trống vì
taxonomy phẳng) sẽ có nội dung trở lại. Không cần đợi theme v2 mới làm.

## 10. Theme v2 cần sửa để khớp cấu trúc này (theo bản 1)

Màu theo từng chuyên mục, 16 icon, breadcrumb, hộp "Bắt đầu từ đâu", "Đọc
nhiều nhất trong chuyên mục" đều **đã có** và đúng bản 1. Còn lại:

1. **Dải "Khám phá"** (`inc/header.php`) đang lấy chuyên mục cấp cao nhất
   (`parent => 0`). Với cấu trúc mới, dải sẽ chỉ còn 3 chip: Kiến thức đá quý ·
   Phong thủy · Lục Yên. Bản 1 vẽ chip cấp lá. Sửa thành: lấy chuyên mục không
   có con, bỏ mục 0 bài, sắp theo thứ tự chọn trong Customizer. Mặc định theo
   thứ tự bản 1: Đá quý · Phong thủy · Kiểm định · Trang sức · Văn hóa · Du lịch
   · Ẩm thực · Chợ đá · Lịch sử.
2. **Icon cho chuyên mục cha mới**:
   - `luc-yen` không khớp từ khóa nào, nên rơi về icon đá quý màu ruby. Chọn tay
     icon Văn hóa (nhà sàn) hoặc Địa chất qua term meta, hoặc thêm `luc-yen` vào
     bảng từ khóa.
   - `kien-thuc-da-quy` khớp `da-quy` → ruby, đúng.
   - `phong-thuy` → sapphire, đúng.
3. **Thứ tự bảng từ khóa icon**: đưa `trang-suc` và `tu-van` lên trước
   `da-quy`, để slug ghép như `trang-suc-da-quy` không bị bắt nhầm (lỗi đã thấy
   trên Local, xem `kiem-thu/BAO-CAO-2026-10-29.md`).
4. **Menu con** ở mục 5.1, nếu chủ site đồng ý. Header Kadence hỗ trợ sẵn; chỉ
   cần CSS cho khối thả xuống khớp vật liệu mờ của header.

## 11. Quyết định chủ site cần chốt

1. **Cha/con hay giữ phẳng?** Đề xuất: **cha/con**, vì bản 1 vẽ breadcrumb ba
   cấp ở cả trang chuyên mục lẫn trang bài. Ghi chú cũ "giữ phẳng vì đổi URL là
   mất index" không còn đúng, vì chuyên mục trên production đang `noindex`.
2. **Ranh giới Đá quý / Đá bán quý.**
   - **A (đề xuất)**: theo quy tắc ở mục 4. Kết quả Đá quý 7 · Đá bán quý 45.
   - **B**: chỉ đưa bài *không phải đá* ra khỏi Đá quý, giữ cách chia hiện tại
     cho đá. Kết quả Đá quý 24 · Đá bán quý 28. Ít bài phải đổi hơn, nhưng không
     có quy tắc nào để người viết sau làm theo.

   Danh sách bài cần viết bổ sung 4 bài cho Đá quý, nên với phương án A mục này
   lên 11 bài.
3. **Menu con** dưới "Đá quý" và "Lục Yên" (sơ đồ 5.1): thêm hay giữ menu phẳng
   đúng như bản 1?
4. **Chuyên mục con của Phong thủy & hợp mệnh**: tạm chưa chia. Khi số bài vượt
   khoảng 25, có thể tách "Hợp mệnh" và "Vật phẩm phong thủy".

## 12. Phát hiện phụ trong lúc rà

- **Hai cặp bài trùng nội dung**:
  - `da-diopside` (1014) và `da-diopside-la-gi` (806);
  - `thach-anh-tinh-the-2` (1058) và bản nháp `thach-anh-tinh-the` (988).

  Nên gộp và đặt 301, làm riêng sau.
- **Hai quy tắc chuyển hướng hỏng** (`khuyen-tai-nam-vang-trang`, `danh-muc/travel`),
  đã ghi ở mục 8.
- WordPress đoán `/category/<slug>/` sang một bài ngẫu nhiên thay vì trang
  chuyên mục.
- Bài `trang-suc-da-quy-thiet-ke-rieng` có câu "tìm đơn vị chế tác uy tín", dễ
  đọc thành quảng cáo. Nên rà lại theo hướng "trang kiến thức thuần".
