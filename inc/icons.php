<?php
/**
 * Bộ icon chuyên mục và icon giao diện — ngôn ngữ thiết kế "Đá Thô".
 *
 * Mọi icon vẽ trên lưới 24pt, nét 1.75, đầu nét tròn — cùng ngữ pháp với
 * SF Symbols của Apple để đứng cạnh chữ ở mọi cỡ mà không lệch tông. Nhúng
 * thẳng SVG vào HTML: không tốn request, không nhấp nháy lúc font icon chưa về,
 * và đổi màu bằng currentColor hoặc biến CSS.
 *
 * Mỗi chuyên mục có một icon và một MÀU ĐÁ riêng. Theme tự khớp theo slug
 * chuyên mục (bảng dql_icon_tu_khoa), chủ site đổi tay ở màn Sửa chuyên mục.
 * Màu lưu dưới dạng KHÓA ('ruby', 'spinel'…) chứ không lưu mã màu, vì chế độ
 * tối cần một bộ giá trị khác — xem assets/css/main.css, mục "Màu đá".
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

const DQL_ICON_META = '_dql_icon';
const DQL_COLOR_META = '_dql_mau_da';

/**
 * Danh mục icon chuyên mục.
 *
 * Mỗi mục: tên hiển thị, đường nét SVG (bên trong viewBox 0 0 24 24, không có
 * thẻ <svg>), và màu đá mặc định.
 *
 * @return array<string, array{ten: string, path: string, mau: string}>
 */
function dql_icon_danh_muc() {
	static $icons = null;

	if ( null !== $icons ) {
		return $icons;
	}

	$icons = array(
		'da-quy'       => array(
			'ten'  => __( 'Đá quý — viên đá cắt giác', 'kadence-daquylucyen' ),
			'path' => '<path d="M8 3h8l4 6-8 12L4 9l4-6Z"/><path d="M4 9h16M8 3l4 6 4-6M12 9v12"/>',
			'mau'  => 'ruby',
		),
		'da-ban-quy'   => array(
			'ten'  => __( 'Đá bán quý — cụm tinh thể thô', 'kadence-daquylucyen' ),
			'path' => '<path d="M4 20h16"/><path d="m5.5 20 2-7.5 3 3L13.5 4.5 16 11l2-2.5L19 20"/>',
			'mau'  => 'garnet',
		),
		'trang-suc'    => array(
			'ten'  => __( 'Trang sức — nhẫn gắn đá', 'kadence-daquylucyen' ),
			'path' => '<circle cx="12" cy="14.5" r="6"/><path d="m9.5 7.5 2.5-4 2.5 4-2.5 2.5-2.5-2.5Z"/>',
			'mau'  => 'amber',
		),
		'tranh-da-quy' => array(
			'ten'  => __( 'Tranh đá quý — khung tranh', 'kadence-daquylucyen' ),
			'path' => '<rect x="3" y="4" width="18" height="16" rx="1.5"/><path d="m3 16 5-5 4 4 3-3 6 6"/><circle cx="16" cy="8.5" r="1.5"/>',
			'mau'  => 'amethyst',
		),
		'phong-thuy'   => array(
			'ten'  => __( 'Phong thủy — la bàn', 'kadence-daquylucyen' ),
			'path' => '<circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="2.5"/><path d="M12 3.5v3M12 17.5v3M3.5 12h3M17.5 12h3"/><path d="m15.5 8.5-2 2M8.5 15.5l2-2"/>',
			'mau'  => 'sapphire',
		),
		'kiem-dinh'    => array(
			'ten'  => __( 'Kiểm định — kính lúp có dấu kiểm', 'kadence-daquylucyen' ),
			'path' => '<circle cx="10.5" cy="10.5" r="6.5"/><path d="m20 20-4.8-4.8"/><path d="m7.8 10.5 1.9 1.9 3.6-3.8"/>',
			'mau'  => 'tourmaline',
		),
		'tu-van'       => array(
			'ten'  => __( 'Tư vấn chọn mua — hội thoại', 'kadence-daquylucyen' ),
			'path' => '<path d="M21 12.5a7.5 7.5 0 0 1-11 6.6L5 20.5l1.4-4.3A7.5 7.5 0 1 1 21 12.5Z"/><path d="M9 11h6M9 14.5h4"/>',
			'mau'  => 'spinel',
		),
		'cho-da-quy'   => array(
			'ten'  => __( 'Chợ đá quý — cán cân', 'kadence-daquylucyen' ),
			'path' => '<path d="M12 4v16M5 20h14"/><path d="M12 6 5 8.5m7-2.5 7 2.5"/><path d="M2.5 14a2.5 2.5 0 0 0 5 0L5 8.5 2.5 14ZM16.5 14a2.5 2.5 0 0 0 5 0L19 8.5l-2.5 5.5Z"/>',
			'mau'  => 'topaz',
		),
		'van-hoa'      => array(
			'ten'  => __( 'Văn hóa & con người — nhà sàn', 'kadence-daquylucyen' ),
			'path' => '<path d="m3 11 9-7 9 7"/><path d="M5.5 10v6h13v-6"/><path d="M7 16v5M11 16v5M13 16v5M17 16v5M9 13h6"/>',
			'mau'  => 'tourmaline',
		),
		'du-lich'      => array(
			'ten'  => __( 'Du lịch — núi soi bóng nước', 'kadence-daquylucyen' ),
			'path' => '<path d="m2.5 16 5-8 3.5 5L14.5 7l7 9"/><path d="M2.5 20c1.5 0 1.5-1 3-1s1.5 1 3 1 1.5-1 3-1 1.5 1 3 1 1.5-1 3-1 1.5 1 3 1"/>',
			'mau'  => 'spinel',
		),
		'am-thuc'      => array(
			'ten'  => __( 'Ẩm thực — bát và đũa', 'kadence-daquylucyen' ),
			'path' => '<path d="M3.5 12h17a8.5 8.5 0 0 1-17 0Z"/><path d="M9 20.5h6"/><path d="m14 3-3 8M18 3.5l-4 7.5"/>',
			'mau'  => 'amber',
		),
		'lich-su'      => array(
			'ten'  => __( 'Lịch sử & di tích — cổng đền', 'kadence-daquylucyen' ),
			'path' => '<path d="M3 8c3-2.5 15-2.5 18 0"/><path d="M6 8v12M18 8v12M3 20h18M9.5 8V5h5v3M9.5 13h5"/>',
			'mau'  => 'garnet',
		),
		'lang-nghe'    => array(
			'ten'  => __( 'Làng nghề & khai thác — cuốc chim', 'kadence-daquylucyen' ),
			'path' => '<path d="m4 20 9.5-9.5"/><path d="M10.5 5c3.5-1.5 7.5-.5 10 2-2.5 2.5-3.5 6.5-2 10"/><path d="m12.5 8.5 3 3"/>',
			'mau'  => 'marble',
		),
		'tin-tuc'      => array(
			'ten'  => __( 'Tin tức & sự kiện — tờ báo', 'kadence-daquylucyen' ),
			'path' => '<path d="M4 4h13a2 2 0 0 1 2 2v12a2 2 0 0 0 2 2H6a2 2 0 0 1-2-2V4Z"/><path d="M19 20a2 2 0 0 1-2-2V9"/><path d="M7.5 8h6M7.5 11.5h6M7.5 15h4"/>',
			'mau'  => 'coal',
		),
		'san-vat'      => array(
			'ten'  => __( 'Nông sản & sản vật — mầm lá', 'kadence-daquylucyen' ),
			'path' => '<path d="M12 21v-9"/><path d="M12 12c0-4.5 3-7.5 8-7.5 0 4.5-3 7.5-8 7.5Z"/><path d="M12 15c0-3.5-2.5-6-6.5-6 0 3.5 2.5 6 6.5 6Z"/>',
			'mau'  => 'jade',
		),
		'dia-chat'     => array(
			'ten'  => __( 'Địa chất & khoáng sản — địa tầng', 'kadence-daquylucyen' ),
			'path' => '<path d="M3 6c3-1.5 6 1.5 9 0s6-1.5 9 0"/><path d="M3 10.5c3-1.5 6 1.5 9 0s6-1.5 9 0"/><path d="M3 15c3-1.5 6 1.5 9 0s6-1.5 9 0"/><path d="M3 19.5c3-1.5 6 1.5 9 0s6-1.5 9 0"/>',
			'mau'  => 'smoky',
		),
	);

	/**
	 * Cho plugin hoặc child theme thêm icon.
	 *
	 * @param array $icons Danh mục icon.
	 */
	$icons = apply_filters( 'dql_icon_danh_muc', $icons );

	return $icons;
}

/**
 * Icon giao diện (không gắn với chuyên mục).
 *
 * @return array<string, string> slug => đường nét SVG.
 */
function dql_icon_giao_dien() {
	return array(
		'tim'        => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
		'toi'        => '<path d="M20 14.5A8.5 8.5 0 0 1 9.5 4a8.5 8.5 0 1 0 10.5 10.5Z"/>',
		'sang'       => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2.5M12 19.5V22M2 12h2.5M19.5 12H22M4.9 4.9l1.8 1.8M17.3 17.3l1.8 1.8M4.9 19.1l1.8-1.8M17.3 6.7l1.8-1.8"/>',
		'menu'       => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'tiep'       => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'phai'       => '<path d="m9 18 6-6-6-6"/>',
		'trai'       => '<path d="m15 18-6-6 6-6"/>',
		'len'        => '<path d="M12 19V5M5 12l7-7 7 7"/>',
		'dong-ho'    => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7v5l3.5 2"/>',
		'email'      => '<rect x="2.5" y="5" width="19" height="14" rx="2.5"/><path d="m3 7 8.3 5.6a1.3 1.3 0 0 0 1.4 0L21 7"/>',
		'luu'        => '<path d="M4 19.5V5a1.5 1.5 0 0 1 1.5-1.5h13A1.5 1.5 0 0 1 20 5v14.5l-8-4.5-8 4.5Z"/>',
		'chia-se'    => '<path d="M12 15V4M8 8l4-4 4 4"/><path d="M5 13v5a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-5"/>',
		'la-chan'    => '<path d="M12 3 4 7v6c0 5 3.4 7.4 8 8 4.6-.6 8-3 8-8V7l-8-4Z"/><path d="m9 12 2 2 4-4"/>',
		'cong'       => '<path d="M5 12h14"/><path d="M12 5v14"/>',
		'canh-bao'   => '<path d="M12 3 2 20h20z"/><path d="M12 9v5"/><path d="M12 17h.01"/>',
	);
}

/**
 * Các màu đá dùng cho huy hiệu chuyên mục.
 *
 * Chỉ trả về KHÓA và tên. Giá trị màu thật nằm ở CSS (biến --dql-da-*), vì mỗi
 * khóa có hai giá trị: một cho nền sáng, một nâng sáng hai bậc cho nền tối.
 *
 * @return array<string, string>
 */
function dql_mau_da_danh_muc() {
	return array(
		'ruby'       => __( 'Ruby Lục Yên (đỏ)', 'kadence-daquylucyen' ),
		'garnet'     => __( 'Garnet (đỏ trầm)', 'kadence-daquylucyen' ),
		'spinel'     => __( 'Spinel xanh', 'kadence-daquylucyen' ),
		'sapphire'   => __( 'Sapphire (xanh tím)', 'kadence-daquylucyen' ),
		'tourmaline' => __( 'Tourmaline (xanh lá)', 'kadence-daquylucyen' ),
		'jade'       => __( 'Ngọc bích (lục lam)', 'kadence-daquylucyen' ),
		'amber'      => __( 'Hổ phách (vàng nâu)', 'kadence-daquylucyen' ),
		'topaz'      => __( 'Topaz (cam)', 'kadence-daquylucyen' ),
		'amethyst'   => __( 'Thạch anh tím', 'kadence-daquylucyen' ),
		'marble'     => __( 'Đá hoa (xám)', 'kadence-daquylucyen' ),
		'smoky'      => __( 'Thạch anh khói (nâu)', 'kadence-daquylucyen' ),
		'coal'       => __( 'Than đá (đen ấm)', 'kadence-daquylucyen' ),
	);
}

/**
 * Bảng khớp icon theo từ khóa trong slug chuyên mục.
 *
 * Thứ tự quan trọng: mục nào đứng trước được thử trước. "da-ban-quy" phải đứng
 * trước "da-quy" vì slug 'da-ban-quy' chứa cả hai chuỗi.
 *
 * @return array<string, string[]> icon => danh sách từ khóa.
 */
function dql_icon_tu_khoa() {
	return array(
		'da-ban-quy'   => array( 'ban-quy', 'thach-anh', 'tourmaline', 'garnet' ),
		'tranh-da-quy' => array( 'tranh' ),
		'cho-da-quy'   => array( 'cho-da', 'cho-', 'thi-truong', 'gia-' ),
		'da-quy'       => array( 'da-quy', 'ruby', 'sapphire', 'spinel', 'kien-thuc' ),
		'trang-suc'    => array( 'trang-suc', 'nhan', 'vong', 'mat-day' ),
		'phong-thuy'   => array( 'phong-thuy', 'menh', 'hop-menh' ),
		'kiem-dinh'    => array( 'kiem-dinh', 'that-gia', 'chung-nhan', 'phan-biet', 'giam-dinh' ),
		'tu-van'       => array( 'tu-van', 'chon-mua', 'mua-' ),
		'van-hoa'      => array( 'van-hoa', 'con-nguoi', 'dan-toc', 'le-hoi', 'nguoi-tay', 'ban-lang' ),
		'du-lich'      => array( 'du-lich', 'diem-den', 'thac-ba', 'kham-pha' ),
		'am-thuc'      => array( 'am-thuc', 'mon-an', 'dac-san', 'an-uong' ),
		'lich-su'      => array( 'lich-su', 'di-tich', 'den-', 'co-' ),
		'lang-nghe'    => array( 'lang-nghe', 'khai-thac', 'che-tac', 'tho-', 'mai-da' ),
		'tin-tuc'      => array( 'tin-tuc', 'su-kien', 'tin-', 'thong-bao' ),
		'san-vat'      => array( 'san-vat', 'nong-san', 'nong-nghiep', 'cam-sanh', 'trong-trot' ),
		'dia-chat'     => array( 'dia-chat', 'khoang-san', 'mo-da', 'dia-ly' ),
	);
}

/**
 * Icon của một chuyên mục.
 *
 * Ưu tiên: chọn tay (term meta) → khớp từ khóa trong slug (và slug của chuyên
 * mục cha, để chuyên mục con "Ruby" nằm dưới "Đá quý" vẫn nhận đúng icon) →
 * 'da-quy' làm mặc định.
 *
 * @param WP_Term|int $term Chuyên mục hoặc ID.
 * @return string Slug icon trong dql_icon_danh_muc().
 */
function dql_category_icon_slug( $term ) {
	$term = get_term( $term, 'category' );

	if ( ! $term || is_wp_error( $term ) ) {
		return 'da-quy';
	}

	$icons = dql_icon_danh_muc();
	$chon  = (string) get_term_meta( $term->term_id, DQL_ICON_META, true );

	if ( $chon && isset( $icons[ $chon ] ) ) {
		return $chon;
	}

	$slugs = array( $term->slug );

	foreach ( get_ancestors( $term->term_id, 'category' ) as $cha_id ) {
		$cha = get_term( $cha_id, 'category' );

		if ( $cha && ! is_wp_error( $cha ) ) {
			$slugs[] = $cha->slug;
		}
	}

	foreach ( $slugs as $slug ) {
		foreach ( dql_icon_tu_khoa() as $icon => $tu_khoa ) {
			foreach ( $tu_khoa as $tk ) {
				if ( false !== strpos( $slug, $tk ) && isset( $icons[ $icon ] ) ) {
					return $icon;
				}
			}
		}
	}

	return 'da-quy';
}

/**
 * Khóa màu đá của một chuyên mục.
 *
 * @param WP_Term|int $term Chuyên mục hoặc ID.
 * @return string Khóa trong dql_mau_da_danh_muc().
 */
function dql_category_color_key( $term ) {
	$term = get_term( $term, 'category' );

	if ( ! $term || is_wp_error( $term ) ) {
		return 'ruby';
	}

	$mau  = dql_mau_da_danh_muc();
	$chon = (string) get_term_meta( $term->term_id, DQL_COLOR_META, true );

	if ( $chon && isset( $mau[ $chon ] ) ) {
		return $chon;
	}

	$icons = dql_icon_danh_muc();
	$icon  = dql_category_icon_slug( $term );

	return isset( $icons[ $icon ]['mau'] ) ? $icons[ $icon ]['mau'] : 'ruby';
}

/**
 * Chuỗi SVG của một icon.
 *
 * @param string $slug Slug icon — trong danh mục chuyên mục hoặc danh mục giao diện.
 * @param array  $args {
 *     @type string $class Class bổ sung.
 *     @type float  $stroke Độ dày nét. Mặc định 1.75; icon to dùng 1.5, nhỏ dùng 2.
 *     @type string $label Nhãn cho trình đọc màn hình. Bỏ trống thì icon là trang trí.
 * }
 * @return string
 */
function dql_icon( $slug, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'class'  => '',
			'stroke' => 1.75,
			'label'  => '',
		)
	);

	$cm   = dql_icon_danh_muc();
	$gd   = dql_icon_giao_dien();
	$path = '';

	if ( isset( $cm[ $slug ] ) ) {
		$path = $cm[ $slug ]['path'];
	} elseif ( isset( $gd[ $slug ] ) ) {
		$path = $gd[ $slug ];
	} else {
		$path = $cm['da-quy']['path'];
	}

	$a11y = $args['label']
		? ' role="img" aria-label="' . esc_attr( $args['label'] ) . '"'
		: ' aria-hidden="true" focusable="false"';

	return sprintf(
		'<svg class="dql-icon %1$s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="%2$s" stroke-linecap="round" stroke-linejoin="round"%3$s>%4$s</svg>',
		esc_attr( trim( $args['class'] ) ),
		esc_attr( (string) $args['stroke'] ),
		$a11y,
		$path // Chuỗi SVG cố định trong mã nguồn, không có dữ liệu người dùng.
	);
}

/**
 * In một icon.
 *
 * @param string $slug Slug icon.
 * @param array  $args Xem dql_icon().
 */
function dql_the_icon( $slug, $args = array() ) {
	echo dql_icon( $slug, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Đã thoát trong dql_icon().
}

/**
 * Huy hiệu đá của một chuyên mục: khối sáu cạnh lệch tô màu đá, icon trắng ở giữa.
 *
 * Cỡ do CSS quyết định qua class --nho (24), --vua (40), --lon (64), --rat-lon (96).
 * Màu đi qua biến --dql-gem để chế độ tối tự đổi giá trị.
 *
 * @param WP_Term|int $term Chuyên mục.
 * @param string      $size 'nho' | 'vua' | 'lon' | 'rat-lon'.
 * @param string      $extra Class bổ sung.
 * @return string
 */
function dql_category_badge( $term, $size = 'vua', $extra = '' ) {
	$icon  = dql_category_icon_slug( $term );
	$color = dql_category_color_key( $term );

	$stroke = array(
		'nho'     => 2,
		'vua'     => 1.75,
		'lon'     => 1.5,
		'rat-lon' => 1.25,
	);

	return sprintf(
		'<span class="dql-gem dql-gem--%1$s %2$s" style="--dql-gem: var(--dql-da-%3$s)">%4$s</span>',
		esc_attr( $size ),
		esc_attr( $extra ),
		esc_attr( $color ),
		dql_icon( $icon, array( 'stroke' => isset( $stroke[ $size ] ) ? $stroke[ $size ] : 1.75 ) )
	);
}

/**
 * In huy hiệu đá của một chuyên mục.
 *
 * @param WP_Term|int $term  Chuyên mục.
 * @param string      $size  Cỡ.
 * @param string      $extra Class bổ sung.
 */
function dql_the_category_badge( $term, $size = 'vua', $extra = '' ) {
	echo dql_category_badge( $term, $size, $extra ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Đã thoát trong dql_category_badge().
}

/**
 * Nhãn chuyên mục kèm huy hiệu nhỏ — dùng trên thẻ bài, dòng bài, đầu bài viết.
 *
 * @param WP_Term $term  Chuyên mục.
 * @param bool    $link  Có bọc liên kết không.
 * @param string  $extra Class bổ sung.
 * @return string
 */
function dql_category_label( $term, $link = true, $extra = '' ) {
	if ( ! $term || is_wp_error( $term ) ) {
		return '';
	}

	$inner = dql_category_badge( $term, 'nho' ) . '<span class="dql-cat-label__ten">' . esc_html( $term->name ) . '</span>';
	$style = ' style="--dql-gem: var(--dql-da-' . esc_attr( dql_category_color_key( $term ) ) . ')"';

	if ( $link ) {
		return '<a class="dql-cat-label ' . esc_attr( $extra ) . '" href="' . esc_url( get_category_link( $term->term_id ) ) . '"' . $style . '>' . $inner . '</a>';
	}

	return '<span class="dql-cat-label ' . esc_attr( $extra ) . '"' . $style . '>' . $inner . '</span>';
}

/**
 * Chuyên mục chính của một bài — chuyên mục con sâu nhất, để nhãn nói đúng chủ đề.
 *
 * get_the_category() trả về theo tên A–Z, nên bài nằm ở "Kiến thức đá quý › Đá
 * quý" sẽ lấy cha trước. Ở đây ưu tiên mục KHÔNG phải cha của mục nào khác
 * trong danh sách, và bỏ chuyên mục mặc định "Chưa phân loại".
 *
 * @param int|WP_Post|null $post Bài viết.
 * @return WP_Term|null
 */
function dql_post_main_category( $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return null;
	}

	$cats = get_the_category( $post->ID );

	if ( ! $cats ) {
		return null;
	}

	$mac_dinh = (int) get_option( 'default_category' );
	$ids      = wp_list_pluck( $cats, 'term_id' );
	$ung_vien = array();

	foreach ( $cats as $cat ) {
		if ( $cat->term_id === $mac_dinh && count( $cats ) > 1 ) {
			continue;
		}

		$la_cha = false;

		foreach ( $cats as $khac ) {
			if ( (int) $khac->parent === (int) $cat->term_id ) {
				$la_cha = true;
				break;
			}
		}

		if ( ! $la_cha ) {
			$ung_vien[] = $cat;
		}
	}

	if ( ! $ung_vien ) {
		return $cats[0];
	}

	return $ung_vien[0];
}

/* ===========================================================================
 * Màn Sửa chuyên mục: chọn icon và màu đá
 * ========================================================================= */

/**
 * In hai ô chọn ở màn Thêm/Sửa chuyên mục.
 *
 * @param WP_Term|string $term Chuyên mục đang sửa, hoặc tên taxonomy ở màn Thêm.
 */
function dql_icon_field( $term ) {
	$term_id = ( $term instanceof WP_Term ) ? $term->term_id : 0;
	$icon    = $term_id ? (string) get_term_meta( $term_id, DQL_ICON_META, true ) : '';
	$mau     = $term_id ? (string) get_term_meta( $term_id, DQL_COLOR_META, true ) : '';
	$tu_dong = $term_id ? dql_category_icon_slug( $term_id ) : '';
	$icons   = dql_icon_danh_muc();

	wp_nonce_field( 'dql_icon_' . $term_id, 'dql_icon_nonce' );

	$mo  = $term_id ? '<tr class="form-field"><th scope="row">' : '<div class="form-field">';
	$gia = $term_id ? '</th><td>' : '';
	$dong = $term_id ? '</td></tr>' : '</div>';

	echo $mo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<label for="dql_icon">' . esc_html__( 'Icon chuyên mục', 'kadence-daquylucyen' ) . '</label>';
	echo $gia; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
	<select name="dql_icon" id="dql_icon">
		<option value=""><?php esc_html_e( '— Tự khớp theo tên —', 'kadence-daquylucyen' ); ?></option>
		<?php foreach ( $icons as $slug => $cfg ) : ?>
			<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $icon, $slug ); ?>><?php echo esc_html( $cfg['ten'] ); ?></option>
		<?php endforeach; ?>
	</select>
	<p class="description">
		<?php
		if ( $tu_dong && ! $icon ) {
			printf(
				/* translators: %s: tên icon đang tự khớp. */
				esc_html__( 'Đang tự khớp: %s. Chọn tay nếu không đúng.', 'kadence-daquylucyen' ),
				'<strong>' . esc_html( $icons[ $tu_dong ]['ten'] ) . '</strong>'
			);
		} else {
			esc_html_e( 'Hiện trên dải chuyên mục, thẻ bài và đầu trang chuyên mục. Để trống thì theme đoán theo slug.', 'kadence-daquylucyen' );
		}
		?>
	</p>
	<?php
	echo $dong; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	echo $mo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<label for="dql_mau_da">' . esc_html__( 'Màu đá', 'kadence-daquylucyen' ) . '</label>';
	echo $gia; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
	<select name="dql_mau_da" id="dql_mau_da">
		<option value=""><?php esc_html_e( '— Theo icon —', 'kadence-daquylucyen' ); ?></option>
		<?php foreach ( dql_mau_da_danh_muc() as $khoa => $ten ) : ?>
			<option value="<?php echo esc_attr( $khoa ); ?>" <?php selected( $mau, $khoa ); ?>><?php echo esc_html( $ten ); ?></option>
		<?php endforeach; ?>
	</select>
	<p class="description"><?php esc_html_e( 'Màu huy hiệu. Mỗi màu có một bản sáng và một bản nâng sáng cho chế độ tối, theme tự chọn.', 'kadence-daquylucyen' ); ?></p>
	<?php
	echo $dong; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'category_edit_form_fields', 'dql_icon_field' );
add_action( 'category_add_form_fields', 'dql_icon_field' );

/**
 * Lưu icon và màu đá.
 *
 * @param int $term_id ID chuyên mục.
 */
function dql_icon_save( $term_id ) {
	if ( ! isset( $_POST['dql_icon_nonce'] ) ) {
		return;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['dql_icon_nonce'] ) );

	// Ở màn Thêm, nonce được tạo với term_id = 0 vì lúc đó chưa có ID.
	if ( ! wp_verify_nonce( $nonce, 'dql_icon_' . $term_id ) && ! wp_verify_nonce( $nonce, 'dql_icon_0' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_term', $term_id ) ) {
		return;
	}

	$icon = isset( $_POST['dql_icon'] ) ? sanitize_key( wp_unslash( $_POST['dql_icon'] ) ) : '';
	$mau  = isset( $_POST['dql_mau_da'] ) ? sanitize_key( wp_unslash( $_POST['dql_mau_da'] ) ) : '';

	if ( $icon && isset( dql_icon_danh_muc()[ $icon ] ) ) {
		update_term_meta( $term_id, DQL_ICON_META, $icon );
	} else {
		delete_term_meta( $term_id, DQL_ICON_META );
	}

	if ( $mau && isset( dql_mau_da_danh_muc()[ $mau ] ) ) {
		update_term_meta( $term_id, DQL_COLOR_META, $mau );
	} else {
		delete_term_meta( $term_id, DQL_COLOR_META );
	}
}
add_action( 'edited_category', 'dql_icon_save' );
add_action( 'created_category', 'dql_icon_save' );

/**
 * Cột "Icon" trong bảng danh sách chuyên mục, để nhìn một lượt biết mục nào
 * chưa có icon hợp lý.
 *
 * @param array $columns Các cột.
 * @return array
 */
function dql_icon_column( $columns ) {
	$moi = array();

	foreach ( $columns as $key => $label ) {
		if ( 'name' === $key ) {
			$moi['dql_icon'] = __( 'Icon', 'kadence-daquylucyen' );
		}

		$moi[ $key ] = $label;
	}

	return $moi;
}
add_filter( 'manage_edit-category_columns', 'dql_icon_column' );

/**
 * Nội dung cột "Icon".
 *
 * @param string $content Nội dung hiện có.
 * @param string $column  Tên cột.
 * @param int    $term_id ID chuyên mục.
 * @return string
 */
function dql_icon_column_content( $content, $column, $term_id ) {
	if ( 'dql_icon' !== $column ) {
		return $content;
	}

	return '<span class="dql-gem dql-gem--vua" style="--dql-gem: ' . esc_attr( dql_mau_da_admin( dql_category_color_key( $term_id ) ) ) . ';display:inline-flex;width:32px;height:32px;clip-path:polygon(48% 2%,90% 22%,96% 70%,54% 98%,10% 78%,4% 26%);background:var(--dql-gem);color:#fff;align-items:center;justify-content:center"><span style="width:18px;height:18px;display:inline-flex">' . dql_icon( dql_category_icon_slug( $term_id ) ) . '</span></span>';
}
add_filter( 'manage_category_custom_column', 'dql_icon_column_content', 10, 3 );

/**
 * Mã màu bản sáng — chỉ dùng trong admin, nơi CSS của theme không nạp.
 *
 * Ngoài trang, màu đi qua biến CSS trong assets/css/main.css. Bảng này phải
 * khớp với bảng đó.
 *
 * @param string $khoa Khóa màu.
 * @return string Mã màu hex.
 */
function dql_mau_da_admin( $khoa ) {
	$bang = array(
		'ruby'       => '#A3122E',
		'garnet'     => '#8B2635',
		'spinel'     => '#1F4E8C',
		'sapphire'   => '#2B4C9B',
		'tourmaline' => '#2F6B4F',
		'jade'       => '#2E7D6B',
		'amber'      => '#B8741A',
		'topaz'      => '#C27A22',
		'amethyst'   => '#6B3F8E',
		'marble'     => '#6B7280',
		'smoky'      => '#7A5C3E',
		'coal'       => '#44403B',
	);

	return isset( $bang[ $khoa ] ) ? $bang[ $khoa ] : $bang['ruby'];
}

/* ===========================================================================
 * Nhãn chuyên mục do Kadence in ra (đầu bài viết, thẻ bài trang thẻ/ngày)
 * ========================================================================= */

/**
 * Gắn huy hiệu đá vào danh sách chuyên mục mà WordPress/Kadence in ra.
 *
 * Kadence dùng get_the_category_list() cho dòng chuyên mục ở đầu bài và trên
 * thẻ bài; hàm đó đi qua bộ lọc 'the_category'. Thay vì ghi đè template của
 * Kadence (sẽ gãy khi Kadence đổi markup), ta sửa chuỗi HTML ở đây: mỗi liên
 * kết nhận thêm huy hiệu và class .dql-cat-label.
 *
 * @param string $thelist HTML danh sách chuyên mục.
 * @return string
 */
function dql_the_category_badges( $thelist ) {
	if ( is_admin() || is_feed() || ! in_the_loop() ) {
		return $thelist;
	}

	foreach ( get_the_category() as $cat ) {
		$ten  = esc_html( $cat->name );
		$tim  = 'rel="category tag">' . $ten . '</a>';
		$thay = 'rel="category tag" class="dql-cat-label" style="--dql-gem: var(--dql-da-' . esc_attr( dql_category_color_key( $cat ) ) . ')">'
			. dql_category_badge( $cat, 'nho' )
			. '<span class="dql-cat-label__ten">' . $ten . '</span></a>';

		$thelist = str_replace( $tim, $thay, $thelist );
	}

	return $thelist;
}
add_filter( 'the_category', 'dql_the_category_badges' );
