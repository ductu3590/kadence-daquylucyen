<?php
/**
 * Bài viết đơn theo bản 1 — ghi đè single-entry.php của Kadence.
 *
 * Thứ tự từ trên xuống, đúng artboard BaiViet:
 *   1. Đầu bài (khổ 800): đường dẫn, nhãn chuyên mục, tiêu đề, đoạn dẫn, dòng
 *      meta kèm nút Lưu và Chia sẻ.
 *   2. Ảnh bìa (khổ 1000, bo 20) — SAU tiêu đề. Người đọc đến vì câu hỏi trong
 *      tiêu đề; ảnh bìa đứng trước đẩy câu trả lời xuống dưới màn hình đầu.
 *   3. Thân bài: cột lề 220 (mục lục dính + thuật ngữ trong bài) và bài 720.
 *      Cuối bài: thẻ, hộp tác giả, bài trước / bài sau.
 *   4. Dải "Đọc tiếp" nền trắng tràn ngang, rồi bình luận.
 *
 * Bố cục khung (tràn ngang, không hộp) được ép ở inc/single.php.
 * Các hook của Kadence vẫn được gọi ở chỗ tương ứng để plugin gắn vào không mất.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

$dql_cat    = dql_post_main_category();
$dql_toc    = dql_toc_is_active();
$dql_tn     = dql_single_glossary_terms( get_the_ID() );
$dql_co_le  = $dql_toc || $dql_tn;
$dql_sapo   = has_excerpt() ? get_the_excerpt() : '';
$dql_kadence = function_exists( '\Kadence\kadence' ) ? \Kadence\kadence() : null;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry single-entry dql-bai' ); ?>>

	<header class="dql-container dql-bai__dau entry-header">
		<div class="dql-bai__dau-in">
			<?php dql_single_breadcrumb( $dql_cat ); ?>

			<?php if ( $dql_cat ) : ?>
				<?php echo dql_category_label( $dql_cat, true, 'dql-bai__chuyen-muc' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- hàm tự thoát. ?>
			<?php endif; ?>

			<?php the_title( '<h1 class="entry-title dql-bai__tieu-de">', '</h1>' ); ?>

			<?php if ( $dql_sapo ) : ?>
				<p class="dql-bai__sapo"><?php echo esc_html( $dql_sapo ); ?></p>
			<?php endif; ?>

			<div class="dql-bai__meta">
				<span class="dql-bai__meta-muc">
					<?php dql_the_icon( 'dong-ho' ); ?>
					<?php dql_the_reading_time(); ?>
				</span>
				<span class="dql-bai__meta-cham" aria-hidden="true">·</span>
				<span class="dql-bai__meta-muc"><?php dql_the_updated(); ?></span>

				<span class="dql-bai__nut">
					<button type="button" class="dql-nut-tron" data-dql-luu="<?php the_ID(); ?>" aria-pressed="false" aria-label="<?php esc_attr_e( 'Lưu bài để đọc sau', 'kadence-daquylucyen' ); ?>">
						<?php dql_the_icon( 'luu' ); ?>
					</button>
					<button type="button" class="dql-nut-tron" data-dql-chia-se aria-label="<?php esc_attr_e( 'Chia sẻ bài viết', 'kadence-daquylucyen' ); ?>">
						<?php dql_the_icon( 'chia-se' ); ?>
					</button>
				</span>
			</div>
		</div>
	</header>

	<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
		<?php $dql_chu_thich = get_the_post_thumbnail_caption(); ?>
		<figure class="dql-container dql-bai__bia">
			<?php
			the_post_thumbnail(
				'full',
				array(
					'class'         => 'dql-bai__bia-img',
					'loading'       => false,
					'fetchpriority' => 'high',
					'sizes'         => '(max-width: 1080px) 100vw, 1000px',
				)
			);
			?>
			<?php if ( $dql_chu_thich ) : ?>
				<figcaption><?php echo wp_kses_post( $dql_chu_thich ); ?></figcaption>
			<?php endif; ?>
		</figure>
		<?php do_action( 'kadence_single_after_featured_image' ); ?>
	<?php endif; ?>

	<div class="dql-container dql-bai__than<?php echo $dql_co_le ? ' dql-bai__than--co-le' : ''; ?>">
		<?php if ( $dql_co_le ) : ?>
			<aside class="dql-bai__le" aria-label="<?php esc_attr_e( 'Điều hướng trong bài', 'kadence-daquylucyen' ); ?>">
				<?php dql_toc_render(); ?>
				<?php dql_single_glossary_box(); ?>
			</aside>
		<?php endif; ?>

		<div class="dql-bai__cot">
			<?php
			do_action( 'kadence_single_before_inner_content' );

			get_template_part( 'template-parts/content/entry_content', get_post_type() );

			dql_single_tags();

			do_action( 'kadence_single_after_inner_content' );

			// Không theo tùy chọn "Author Box" của Kadence (mặc định tắt): hộp
			// tác giả là một phần của thiết kế, không phải phụ kiện bật thêm.
			dql_single_author_box();

			if ( ! $dql_kadence || $dql_kadence->show_post_navigation() ) {
				dql_single_post_nav();
			}
			?>
		</div>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->

<?php
/**
 * Hook sau bài viết (quảng cáo cuối bài của theme gắn ở đây).
 */
do_action( 'kadence_single_after_content' );

if ( ! $dql_kadence || $dql_kadence->option( 'post_related' ) ) {
	get_template_part( 'template-parts/content/entry_related', get_post_type() );
}

if ( $dql_kadence && $dql_kadence->show_comments() ) {
	echo '<div class="dql-container dql-bai__binh-luan">';
	comments_template();
	echo '</div>';
}
