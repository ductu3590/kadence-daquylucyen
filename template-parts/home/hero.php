<?php
/**
 * Trang chủ — câu định vị và ô tìm kiếm.
 *
 * Khối này trả lời câu hỏi "đây là chỗ gì" trong khoảng hai giây đầu.
 * Không dùng ảnh nền: ảnh nền lớn làm chậm chỉ số hiển thị nội dung chính,
 * mà ở đây thứ cần thấy trước là chữ.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

$dql_title = dql_home_option( 'dql_hero_title' );
$dql_sub   = dql_home_option( 'dql_hero_sub' );
?>

<section class="dql-section dql-hero">
	<div class="dql-container dql-container--narrow">
		<?php if ( $dql_title ) : ?>
			<h1 class="dql-hero__title"><?php echo esc_html( $dql_title ); ?></h1>
		<?php endif; ?>

		<?php if ( $dql_sub ) : ?>
			<p class="dql-hero__sub"><?php echo esc_html( $dql_sub ); ?></p>
		<?php endif; ?>

		<?php
		/*
		 * Ô tìm kiếm vẫn là một form gửi được bình thường. JavaScript chỉ thêm
		 * phần gợi ý tức thời bên dưới — tắt JS thì bấm "Tìm" vẫn ra trang kết
		 * quả như thường.
		 */
		?>
		<form role="search" method="get" class="dql-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="dql-search-field">
				<?php esc_html_e( 'Tìm kiếm trên website', 'kadence-daquylucyen' ); ?>
			</label>
			<svg class="dql-search__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true" focusable="false">
				<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
			</svg>
			<input
				type="search"
				id="dql-search-field"
				class="dql-search__field"
				placeholder="<?php esc_attr_e( 'Tìm loại đá, mệnh, thuật ngữ…', 'kadence-daquylucyen' ); ?>"
				value="<?php echo esc_attr( get_search_query() ); ?>"
				name="s"
			/>
			<button type="submit" class="dql-search__submit">
				<?php esc_html_e( 'Tìm', 'kadence-daquylucyen' ); ?>
			</button>

			<div
				id="dql-search-goi-y"
				class="dql-goi-y"
				role="listbox"
				aria-label="<?php esc_attr_e( 'Gợi ý tìm kiếm', 'kadence-daquylucyen' ); ?>"
				hidden
			></div>
		</form>
	</div>
</section>
