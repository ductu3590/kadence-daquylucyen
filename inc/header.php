<?php
/**
 * Dải chuyên mục dưới header, và vài thứ cắm vào header của Kadence.
 *
 * Dải "Khám phá": một hàng chip có huy hiệu đá, mỗi chip là một chuyên mục
 * cha. Lấy cảm hứng từ thanh "trending" của các theme tạp chí, nhưng ở đây dẫn
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
 * Các chuyên mục hiện trên dải.
 *
 * Chuyên mục cha có bài, bỏ "Chưa phân loại", xếp theo số bài giảm dần. Tối đa
 * 10 — nhiều hơn thì dải cuộn ngang, và một dải cuộn quá dài thành vô nghĩa.
 *
 * @return WP_Term[]
 */
function dql_strip_terms() {
	static $cache = null;

	if ( null !== $cache ) {
		return $cache;
	}

	$mac_dinh = (int) get_option( 'default_category' );
	$tops     = get_categories(
		array(
			'parent'     => 0,
			'hide_empty' => false,
			'exclude'    => array( $mac_dinh ),
		)
	);

	$co_bai = array();

	foreach ( $tops as $t ) {
		$n = dql_category_deep_count( $t );

		if ( $n ) {
			$co_bai[] = array( $t, $n );
		}
	}

	usort(
		$co_bai,
		function ( $a, $b ) {
			return $b[1] - $a[1];
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

	if ( is_category() ) {
		$obj = get_queried_object();

		if ( $obj && ! is_wp_error( $obj ) ) {
			// Đang ở chuyên mục con thì tô sáng chuyên mục cha gốc của nó.
			$to_tien = get_ancestors( $obj->term_id, 'category' );
			$current = $to_tien ? (int) end( $to_tien ) : (int) $obj->term_id;
		}
	} elseif ( is_singular( 'post' ) ) {
		$chinh = dql_post_main_category();

		if ( $chinh ) {
			$to_tien = get_ancestors( $chinh->term_id, 'category' );
			$current = $to_tien ? (int) end( $to_tien ) : (int) $chinh->term_id;
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
					<span><?php echo esc_html( $t->name ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</nav>
	<?php
}
add_action( 'kadence_after_header', 'dql_strip_render', 5 );

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
