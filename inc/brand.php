<?php
/**
 * Bộ nhận diện thương hiệu: màu sắc, chữ, chiều rộng nội dung.
 *
 * Đây là nơi duy nhất khai báo giá trị mặc định của thương hiệu.
 *
 * Cách Kadence hoạt động — cần hiểu trước khi sửa file này:
 *
 *   1. Kadence đọc màu từ Customizer rồi bơm ra biến CSS --global-palette1..15.
 *   2. theme.json của Kadence chỉ trỏ tới các biến đó, không chứa mã màu.
 *   3. Các filter bên dưới đặt giá trị MẶC ĐỊNH, dùng khi Customizer chưa được lưu.
 *
 * Hệ quả thực tế: sửa màu trong Customizer sẽ ghi đè file này, và đó là hành vi
 * mong muốn. File này quyết định site trông thế nào lúc mới cài, hoặc sau khi
 * bấm "Khôi phục mặc định" trong Customizer.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bảng màu thương hiệu — phương án "Sapphire sáng".
 *
 * Ý đồ: sapphire chỉ dùng làm điểm nhấn trên nền trắng, không phủ xanh khắp trang.
 * Đây là cách dung hòa giữa "sang trọng" của màu sapphire và "tươi sáng thân thiện"
 * mà một blog kiến thức cần để người đọc theo hết bài dài.
 *
 * Tên khe màu là do Kadence quy định, không đổi được:
 *
 *   palette1  Màu nhấn — link, nút bấm
 *   palette2  Màu nhấn phụ — trạng thái di chuột, nền đậm
 *   palette3  Chữ đậm nhất — tiêu đề
 *   palette4  Chữ đậm — nội dung bài
 *   palette5  Chữ vừa — chú thích
 *   palette6  Chữ nhạt — ngày tháng, thông tin phụ
 *   palette7  Nền nhạt — hộp trích dẫn, thẻ bài
 *   palette8  Nền sáng hơn — dải phân vùng
 *   palette9  Trắng
 *   palette10 Màu bổ túc — để #FfFfFf thì Kadence tự tính từ palette1
 *   palette11..15  Màu thông báo: thành công, thông tin, lỗi, cảnh báo, đánh giá
 *
 * @return string Chuỗi JSON đúng định dạng Kadence yêu cầu.
 */
function dql_brand_palette() {
	$colors = array(
		'palette1'  => '#2563AC', // Sapphire — link và nút.
		'palette2'  => '#0F2E5F', // Sapphire đậm — di chuột, nền đậm.
		'palette3'  => '#16203A', // Tiêu đề.
		'palette4'  => '#3D4759', // Nội dung bài.
		'palette5'  => '#5A6478', // Chữ vừa.
		'palette6'  => '#7E93AE', // Bạc — chữ nhạt.
		'palette7'  => '#EEF2F8', // Nền sương.
		'palette8'  => '#F7F9FC', // Nền sáng hơn.
		'palette9'  => '#FFFFFF',
		'palette10' => '#FfFfFf', // Giữ nguyên: đây là mã đặc biệt báo Kadence tự tính.
		'palette11' => '#137A4C', // Thành công.
		'palette12' => '#1159AF', // Thông tin.
		'palette13' => '#B82105', // Lỗi.
		'palette14' => '#C9770B', // Cảnh báo — chỉnh trầm lại cho hợp tông xanh.
		'palette15' => '#D99A16', // Đánh giá sao.
	);

	// Giữ nguyên cách đặt tên của Kadence để nhãn trong Customizer không đổi.
	$names = array(
		'palette10' => 'Palette Color Complement',
		'palette11' => 'Palette Color Success',
		'palette12' => 'Palette Color Info',
		'palette13' => 'Palette Color Alert',
		'palette14' => 'Palette Color Warning',
		'palette15' => 'Palette Color Rating',
	);

	$set = array();
	$i   = 0;
	foreach ( $colors as $slug => $color ) {
		$i++;
		$set[] = array(
			'color' => $color,
			'slug'  => $slug,
			'name'  => isset( $names[ $slug ] ) ? $names[ $slug ] : 'Palette Color ' . $i,
		);
	}

	// Kadence lưu ba bộ màu để người dùng đổi qua lại. Ta đặt cả ba giống nhau.
	return wp_json_encode(
		array(
			'palette'        => $set,
			'second-palette' => $set,
			'third-palette'  => $set,
			'active'         => 'palette',
		)
	);
}
add_filter( 'kadence_global_palette_defaults', 'dql_brand_palette' );

/**
 * Giá trị mặc định cho chữ và bố cục.
 *
 * @param array $defaults Mặc định gốc của Kadence.
 * @return array
 */
function dql_brand_typography( $defaults ) {

	// --- Chữ nội dung -----------------------------------------------------
	// Inter ở cỡ 17px. Tiếng Việt nhiều dấu nên cần line-height cao hơn
	// tiếng Anh, 1.75 thay vì 1.6 mặc định của Kadence, để dấu không chạm
	// dòng trên.
	$defaults['base_font'] = array(
		'size'       => array(
			'desktop' => 17,
			'tablet'  => 17,
			'mobile'  => 16,
		),
		'lineHeight' => array(
			'desktop' => 1.75,
			'tablet'  => 1.75,
			'mobile'  => 1.7,
		),
		'family'     => 'Inter',
		'google'     => true,
		'weight'     => '400',
		'variant'    => 'regular',
		'color'      => 'palette4',
	);

	// --- Chữ tiêu đề ------------------------------------------------------
	// Be Vietnam Pro do người Việt thiết kế, dấu được vẽ riêng chứ không
	// ghép máy móc như phần lớn font phương Tây.
	//
	// Lưu ý về khóa 'variant': ở đây bắt buộc phải là MẢNG, không phải chuỗi.
	//
	// Kadence gom weight cần tải trong maybe_add_google_font() (styles/component.php).
	// Với font tiêu đề, nó chỉ quét weight của h1..h6 khi $font['variant'] là mảng;
	// nếu truyền chuỗi, danh sách weight trả về rỗng và Google chỉ gửi weight 400.
	// Hậu quả: trình duyệt tự bôi đậm giả chữ tiêu đề — dấu tiếng Việt bị dày thô.
	//
	// Mảng này phải chứa mọi weight mà h1..h6 dùng: 600 cho h1–h4 (ta đặt bên dưới),
	// 700 cho h5–h6 (giữ mặc định của Kadence).
	//
	$defaults['heading_font'] = array(
		'family'  => 'Be Vietnam Pro',
		'google'  => true,
		'weight'  => '600',
		'variant' => array( '600', '700' ),
	);

	// Thang cỡ chữ. Bước nhảy giữa các cấp đủ rõ để người đọc quét nhanh
	// thấy ngay cấu trúc bài, nhưng không quá to gây chói.
	$headings = array(
		'h1_font' => array( 40, 34, 28, 1.25 ),
		'h2_font' => array( 30, 27, 24, 1.3 ),
		'h3_font' => array( 24, 22, 20, 1.35 ),
		'h4_font' => array( 20, 19, 18, 1.4 ),
	);
	foreach ( $headings as $key => $spec ) {
		$defaults[ $key ] = array(
			'size'       => array(
				'desktop' => $spec[0],
				'tablet'  => $spec[1],
				'mobile'  => $spec[2],
			),
			'lineHeight' => array(
				'desktop' => $spec[3],
				'tablet'  => $spec[3],
				'mobile'  => $spec[3],
			),
			'family'     => 'inherit',
			'google'     => false,
			'weight'     => '600',
			'variant'    => '600',
			'color'      => 'palette3',
		);
	}

	// --- Nạp font ---------------------------------------------------------
	// Bắt buộc bật bộ ký tự vietnamese, nếu không chữ có dấu sẽ rơi về
	// font dự phòng của hệ điều hành và trông lệch hẳn so với chữ không dấu.
	$defaults['google_subsets']['vietnamese'] = true;
	$defaults['google_subsets']['latin-ext']  = true;

	// Tải font về máy chủ thay vì gọi sang Google mỗi lần có khách.
	// Nhanh hơn rõ rệt với đường truyền trong nước, và không gửi IP người đọc
	// sang bên thứ ba.
	$defaults['load_fonts_local']    = true;
	$defaults['preload_fonts_local'] = true;

	// --- Chiều rộng nội dung ---------------------------------------------
	// 1200px cho trang có lưới, 760px cho bài đọc.
	// 760px ở cỡ chữ 17px cho ra khoảng 70–75 ký tự mỗi dòng — quãng mà mắt
	// người chuyển dòng không bị lạc.
	$defaults['content_width'] = array(
		'size' => 1200,
		'unit' => 'px',
	);
	$defaults['content_narrow_width'] = array(
		'size' => 760,
		'unit' => 'px',
	);

	// --- Ảnh đại diện bài viết -------------------------------------------
	//
	// Kadence mặc định đặt ảnh ở chế độ "behind": ảnh rộng hơn khung bài, và
	// khung bài bị kéo lên trùm lên mép dưới tấm ảnh bằng margin âm.
	//
	// Kiểu đó đẹp khi bài chiếm trọn chiều ngang, nhưng site này có cột mục lục
	// nằm ở lề trái. Phần ảnh thừa ra hai bên tràn đúng vào làn mục lục và đè
	// lên nó — đo được 95px ngang, 73px dọc.
	//
	// Chuyển sang "above": ảnh nằm trọn phía trên, khung bài bắt đầu bên dưới.
	// Mục lục vì thế cũng bắt đầu sau khi hết ảnh, không còn khả năng chồng lấn.
	$defaults['post_feature_position'] = 'above';

	// --- Logo --------------------------------------------------------------
	//
	// Logo là khối ngang rất dẹt, nên chiều rộng quyết định luôn chiều cao
	// header. Ở 200px (mặc định Kadence) menu bị đẩy tràn xuống ba dòng — đo
	// được ở màn 1045px. 165px là con số lớn nhất còn giữ menu gọn một dòng.
	//
	// Từ 04/08/2026 logo đổi sang bản xanh, tỉ lệ 5.2:1 thay vì 3.4:1 (bản cũ
	// có thêm dòng tagline). Ở 165px logo giờ cao khoảng 32px thay vì 48px,
	// phần chữ cao ~17px. Muốn to hơn thì phải tăng số này VÀ đo lại menu ở
	// màn 1045px — đừng tăng mà không đo.
	$defaults['logo_width'] = array(
		'size' => array(
			'desktop' => 165,
			'tablet'  => 150,
			'mobile'  => 135,
		),
		'unit' => array(
			'desktop' => 'px',
			'tablet'  => 'px',
			'mobile'  => 'px',
		),
	);

	// Chỉ hiện logo, KHÔNG hiện thêm tên site dạng chữ.
	//
	// Mặc định của Kadence là 'logo_title' — in cả hai. Tên site ở đây là
	// "Đá quý Lục Yên | Chuyên trang kiến thức đá quý", dài tới mức khối thương
	// hiệu chiếm 591px trong khi ảnh logo chỉ 165px, và menu bị đẩy tràn xuống
	// ba dòng.
	//
	// Logo đã chứa sẵn tên rồi, in lại bằng chữ là thừa.
	$defaults['logo_layout'] = array(
		'include' => array(
			'desktop' => 'logo',
			'tablet'  => 'logo',
			'mobile'  => 'logo',
		),
		'layout'  => array(
			'desktop' => 'standard',
			'tablet'  => 'standard',
			'mobile'  => 'standard',
		),
	);

	// --- Cột bên -----------------------------------------------------------
	//
	// Kadence có sẵn bố cục hai cột, nên không tự dựng lại: bật tùy chọn của nó
	// thì chủ site đổi được trong Customizer mà không cần sửa code.
	//
	// 'right' = nội dung bên trái, cột bên bên phải.
	//
	// Trang bài viết và trang chuyên mục dùng HAI vùng widget khác nhau. Trước
	// đây cả hai — và cả trang chủ — dùng chung 'sidebar-primary', nên mọi widget
	// đặt vào đó hiện ở khắp nơi: widget "Bài viết mới nhất" nằm ngay cạnh khối
	// "Bài mới nhất" của trang chủ, in cùng một danh sách hai lần.
	//
	// Nội dung cột bên bài viết do inc/sidebar.php dựng; 'dql-sb-post' là ô tự do
	// nằm xen giữa các khối đó.
	$defaults['post_layout']              = 'right';
	$defaults['post_sidebar_id']          = 'dql-sb-post';
	$defaults['post_archive_layout']      = 'right';
	$defaults['post_archive_sidebar_id']  = 'sidebar-primary';

	return $defaults;
}
add_filter( 'kadence_theme_options_defaults', 'dql_brand_typography' );
