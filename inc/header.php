<?php
/**
 * Dải chuyên mục dưới header, và vài thứ cắm vào header của Kadence.
 *
 * Dải "Khám phá": một hàng chip có huy hiệu đá, mỗi chip là một chuyên mục
 * cấp lá (Đá quý, Kiểm định, Du lịch…), không phải chuyên mục cha. Lấy cảm hứng từ thanh "trending" của các theme tạp chí, nhưng ở đây dẫn
 * bằng CHỦ ĐỀ chứ không bằng bài — đúng nguyên tắc "cổng kiến thức" của site.
 * Đây cũng là nơi người đọc thấy ngay site viết về cả Lục Yên: văn hóa, du
 * lịch, ẩm thực… không chỉ đá quý.
 *
 * Header của Kadence giữ nguyên (header builder vẫn hoạt động). Dải này cắm
 * vào action 'kadence_after_header', nằm ngay dưới header, trước nội dung.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

/**
 * Thứ tự chip trên dải, theo slug — đúng thứ tự thiết kế bản 1.
 *
 * Chuyên mục không có trong danh sách đứng sau, xếp theo số bài giảm dần.
 *
 * @return string[]
 */
function dql_strip_order() {
	return apply_filters(
		'dql_strip_order',
		array( 'da-quy', 'phong-thuy', 'kiem-dinh', 'trang-suc', 'van-hoa', 'du-lich', 'am-thuc', 'cho-da-quy', 'lich-su' )
	);
}

/**
 * Tên ngắn của chip, theo slug — bản 1 vẽ "Phong thủy", "Chợ đá" chứ không
 * phải tên đầy đủ "Phong thủy & hợp mệnh", "Chợ đá quý".
 *
 * Chuyên mục không có trong danh sách hiện tên đầy đủ.
 *
 * @return string[]
 */
function dql_strip_labels() {
	return apply_filters(
		'dql_strip_labels',
		array(
			'phong-thuy' => __( 'Phong thủy', 'kadence-daquylucyen' ),
			'van-hoa'    => __( 'Văn hóa', 'kadence-daquylucyen' ),
			'lich-su'    => __( 'Lịch sử', 'kadence-daquylucyen' ),
			'dia-chat'   => __( 'Địa chất', 'kadence-daquylucyen' ),
			'cho-da-quy' => __( 'Chợ đá', 'kadence-daquylucyen' ),
			'lang-nghe'  => __( 'Làng nghề', 'kadence-daquylucyen' ),
			'san-vat'    => __( 'Sản vật', 'kadence-daquylucyen' ),
			'tu-van'     => __( 'Tư vấn', 'kadence-daquylucyen' ),
		)
	);
}

/**
 * Các chuyên mục hiện trên dải.
 *
 * Chuyên mục cấp lá (không có con) có bài, bỏ "Chưa phân loại". Chuyên mục cha
 * như "Kiến thức đá quý" hay "Lục Yên" đã có trong menu chính; dải đưa người
 * đọc thẳng tới chủ đề cụ thể. Tối đa 10 — nhiều hơn thì dải cuộn ngang, và
 * một dải cuộn quá dài thành vô nghĩa.
 *
 * @return WP_Term[]
 */
function dql_strip_terms() {
	static $cache = null;

	if ( null !== $cache ) {
		return $cache;
	}

	$mac_dinh = dql_uncategorized_id();
	$tat_ca   = get_categories(
		array(
			'hide_empty' => true,
			'exclude'    => array( $mac_dinh ),
		)
	);
	$co_con   = array_map( 'intval', wp_list_pluck( get_categories( array( 'hide_empty' => false ) ), 'parent' ) );
	$thu_tu   = array_flip( dql_strip_order() );

	$co_bai = array();

	foreach ( $tat_ca as $t ) {
		if ( ! in_array( (int) $t->term_id, $co_con, true ) ) {
			$co_bai[] = array( $t, (int) $t->count );
		}
	}

	usort(
		$co_bai,
		function ( $a, $b ) use ( $thu_tu ) {
			$ta = $thu_tu[ $a[0]->slug ] ?? PHP_INT_MAX;
			$tb = $thu_tu[ $b[0]->slug ] ?? PHP_INT_MAX;

			return $ta === $tb ? $b[1] - $a[1] : $ta <=> $tb;
		}
	);

	$cache = array_map(
		function ( $item ) {
			return $item[0];
		},
		array_slice( $co_bai, 0, 10 )
	);

	return $cache;
}

/**
 * Dải có hiện ở trang này không.
 *
 * @return bool
 */
function dql_strip_visible() {
	$mode = get_theme_mod( 'dql_strip', 'all' );

	if ( 'off' === $mode ) {
		return false;
	}

	if ( 'home' === $mode ) {
		return is_front_page();
	}

	// Không hiện ở trang 404 và trang tìm kiếm: hai trang đó đã có khối gợi ý
	// chuyên mục riêng của chúng.
	return ! is_404() && ! is_search();
}

/**
 * In dải chuyên mục.
 */
function dql_strip_render() {
	if ( ! dql_strip_visible() ) {
		return;
	}

	$terms = dql_strip_terms();

	if ( count( $terms ) < 2 ) {
		return;
	}

	$current = 0;

	// Chip là chuyên mục cấp lá, nên tô sáng đúng chuyên mục đang xem (hoặc
	// chuyên mục của bài). Ở trang chuyên mục cha thì không chip nào sáng.
	if ( is_category() ) {
		$obj = get_queried_object();

		if ( $obj && ! is_wp_error( $obj ) ) {
			$current = (int) $obj->term_id;
		}
	} elseif ( is_singular( 'post' ) ) {
		$chinh = dql_post_main_category();

		if ( $chinh ) {
			$current = (int) $chinh->term_id;
		}
	}
	?>
	<nav class="dql-strip" aria-label="<?php esc_attr_e( 'Khám phá theo chủ đề', 'kadence-daquylucyen' ); ?>">
		<div class="dql-container dql-strip__in">
			<span class="dql-strip__nhan"><?php esc_html_e( 'Khám phá', 'kadence-daquylucyen' ); ?></span>
			<?php foreach ( $terms as $t ) : ?>
				<a
					class="dql-chip dql-chip--gem<?php echo $current === (int) $t->term_id ? ' is-current' : ''; ?>"
					href="<?php echo esc_url( get_category_link( $t->term_id ) ); ?>"
					<?php echo $current === (int) $t->term_id ? ' aria-current="true"' : ''; ?>
				>
					<?php dql_the_category_badge( $t, 'nho' ); ?>
					<span><?php echo esc_html( dql_strip_labels()[ $t->slug ] ?? $t->name ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</nav>
	<?php
}
add_action( 'kadence_after_header', 'dql_strip_render', 5 );

/**
 * Ẩn khỏi menu những chuyên mục chưa có bài.
 *
 * Menu dựng sẵn đủ chuyên mục con của "Lục Yên" để khỏi sửa menu mỗi khi một
 * chuyên mục có bài đầu tiên; mục nào còn rỗng thì không in ra, khỏi dẫn người
 * đọc tới trang trống. Mục con của mục bị ẩn cũng ẩn theo.
 *
 * @param WP_Post[] $items Các mục menu.
 * @return WP_Post[]
 */
function dql_menu_hide_empty_categories( $items ) {
	$an = array();

	foreach ( $items as $item ) {
		if ( 'taxonomy' !== $item->type || 'category' !== $item->object ) {
			continue;
		}

		if ( ! dql_category_deep_count( get_term( (int) $item->object_id, 'category' ) ) ) {
			$an[ (int) $item->ID ] = true;
		}
	}

	if ( ! $an ) {
		return $items;
	}

	// Lặp tới khi không còn mục nào có cha bị ẩn — menu có thể nhiều cấp.
	do {
		$them = false;

		foreach ( $items as $item ) {
			if ( ! isset( $an[ (int) $item->ID ] ) && isset( $an[ (int) $item->menu_item_parent ] ) ) {
				$an[ (int) $item->ID ] = true;
				$them                  = true;
			}
		}
	} while ( $them );

	$items = array_values(
		array_filter(
			$items,
			function ( $item ) use ( $an ) {
				return ! isset( $an[ (int) $item->ID ] );
			}
		)
	);

	// WordPress gắn class 'menu-item-has-children' TRƯỚC filter này. Mục cha
	// mất hết con thì phải gỡ class, không thì vẫn hiện mũi tên thả xuống rỗng.
	$con_cua = array_map( 'intval', wp_list_pluck( $items, 'menu_item_parent' ) );

	foreach ( $items as $item ) {
		if ( ! in_array( (int) $item->ID, $con_cua, true ) ) {
			$item->classes = array_diff( (array) $item->classes, array( 'menu-item-has-children' ) );
		}
	}

	return $items;
}
add_filter( 'wp_nav_menu_objects', 'dql_menu_hide_empty_categories' );

/**
 * Dải tiến độ đọc ở đầu trang bài viết.
 *
 * Chỉ in khung; assets/js/ui.js cập nhật chiều rộng theo vị trí cuộn. Không có
 * JS thì phần tử rỗng 2px — không ai thấy.
 */
function dql_progress_render() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	echo '<div class="dql-progress" aria-hidden="true"><span class="dql-progress__bar" data-dql-progress></span></div>';
}
add_action( 'kadence_after_header', 'dql_progress_render', 4 );

/**
 * Chuỗi cho JavaScript giao diện.
 *
 * @return array
 */
function dql_ui_strings() {
	return array(
		'doiNen'      => __( 'Đổi chế độ sáng / tối', 'kadence-daquylucyen' ),
		'batSang'     => __( 'Bật chế độ sáng', 'kadence-daquylucyen' ),
		'batToi'      => __( 'Bật chế độ tối', 'kadence-daquylucyen' ),
		'coCheDoToi'  => dql_dark_mode_on(),
		'nutDoiNen'   => dql_dark_mode_button(),
		'lenDauTrang' => __( 'Lên đầu trang', 'kadence-daquylucyen' ),
	);
}
