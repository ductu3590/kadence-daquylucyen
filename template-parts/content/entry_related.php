<?php
/**
 * "Đọc tiếp trong <chuyên mục>" ở cuối bài — thay khối "Similar Posts" của Kadence.
 *
 * Kadence gọi file này từ single-entry.php khi bật Customizer → Bài viết →
 * Related Posts, nên bật/tắt vẫn ở chỗ cũ. Child theme chỉ thay giao diện:
 *
 * - Thiết kế bản 1 là dải trắng tràn ngang, tiêu đề "Đọc tiếp trong Đá quý"
 *   kèm "Cả chuyên mục →", ba thẻ tĩnh. Carousel 6 bài của Kadence bắt người
 *   đọc bấm mũi tên mới thấy bài, và tiêu đề tiếng Anh.
 * - Lấy theo chuyên mục CHÍNH của bài (dql_post_main_category), không theo thẻ:
 *   chuyên mục là thứ người đọc vừa đi qua ở breadcrumb.
 * - Chuyên mục chưa đủ bài khác (Du lịch mới có một bài) thì lấy thêm từ
 *   chuyên mục cha, để dải không trống trơn ngay ở mảng mới của site.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

$dql_post = get_post();
$dql_cat  = $dql_post ? dql_post_main_category( $dql_post ) : null;

if ( ! $dql_cat ) {
	return;
}

$dql_ids     = array();
$dql_tieu_de = null;

// Bài có ảnh trước — thẻ không ảnh chỉ là chỗ trống; rồi mới tới bài không ảnh.
foreach ( array_filter( array( $dql_cat, $dql_cat->parent ? get_term( $dql_cat->parent, 'category' ) : null ) ) as $dql_nguon ) {
	foreach ( array( 'EXISTS', 'NOT EXISTS' ) as $dql_anh ) {
		if ( count( $dql_ids ) >= 3 ) {
			break 2;
		}

		$dql_truoc = count( $dql_ids );
		$dql_ids   = array_merge(
			$dql_ids,
			get_posts(
				array(
					'cat'                 => $dql_nguon->term_id,
					'post__not_in'        => array_merge( array( $dql_post->ID ), $dql_ids ),
					'posts_per_page'      => 3 - count( $dql_ids ),
					'ignore_sticky_posts' => true,
					'fields'              => 'ids',
					'meta_query'          => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
						array(
							'key'     => '_thumbnail_id',
							'compare' => $dql_anh,
						),
					),
				)
			)
		);

		// Tiêu đề và link "Cả chuyên mục" theo chuyên mục rộng nhất đã góp bài:
		// có bài lấy từ chuyên mục cha thì ghi tên cha, vì cha chứa cả con.
		if ( count( $dql_ids ) > $dql_truoc ) {
			$dql_tieu_de = $dql_nguon;
		}
	}
}

if ( ! $dql_ids ) {
	return;
}

$dql_q = new WP_Query(
	array(
		'post__in'            => $dql_ids,
		'orderby'             => 'post__in',
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
?>
<section class="dql-doc-tiep alignfull" aria-labelledby="dql-doc-tiep-title">
	<div class="dql-container dql-doc-tiep__in">
		<div class="dql-doc-tiep__head">
			<h2 class="dql-doc-tiep__title" id="dql-doc-tiep-title">
				<?php
				/* translators: %s: tên chuyên mục. */
				echo esc_html( sprintf( __( 'Đọc tiếp trong %s', 'kadence-daquylucyen' ), $dql_tieu_de->name ) );
				?>
			</h2>
			<a class="dql-doc-tiep__all" href="<?php echo esc_url( get_category_link( $dql_tieu_de->term_id ) ); ?>">
				<?php esc_html_e( 'Cả chuyên mục', 'kadence-daquylucyen' ); ?> <span aria-hidden="true">→</span>
			</a>
		</div>

		<div class="dql-cards dql-cards--3">
			<?php
			while ( $dql_q->have_posts() ) :
				$dql_q->the_post();
				?>
				<article <?php post_class( 'dql-card dql-card--doc-tiep' ); ?>>
					<a class="dql-card__link" href="<?php the_permalink(); ?>">
						<span class="dql-card__media dql-hat<?php echo has_post_thumbnail() ? '' : ' dql-card__media--trong'; ?>">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'dql-card', array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
							<?php else : ?>
								<?php dql_the_icon( 'da-quy', array( 'stroke' => 1.2 ) ); ?>
							<?php endif; ?>
						</span>
						<span class="dql-card__body">
							<h3 class="dql-card__title"><?php the_title(); ?></h3>
							<span class="dql-card__meta"><?php dql_the_reading_time(); ?></span>
						</span>
					</a>
				</article>
			<?php endwhile; ?>
		</div>
	</div>
</section>
<?php
wp_reset_postdata();
