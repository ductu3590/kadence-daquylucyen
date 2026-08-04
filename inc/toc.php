<?php
/**
 * Mục lục tự động cho bài viết.
 *
 * Cách hoạt động:
 *   1. Quét thẻ H2 và H3 trong nội dung bài, sinh id cho thẻ nào chưa có.
 *   2. Dựng mục lục từ danh sách đó, in ra trước nội dung.
 *   3. CSS đẩy mục lục ra lề trái trên màn hình rộng, thu thành thanh gập trên
 *      màn hình hẹp.
 *
 * Điểm tinh tế: mục lục phải in ra TRƯỚC nội dung, nhưng danh sách tiêu đề chỉ
 * biết được sau khi đọc nội dung. Nên cả hai bước đều gọi chung một hàm quét,
 * chạy trên cùng một chuỗi, theo cùng thứ tự — nhờ vậy id trong mục lục luôn
 * khớp với id trong bài.
 *
 * Giới hạn đã biết: tiêu đề do block động sinh ra lúc render (không nằm sẵn
 * trong nội dung lưu ở database) sẽ không vào mục lục. Site này không dùng
 * loại block đó.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

/**
 * Số tiêu đề tối thiểu để mục lục có ích.
 *
 * Dưới ngưỡng này, mục lục chỉ gây rối chứ không giúp người đọc định vị.
 */
define( 'DQL_TOC_MIN_HEADINGS', 3 );

/**
 * Quét H2/H3, sinh id còn thiếu.
 *
 * @param string      $content     Nội dung cần quét.
 * @param string|null $new_content Nhận lại nội dung đã gắn id.
 * @return array Danh sách tiêu đề, mỗi phần tử gồm level, id, text.
 */
function dql_toc_parse( $content, &$new_content = null ) {
	$headings = array();
	$used     = array();

	$new_content = preg_replace_callback(
		'/<h([23])(\s[^>]*)?>(.*?)<\/h\1>/is',
		function ( $m ) use ( &$headings, &$used ) {
			$level = (int) $m[1];
			$attrs = isset( $m[2] ) ? $m[2] : '';
			$inner = $m[3];
			$text  = trim( wp_strip_all_tags( $inner ) );

			// Tiêu đề rỗng (thường là thẻ trang trí) thì bỏ qua, giữ nguyên.
			if ( '' === $text ) {
				return $m[0];
			}

			if ( preg_match( '/\sid=["\']([^"\']+)["\']/i', $attrs, $found ) ) {
				$id            = $found[1];
				$used[ $id ]   = true;
				$out           = $m[0];
			} else {
				$id  = dql_toc_unique_id( $text, $used );
				$out = '<h' . $level . $attrs . ' id="' . esc_attr( $id ) . '">' . $inner . '</h' . $level . '>';
			}

			$headings[] = array(
				'level' => $level,
				'id'    => $id,
				'text'  => $text,
			);

			return $out;
		},
		$content
	);

	return $headings;
}

/**
 * Sinh id duy nhất từ nội dung tiêu đề.
 *
 * sanitize_title() bỏ dấu tiếng Việt, nên "Phân biệt đá thật" thành
 * "phan-biet-da-that" — đọc được và an toàn khi làm neo trong URL.
 *
 * @param string $text Nội dung tiêu đề.
 * @param array  $used Các id đã dùng, truyền theo tham chiếu.
 * @return string
 */
function dql_toc_unique_id( $text, &$used ) {
	$base = sanitize_title( $text );

	if ( '' === $base ) {
		$base = 'muc';
	}

	$id = $base;
	$i  = 2;

	while ( isset( $used[ $id ] ) ) {
		$id = $base . '-' . $i;
		$i++;
	}

	$used[ $id ] = true;

	return $id;
}

/**
 * Lấy danh sách tiêu đề của bài hiện tại, có nhớ tạm trong một lần tải trang.
 *
 * @param int $post_id ID bài viết.
 * @return array
 */
function dql_toc_headings( $post_id ) {
	static $cache = array();

	if ( isset( $cache[ $post_id ] ) ) {
		return $cache[ $post_id ];
	}

	$post = get_post( $post_id );

	if ( ! $post ) {
		$cache[ $post_id ] = array();
		return $cache[ $post_id ];
	}

	$cache[ $post_id ] = dql_toc_parse( $post->post_content );

	return $cache[ $post_id ];
}

/**
 * Bài này có nên hiện mục lục không.
 *
 * @return bool
 */
function dql_toc_is_active() {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return false;
	}

	return count( dql_toc_headings( get_the_ID() ) ) >= DQL_TOC_MIN_HEADINGS;
}

/**
 * Gắn id vào các thẻ tiêu đề trong nội dung khi hiển thị.
 *
 * @param string $content Nội dung bài.
 * @return string
 */
function dql_toc_add_ids( $content ) {
	if ( ! dql_toc_is_active() ) {
		return $content;
	}

	dql_toc_parse( $content, $with_ids );

	return null === $with_ids ? $content : $with_ids;
}
add_filter( 'the_content', 'dql_toc_add_ids', 20 );

/**
 * In mục lục ra đầu bài viết.
 */
function dql_toc_render() {
	if ( ! dql_toc_is_active() ) {
		return;
	}

	$headings = dql_toc_headings( get_the_ID() );
	?>
	<aside class="dql-toc" aria-labelledby="dql-toc-heading">
		<div class="dql-toc__inner">
			<button
				type="button"
				class="dql-toc__toggle"
				id="dql-toc-heading"
				aria-expanded="true"
				aria-controls="dql-toc-nav"
			>
				<span><?php esc_html_e( 'Mục lục bài viết', 'kadence-daquylucyen' ); ?></span>
				<svg class="dql-toc__chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
					<path d="m6 9 6 6 6-6"/>
				</svg>
			</button>

			<nav id="dql-toc-nav" class="dql-toc__nav">
				<ol class="dql-toc__list">
					<?php foreach ( $headings as $h ) : ?>
						<li class="dql-toc__item dql-toc__item--h<?php echo (int) $h['level']; ?>">
							<a class="dql-toc__link" href="#<?php echo esc_attr( $h['id'] ); ?>">
								<?php echo esc_html( $h['text'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ol>
			</nav>
		</div>
	</aside>
	<?php
}
add_action( 'kadence_single_before_inner_content', 'dql_toc_render' );

/**
 * In thời gian đọc ngay trên nội dung bài.
 */
function dql_single_reading_time() {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return;
	}

	echo '<p class="dql-single-meta">';
	dql_the_reading_time();
	echo '</p>';
}
add_action( 'kadence_single_before_entry_content', 'dql_single_reading_time' );
