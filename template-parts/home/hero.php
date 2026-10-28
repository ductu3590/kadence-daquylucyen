<?php
/**
 * Trang chủ — câu định vị và ô tìm kiếm.
 *
 * Khối này trả lời câu hỏi "đây là chỗ gì". Từ bản 2.0 nó đứng SAU khối bài
 * nổi bật và xếp hai cột: câu định vị bên trái, ô tìm kiếm bên phải — kiểu
 * dải "about" của tạp chí. Không dùng ảnh nền: thứ cần thấy là chữ.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

$dql_kicker = dql_home_option( 'dql_hero_kicker' );
$dql_title  = dql_home_option( 'dql_hero_title' );
$dql_sub    = dql_home_option( 'dql_hero_sub' );
?>

<section class="dql-section dql-hero">
	<div class="dql-container dql-hero__in">
		<div class="dql-hero__chu" data-reveal>
			<?php if ( $dql_kicker ) : ?>
				<p class="dql-kicker"><?php echo esc_html( $dql_kicker ); ?></p>
			<?php endif; ?>

			<?php if ( $dql_title ) : ?>
				<h1 class="dql-hero__title"><?php echo esc_html( $dql_title ); ?></h1>
			<?php endif; ?>

			<?php if ( $dql_sub ) : ?>
				<p class="dql-hero__sub"><?php echo esc_html( $dql_sub ); ?></p>
			<?php endif; ?>
		</div>

		<?php
		/*
		 * Ô tìm kiếm vẫn là một form gửi được bình thường. JavaScript chỉ thêm
		 * phần gợi ý tức thời bên dưới — tắt JS thì bấm "Tìm" vẫn ra trang kết
		 * quả như thường.
		 */
		?>
		<form role="search" method="get" class="dql-search" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-reveal>
			<label class="screen-reader-text" for="dql-search-field">
				<?php esc_html_e( 'Tìm kiếm trên website', 'kadence-daquylucyen' ); ?>
			</label>
			<?php dql_the_icon( 'tim', array( 'class' => 'dql-search__icon' ) ); ?>
			<input
				type="search"
				id="dql-search-field"
				class="dql-search__field"
				placeholder="<?php esc_attr_e( 'Tìm loại đá, mệnh, địa danh, thuật ngữ…', 'kadence-daquylucyen' ); ?>"
				value="<?php echo esc_attr( get_search_query() ); ?>"
				name="s"
			/>
			<button type="submit" class="dql-search__submit dql-nut">
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
