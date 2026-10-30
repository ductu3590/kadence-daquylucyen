LOGO ĐÁ QUÝ LỤC YÊN — BẢN 2.0, PHƯƠNG ÁN B "RUBY TRONG ĐÁ HOA TRẮNG"

Huy hiệu sáu cạnh lệch đúng bản thiết kế đã duyệt: gradient ruby #C0233F → #6E0B20
có hạt, icon viên đá nét trắng; đáy huy hiệu là đá hoa trắng Lục Yên (marble) có
vân, viên đá mọc liền từ đá. Tên site một dòng, Fraunces 600, "Lục Yên" ruby.
Chữ đã chuyển thành đường nét, không cần cài font. Tỉ lệ 5,2 : 1, bằng logo xanh
cũ nên giữ nguyên Logo Width đang dùng (165/150/135) là vừa.

FILE PNG (nền trong suốt)                     Dùng ở đâu
logo-header.png        1200x231   Customizer > Site Identity > Logo (header, nền sáng)
logo-header-trang.png  1200x231   Customizer > Giao diện / Footer > "Logo footer" (nền tối)
favicon.png            512x512    Customizer > Site Identity > Site Icon
favicon-192.png        192x192    Dự phòng cho plugin PWA/SEO nếu cần
logo-mark.png          1024x1024  Avatar Facebook/Zalo, ảnh chia sẻ (đặt lên nền tự chọn)

FILE SVG (bản gốc, phóng to không vỡ): logo-header.svg, logo-header-trang.svg,
logo-mark.svg, favicon.svg. Dùng cho in ấn, biển hiệu, hoặc sửa sau.

CẬP NHẬT TRÊN SITE (local rồi production, cùng các bước)
1. Media > Add New: tải logo-header.png, logo-header-trang.png, favicon.png.
2. Customizer > Site Identity: Logo = logo-header.png; Site Icon = favicon.png.
3. Customizer > mục Footer của theme: "Logo footer" = logo-header-trang.png.
4. Lưu. Nếu dùng plugin cache hoặc CDN: xóa cache. Site icon thường cần xóa
   cache trình duyệt mới thấy đổi.
5. Logo xanh cũ còn trong Thư viện; xóa khi chắc không trang nào dùng.

LƯU Ý: PNG header xuất ở 1200px (gấp đôi cỡ cũ 660) để màn hình Retina nét;
WordPress tự tạo bản nhỏ. Không cần chỉnh Logo Width.
