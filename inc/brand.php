<?php
/**
 * Bộ nhận diện thương hiệu: màu sắc, chữ, chiều rộng nội dung, header.
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
 * LƯU Ý KHI NÂNG CẤP TỪ BẢN 1.x (bảng màu "Sapphire sáng"): site đang chạy đã
 * có bảng màu lưu trong Customizer, nên các mặc định dưới đây KHÔNG tự áp dụng.
 * Vào Customizer → Colors, bấm khôi phục mặc định (hoặc nhập tay theo bảng
 * này) rồi lưu. Xem docs/huong-dan-localwp.md.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bảng màu thương hiệu — ngôn ngữ thiết kế "Đá Thô" (từ bản 2.0).
 *
 * Ý đồ: viên đá chưa mài. Nền là đá hoa trắng Lục Yên — trắng ấm, không trắng
 * tinh. Chữ màu than đá ấm thay cho đen hoặc navy. Ruby Lục Yên là màu nhấn
 * duy nhất, dùng tiết chế: liên kết, nút, nhãn — không bao giờ phủ nền rộng.
 * Màu đá của từng chuyên mục (spinel, tourmaline, hổ phách…) nằm ở CSS
 * (--dql-da-*), không chiếm khe palette của Kadence.
 *
 * Tên khe màu là do Kadence quy định, không đổi được:
 *
 *   palette1  Màu nhấn — link, nút bấm
 *   palette2  Màu nhấn phụ — trạng thái di chuột, nền đậm
 *   palette3  Chữ đậm nhất — tiêu đề
 *   palette4  Chữ đậm — nội dung bài
 *   palette5  Chữ vừa — chú thích
 *   palette6  Chữ nhạt — ngày tháng, thông tin phụ (phải đạt 4.5:1 trên palette8)
 *   palette7  Nền nhạt — viền, hộp
 *   palette8  Nền trang
 *   palette9  Mặt thẻ (trắng)
 *   palette10 Màu bổ túc — để #FfFfFf thì Kadence tự tính từ palette1
 *   palette11..15  Màu thông báo: thành công, thông tin, lỗi, cảnh báo, đánh giá
 *
 * Chế độ tối đặt lại cả bảng này trong assets/css/dark.css.
 *
 * @return string Chuỗi JSON đúng định dạng Kadence yêu cầu.
 */
function dql_brand_palette() {
	$colors = array(
		'palette1'  => '#A3122E', // Ruby Lục Yên — link và nút. 7.2:1 trên nền.
		'palette2'  => '#6E0B20', // Ruby đậm — di chuột, nền đậm.
		'palette3'  => '#1C1917', // Than đá — tiêu đề.
		'palette4'  => '#44403B', // Đá đen ấm — nội dung bài.
		'palette5'  => '#5E5750', // Đá xám — chú thích. 6.4:1 trên nền trang.
		'palette6'  => '#6F6861', // Bụi đá — meta. 5.3:1 trên #F6F3EE, 4.6:1 trên nền hộp #ECE6DC.
		'palette7'  => '#ECE6DC', // Vôi — viền, nền hộp.
		'palette8'  => '#F6F3EE', // Đá hoa trắng — nền trang.
		'palette9'  => '#FFFFFF', // Mặt thẻ.
		'palette10' => '#FfFfFf', // Giữ nguyên: mã đặc biệt báo Kadence tự tính.
		'palette11' => '#2F6B4F', // Thành công — tourmaline.
		'palette12' => '#1F4E8C', // Thông tin — spinel xanh.
		'palette13' => '#B42318', // Lỗi.
		'palette14' => '#8A5410', // Cảnh báo — hổ phách đậm (bản sáng #B8741A chỉ 3.1:1).
		'palette15' => '#C9961A', // Đánh giá sao.
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
 * Giá trị mặc định cho chữ, bố cục và header.
 *
 * @param array $defaults Mặc định gốc của Kadence.
 * @return array
 */
function dql_brand_typography( $defaults ) {

	// --- Chữ nội dung -----------------------------------------------------
	// Be Vietnam Pro, 17px — cỡ "Body" của Apple HIG. Tiếng Việt nhiều dấu nên
	// cần dòng cao hơn tiếng Anh: 1.65 thay vì 1.47 của HIG, để dấu không chạm
	// dòng trên.
	//
	// Vì sao Be Vietnam Pro làm chữ nội dung (bản 1.x dùng Inter): do người
	// Việt thiết kế, dấu được vẽ riêng chứ không ghép máy móc. Ở bản 1.x nó chỉ
	// làm tiêu đề; nay tiêu đề chuyển sang serif nên nó về đúng chỗ.
	$defaults['base_font'] = array(
		'size'       => array(
			'desktop' => 17,
			'tablet'  => 17,
			'mobile'  => 16,
		),
		'lineHeight' => array(
			'desktop' => 1.65,
			'tablet'  => 1.65,
			'mobile'  => 1.6,
		),
		'family'     => 'Be Vietnam Pro',
		'google'     => true,
		'weight'     => '400',
		'variant'    => 'regular',
		'color'      => 'palette4',
	);

	// --- Chữ tiêu đề ------------------------------------------------------
	// Fraunces: serif có nét bào mềm — đúng cảm giác đá mài tay, và là thứ cho
	// site chất "tạp chí" thay vì "blog công cụ". Hỗ trợ tiếng Việt đầy đủ.
	//
	// Lưu ý về khóa 'variant': ở đây bắt buộc phải là MẢNG, không phải chuỗi.
	//
	// Kadence gom weight cần tải trong maybe_add_google_font() (styles/component.php).
	// Với font tiêu đề, nó chỉ quét weight của h1..h6 khi $font['variant'] là mảng;
	// nếu truyền chuỗi, danh sách weight trả về rỗng và Google chỉ gửi weight 400.
	// Hậu quả: trình duyệt tự bôi đậm giả chữ tiêu đề — dấu tiếng Việt bị dày thô.
	//
	// Mảng này phải chứa mọi weight mà h1..h6 dùng: 600 cho h1–h4, 700 cho h5–h6.
	$defaults['heading_font'] = array(
		'family'  => 'Fraunces',
		'google'  => true,
		'weight'  => '600',
		'variant' => array( '500', '600', '700' ),
	);

	// Thang cỡ chữ ánh xạ từ Apple HIG (Large Title 34 → 40 trên web, Title 1
	// 28 → 32, Title 2 22 → 24, Title 3 20). Serif cần dòng chặt hơn sans.
	$headings = array(
		'h1_font' => array( 40, 34, 30, 1.15 ),
		'h2_font' => array( 32, 28, 26, 1.2 ),
		'h3_font' => array( 24, 22, 21, 1.25 ),
		'h4_font' => array( 20, 19, 18, 1.3 ),
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

	// Menu và nút dùng chữ nội dung ở weight 500/600. Khai báo ở đây để Kadence
	// biết mà tải thêm hai weight đó của Be Vietnam Pro — không khai báo thì
	// trình duyệt bôi đậm giả.
	$defaults['primary_navigation_typography'] = array(
		'size'       => array(
			'desktop' => 15,
		),
		'lineHeight' => array(
			'desktop' => '',
		),
		'family'     => 'inherit',
		'google'     => false,
		'weight'     => '500',
		'variant'    => '500',
		'transform'  => '',
	);

	$defaults['buttons_typography'] = array(
		'size'       => array(
			'desktop' => 15,
		),
		'lineHeight' => array(
			'desktop' => '',
		),
		'family'     => 'inherit',
		'google'     => false,
		'weight'     => '600',
		'variant'    => '600',
		'transform'  => '',
	);

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
	// 1280px cho trang có lưới (lề 64px trên màn 1440 — theo lưới HIG), 760px
	// cho bài đọc: ở 17px cho ra 70–75 ký tự mỗi dòng.
	$defaults['content_width'] = array(
		'size' => 1280,
		'unit' => 'px',
	);
	$defaults['content_narrow_width'] = array(
		'size' => 760,
		'unit' => 'px',
	);

	// --- Ảnh đại diện bài viết -------------------------------------------
	// 'above': ảnh nằm trọn phía trên, không trùm vào làn mục lục ở lề trái.
	// Không kéo full màn hình vì 82% ảnh gốc của site dưới 1200px — xem
	// docs/thiet-ke.md.
	$defaults['post_feature_position'] = 'above';

	// Dòng chuyên mục ở đầu bài do theme in (có huy hiệu đá), nên bỏ bản của
	// Kadence khỏi khối tiêu đề để không in hai lần.
	$defaults['post_title_elements'] = array( 'title', 'meta', 'excerpt' );

	// --- Header -----------------------------------------------------------
	//
	// Header dính, vật liệu mờ (CSS lo). Bên phải thêm ô tìm kiếm và phần tử
	// HTML chứa nút đổi chế độ sáng/tối. Nếu Customizer đã lưu bố cục header,
	// mặc định này không áp dụng — khi đó kéo thêm "Search" và "HTML" vào hàng
	// header trong Header Builder, và dán [dql_doi_nen] vào ô HTML.
	$defaults['header_desktop_items'] = array(
		'top'    => array(
			'top_left'   => array(),
			'top_center' => array(),
			'top_right'  => array(),
		),
		'main'   => array(
			'main_left'   => array( 'logo' ),
			'main_center' => array(),
			'main_right'  => array( 'navigation', 'search', 'html' ),
		),
		'bottom' => array(
			'bottom_left'   => array(),
			'bottom_center' => array(),
			'bottom_right'  => array(),
		),
	);
	$defaults['header_html_content'] = '[dql_doi_nen]';
	$defaults['header_sticky']       = 'main';

	// --- Logo --------------------------------------------------------------
	//
	// Logo là khối ngang rất dẹt, nên chiều rộng quyết định luôn chiều cao
	// header. 165px là con số lớn nhất còn giữ menu gọn một dòng (đo ở 1045px).
	//
	// Bản 2.0 đổi bảng màu sang ruby/than đá; logo xanh navy của bản 1.x lệch
	// tông. Hai cách: (a) vẽ lại logo theo bảng màu mới, hoặc (b) bỏ logo ảnh,
	// dùng tên site dạng chữ — theme đã dựng sẵn dấu hiệu đá ruby đứng trước
	// tên site (xem main.css, mục "Thương hiệu"). Khuyến nghị (b) cho tới khi
	// có logo mới.
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
	// Trang bài viết và trang chuyên mục dùng HAI vùng widget khác nhau. Nội
	// dung cột bên bài viết do inc/sidebar.php dựng; 'dql-sb-post' là ô tự do.
	$defaults['post_layout']              = 'right';
	$defaults['post_sidebar_id']          = 'dql-sb-post';
	$defaults['post_archive_layout']      = 'right';
	$defaults['post_archive_sidebar_id']  = 'sidebar-primary';

	return $defaults;
}
add_filter( 'kadence_theme_options_defaults', 'dql_brand_typography' );
