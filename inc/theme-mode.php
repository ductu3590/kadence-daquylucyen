<?php
/**
 * Chế độ tối (Dark Mode).
 *
 * Theo Apple HIG: giao diện phải theo lựa chọn của hệ điều hành, và cho người
 * dùng ghi đè. Cách làm:
 *
 *   1. Một đoạn script RẤT NGẮN in ở <head>, trước mọi CSS: đọc lựa chọn đã lưu
 *      trong localStorage, không có thì đọc prefers-color-scheme, rồi gắn
 *      data-dql-theme="dark|light" lên thẻ <html>. Chạy trước khi trang vẽ nên
 *      không có nhịp nhấp nháy từ sáng sang tối.
 *   2. CSS trong assets/css/dark.css đặt lại các biến --global-palette* của
 *      Kadence trong phạm vi html[data-dql-theme="dark"]. Mọi thành phần đã
 *      dùng biến thì tự đổi — đó là lý do theme này không bao giờ viết mã màu
 *      trực tiếp.
 *   3. Nút bấm đổi chế độ: shortcode [dql_doi_nen] để đặt vào phần tử HTML
 *      của header builder Kadence. Nếu header không có nút nào, assets/js/ui.js
 *      tự chèn một nút vào góc phải header.
 *
 * Tắt toàn bộ tính năng ở Customizer → Giao diện Đá Thô → "Chế độ tối".
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

/**
 * Chế độ tối có được bật không.
 *
 * @return bool
 */
function dql_dark_mode_on() {
	return 'off' !== get_theme_mod( 'dql_dark_mode', 'auto' );
}

/**
 * Script gắn chế độ lên <html> trước khi trang vẽ.
 *
 * In ở wp_head với ưu tiên 0 để đứng trước mọi <link rel="stylesheet">.
 * Không có JS thì dql_dark_mode_noscript() lo phần theo hệ điều hành.
 */
function dql_dark_mode_head_script() {
	if ( ! dql_dark_mode_on() ) {
		return;
	}
	?>
	<meta name="color-scheme" content="light dark">
	<script id="dql-theme-init" data-no-optimize="1" data-no-defer="1" data-cfasync="false">
	( function () {
		try {
			var s = localStorage.getItem( 'dql-theme' );
			var d = s ? s === 'dark' : window.matchMedia( '(prefers-color-scheme: dark)' ).matches;
			document.documentElement.setAttribute( 'data-dql-theme', d ? 'dark' : 'light' );
			if ( ! s ) { document.documentElement.setAttribute( 'data-dql-theme-auto', '' ); }
		} catch ( e ) {}
	} )();
	</script>
	<?php
}

/**
 * Chế độ tối khi trình duyệt tắt JavaScript.
 *
 * Không có script ở trên thì <html> không bao giờ có data-dql-theme, và toàn bộ
 * dark.css nằm im. Bản trong <noscript> là chính dark.css với tiền tố
 * html[data-dql-theme="dark"] đổi thành html:not([data-dql-theme]) — cùng độ
 * ưu tiên nên vẫn thắng :root của Kadence — gói trong media query của hệ điều
 * hành. Một nguồn CSS duy nhất, sửa dark.css là bản này tự đi theo. In ở cuối
 * <head> để đứng sau các stylesheet như dark.css thật. Trình duyệt bật JS bỏ
 * qua nội dung <noscript>.
 */
function dql_dark_mode_noscript() {
	if ( ! dql_dark_mode_on() ) {
		return;
	}

	$css = file_get_contents( DQL_DIR . '/assets/css/dark.css' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Tệp cục bộ của theme.

	if ( ! $css ) {
		return;
	}

	$css = str_replace( 'html[data-dql-theme="dark"]', 'html:not([data-dql-theme])', $css );
	$css = preg_replace( '#/\*.*?\*/#s', '', $css );
	$css = preg_replace( '/\s+/', ' ', $css );

	echo '<noscript><style id="dql-dark-nojs" media="(prefers-color-scheme: dark)">' . trim( $css ) . '</style></noscript>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS tĩnh của theme.
}
add_action( 'wp_head', 'dql_dark_mode_noscript', 999 );
add_action( 'wp_head', 'dql_dark_mode_head_script', 0 );

/**
 * Miễn cho script chế độ tối khỏi bị LiteSpeed hoãn.
 *
 * LiteSpeed Cache trên production bật "Load JS Deferred: Delayed": mọi script,
 * kể cả script nội tuyến, bị đổi thành type="litespeed/javascript" và chỉ chạy
 * khi người đọc chạm hay cuộn lần đầu. Script ở trên mà bị hoãn thì trang luôn
 * hiện sáng rồi mới nhảy sang tối khi chạm. Các thuộc tính data-no-optimize,
 * data-no-defer và data-cfasync="false" trên thẻ script đưa nó ra ngoài mọi tối
 * ưu JS của LiteSpeed (và Rocket Loader của Cloudflare).
 *
 * ui.js cùng biến dqlUi của nó cũng được miễn hoãn: nút đổi chế độ nằm ngay
 * trên header điện thoại, và lần chạm đầu vào nút không được phép mất đi chỉ
 * vì script chưa nạp. Guest Mode của LiteSpeed (đang bật trên production) đọc
 * danh sách loại trừ qua một filter khác, nên gắn vào cả hai.
 *
 * @param string[] $loai_tru Các chuỗi mà script khớp thì không bị hoãn.
 * @return string[]
 */
function dql_litespeed_khong_hoan( $loai_tru ) {
	$loai_tru   = is_array( $loai_tru ) ? $loai_tru : array();
	$loai_tru[] = '/assets/js/ui.js';
	$loai_tru[] = 'var dqlUi';
	return $loai_tru;
}
add_filter( 'litespeed_optm_js_defer_exc', 'dql_litespeed_khong_hoan' );
add_filter( 'litespeed_optm_gm_js_exc', 'dql_litespeed_khong_hoan' );

/**
 * Nút đổi chế độ sáng/tối.
 *
 * Một nút duy nhất, hai icon chồng nhau; CSS hiện icon đúng với chế độ hiện
 * tại. aria-pressed cho trình đọc màn hình biết trạng thái. Đích bấm 44×44
 * theo HIG dù icon chỉ 20px.
 *
 * @return string
 */
function dql_dark_mode_button() {
	if ( ! dql_dark_mode_on() ) {
		return '';
	}

	return sprintf(
		'<button type="button" class="dql-doi-nen" aria-label="%1$s" aria-pressed="false" data-dql-doi-nen>%2$s%3$s</button>',
		esc_attr__( 'Đổi chế độ sáng / tối', 'kadence-daquylucyen' ),
		dql_icon( 'toi', array( 'class' => 'dql-doi-nen__toi' ) ),
		dql_icon( 'sang', array( 'class' => 'dql-doi-nen__sang' ) )
	);
}
add_shortcode( 'dql_doi_nen', 'dql_dark_mode_button' );

/**
 * Tùy chọn Customizer cho giao diện Đá Thô (chế độ tối, dải chuyên mục, hạt đá).
 *
 * @param WP_Customize_Manager $wp_customize Đối tượng Customizer.
 */
function dql_theme_mode_customize( $wp_customize ) {
	$wp_customize->add_section(
		'dql_giao_dien',
		array(
			'title'       => __( 'Giao diện Đá Thô', 'kadence-daquylucyen' ),
			'priority'    => 24,
			'description' => __( 'Các tùy chọn của ngôn ngữ thiết kế Đá Thô: chế độ tối, dải chuyên mục dưới header, kết cấu hạt đá.', 'kadence-daquylucyen' ),
		)
	);

	$wp_customize->add_setting(
		'dql_dark_mode',
		array(
			'default'           => 'auto',
			'sanitize_callback' => function ( $v ) {
				return in_array( $v, array( 'auto', 'off' ), true ) ? $v : 'auto';
			},
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'dql_dark_mode',
		array(
			'label'       => __( 'Chế độ tối', 'kadence-daquylucyen' ),
			'description' => __( '"Theo hệ điều hành" là mặc định: người đọc đang để máy ở chế độ tối thì site tối, và vẫn có nút đổi tay trên header. Chọn "Tắt" để site luôn sáng.', 'kadence-daquylucyen' ),
			'section'     => 'dql_giao_dien',
			'type'        => 'select',
			'choices'     => array(
				'auto' => __( 'Theo hệ điều hành, có nút đổi', 'kadence-daquylucyen' ),
				'off'  => __( 'Tắt — luôn sáng', 'kadence-daquylucyen' ),
			),
			'priority'    => 10,
		)
	);

	$wp_customize->add_setting(
		'dql_strip',
		array(
			'default'           => 'all',
			'sanitize_callback' => function ( $v ) {
				return in_array( $v, array( 'all', 'home', 'off' ), true ) ? $v : 'all';
			},
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'dql_strip',
		array(
			'label'       => __( 'Dải chuyên mục dưới header', 'kadence-daquylucyen' ),
			'description' => __( 'Hàng chip có icon dẫn tới các chuyên mục cha. Đây là nơi người đọc nhận ra site viết về cả Lục Yên, không chỉ đá quý.', 'kadence-daquylucyen' ),
			'section'     => 'dql_giao_dien',
			'type'        => 'select',
			'choices'     => array(
				'all'  => __( 'Mọi trang', 'kadence-daquylucyen' ),
				'home' => __( 'Chỉ trang chủ', 'kadence-daquylucyen' ),
				'off'  => __( 'Tắt', 'kadence-daquylucyen' ),
			),
			'priority'    => 20,
		)
	);

	$wp_customize->add_setting(
		'dql_grain',
		array(
			'default'           => true,
			'sanitize_callback' => 'wp_validate_boolean',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'dql_grain',
		array(
			'label'       => __( 'Kết cấu hạt đá trên nền', 'kadence-daquylucyen' ),
			'description' => __( 'Lớp nhiễu rất nhẹ (6%) phủ lên nền trang và các dải tối, gợi bề mặt đá chưa mài. Tắt nếu muốn nền phẳng hoàn toàn.', 'kadence-daquylucyen' ),
			'section'     => 'dql_giao_dien',
			'type'        => 'checkbox',
			'priority'    => 30,
		)
	);
}
add_action( 'customize_register', 'dql_theme_mode_customize' );

/**
 * Class trên <body> cho các tùy chọn giao diện, để CSS bật/tắt theo.
 *
 * @param string[] $classes Class hiện có.
 * @return string[]
 */
function dql_theme_mode_body_class( $classes ) {
	if ( get_theme_mod( 'dql_grain', true ) ) {
		$classes[] = 'dql-hat-da';
	}

	if ( dql_dark_mode_on() ) {
		$classes[] = 'dql-co-che-do-toi';

		if ( dql_dark_logo_url() ) {
			$classes[] = 'dql-co-logo-toi';
		}
	}

	return $classes;
}
add_filter( 'body_class', 'dql_theme_mode_body_class' );

/**
 * Địa chỉ logo dùng cho nền tối.
 *
 * Logo header là bản màu cho nền sáng (chữ "Đá Quý" đen), đặt lên nền than đá
 * thì chữ biến mất. Bản chữ trắng đã có sẵn ở ô "Logo footer" trong Customizer,
 * nên dùng lại chính ô đó thay vì thêm một ô thứ hai phải nhớ điền.
 *
 * @return string URL, hoặc chuỗi rỗng nếu chưa chọn logo footer.
 */
function dql_dark_logo_url() {
	$id = (int) get_theme_mod( 'dql_footer_logo', 0 );

	if ( ! $id ) {
		return '';
	}

	$url = wp_get_attachment_image_url( $id, 'full' );

	return $url ? $url : '';
}

/**
 * Đưa URL logo tối cho dark.css qua biến CSS.
 *
 * CSS tĩnh không biết ảnh nào được chọn trong Customizer, nên địa chỉ đi qua
 * một biến trên :root. In ở wp_head ưu tiên 0, trước các stylesheet.
 */
function dql_dark_logo_var() {
	$url = dql_dark_mode_on() ? dql_dark_logo_url() : '';

	if ( ! $url ) {
		return;
	}

	printf( "<style id=\"dql-logo-toi\">:root{--dql-logo-toi:url(\"%s\")}</style>
", esc_url( $url ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Đã qua esc_url().
}
add_action( 'wp_head', 'dql_dark_logo_var', 0 );
