<?php
/**
 * Page hero configuration and rendering helpers.
 *
 * @package Velký_mlýn
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const VELKYMLYN_PAGE_HERO_MODE_META   = '_velkymlyn_page_hero_mode';
const VELKYMLYN_PAGE_HERO_SLIDER_META = '_velkymlyn_page_hero_slider_id';

/**
 * Add the page hero controls to Page edit screens.
 */
function velkymlyn_register_page_hero_meta_box() {
	add_meta_box(
		'velkymlyn-page-hero',
		__( 'Page hero', 'velkymlyn' ),
		'velkymlyn_render_page_hero_meta_box',
		'page',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes_page', 'velkymlyn_register_page_hero_meta_box' );

/**
 * Render the page hero controls.
 *
 * @param WP_Post $post Page being edited.
 */
function velkymlyn_render_page_hero_meta_box( $post ) {
	$mode      = 'slider' === get_post_meta( $post->ID, VELKYMLYN_PAGE_HERO_MODE_META, true ) ? 'slider' : 'image';
	$slider_id = absint( get_post_meta( $post->ID, VELKYMLYN_PAGE_HERO_SLIDER_META, true ) );
	$sliders   = post_type_exists( 'mlyn_slider' ) ? get_posts(
		array(
			'post_type'      => 'mlyn_slider',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	) : array();
	$slider_ids = wp_list_pluck( $sliders, 'ID' );

	wp_nonce_field( 'velkymlyn_save_page_hero', 'velkymlyn_page_hero_nonce' );
	?>
	<p>
		<label>
			<input type="radio" name="velkymlyn_page_hero_mode" value="image" <?php checked( $mode, 'image' ); ?>>
			<?php esc_html_e( 'Use image', 'velkymlyn' ); ?>
		</label><br>
		<span class="description"><?php esc_html_e( 'Uses the page Featured Image, then the global theme image.', 'velkymlyn' ); ?></span>
	</p>
	<p>
		<label>
			<input type="radio" name="velkymlyn_page_hero_mode" value="slider" <?php checked( $mode, 'slider' ); ?>>
			<?php esc_html_e( 'Use flexible slider', 'velkymlyn' ); ?>
		</label>
	</p>
	<div id="velkymlyn-page-hero-slider-field"<?php echo 'slider' === $mode ? '' : ' hidden'; ?>>
		<label for="velkymlyn-page-hero-slider"><strong><?php esc_html_e( 'Slider', 'velkymlyn' ); ?></strong></label>
		<select id="velkymlyn-page-hero-slider" name="velkymlyn_page_hero_slider_id" class="widefat">
			<option value="0"><?php esc_html_e( 'Select a published slider', 'velkymlyn' ); ?></option>
			<?php if ( $slider_id && ! in_array( $slider_id, $slider_ids, true ) ) : ?>
				<option value="<?php echo esc_attr( (string) $slider_id ); ?>" selected><?php echo esc_html( sprintf( __( 'Unavailable slider (#%d)', 'velkymlyn' ), $slider_id ) ); ?></option>
			<?php endif; ?>
			<?php foreach ( $sliders as $slider ) : ?>
				<option value="<?php echo esc_attr( (string) $slider->ID ); ?>" <?php selected( $slider_id, $slider->ID ); ?>><?php echo esc_html( $slider->post_title ); ?></option>
			<?php endforeach; ?>
		</select>

		<?php if ( ! post_type_exists( 'mlyn_slider' ) ) : ?>
			<p class="description"><?php esc_html_e( 'The Mlýn Flexible Slider plugin is currently unavailable. The image fallback will be used.', 'velkymlyn' ); ?></p>
		<?php elseif ( ! $sliders ) : ?>
			<p class="description"><?php esc_html_e( 'No published sliders are available. The image fallback will be used.', 'velkymlyn' ); ?></p>
		<?php endif; ?>

		<?php if ( post_type_exists( 'mlyn_slider' ) ) : ?>
			<p>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=mlyn_slider' ) ); ?>"><?php esc_html_e( 'Manage sliders', 'velkymlyn' ); ?></a>
				<?php if ( $slider_id && get_post( $slider_id ) && current_user_can( 'edit_post', $slider_id ) ) : ?>
					<span aria-hidden="true"> · </span><a href="<?php echo esc_url( get_edit_post_link( $slider_id ) ); ?>"><?php esc_html_e( 'Edit selected slider', 'velkymlyn' ); ?></a>
				<?php endif; ?>
			</p>
		<?php endif; ?>
	</div>
	<p class="description"><?php esc_html_e( 'If the selected slider cannot be displayed, the image fallback is used automatically.', 'velkymlyn' ); ?></p>
	<?php
}

/**
 * Save page hero settings.
 *
 * @param int $post_id Page ID.
 */
function velkymlyn_save_page_hero( $post_id ) {
	if ( ! isset( $_POST['velkymlyn_page_hero_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['velkymlyn_page_hero_nonce'] ) ), 'velkymlyn_save_page_hero' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$mode      = isset( $_POST['velkymlyn_page_hero_mode'] ) && 'slider' === sanitize_key( wp_unslash( $_POST['velkymlyn_page_hero_mode'] ) ) ? 'slider' : 'image';
	$slider_id = isset( $_POST['velkymlyn_page_hero_slider_id'] ) ? absint( wp_unslash( $_POST['velkymlyn_page_hero_slider_id'] ) ) : 0;
	$slider    = $slider_id ? get_post( $slider_id ) : null;

	if ( $slider && 'mlyn_slider' === $slider->post_type ) {
		update_post_meta( $post_id, VELKYMLYN_PAGE_HERO_SLIDER_META, $slider_id );
	} elseif ( 'slider' === $mode ) {
		delete_post_meta( $post_id, VELKYMLYN_PAGE_HERO_SLIDER_META );
	}

	update_post_meta( $post_id, VELKYMLYN_PAGE_HERO_MODE_META, $mode );
}
add_action( 'save_post_page', 'velkymlyn_save_page_hero' );

/**
 * Enqueue the Page hero admin interaction only on Page edit screens.
 *
 * @param string $hook Current admin page hook.
 */
function velkymlyn_enqueue_page_hero_admin_script( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || 'page' !== $screen->post_type ) {
		return;
	}

	$script_path    = get_theme_file_path( '/js/page-hero-admin.js' );
	$script_version = file_exists( $script_path ) ? filemtime( $script_path ) : VELKYMLYN_VERSION;
	wp_enqueue_script( 'velkymlyn-page-hero-admin', get_theme_file_uri( '/js/page-hero-admin.js' ), array(), $script_version, true );
}
add_action( 'admin_enqueue_scripts', 'velkymlyn_enqueue_page_hero_admin_script' );

/**
 * Resolve the media used by a page hero.
 *
 * @param int $post_id Page ID.
 * @return array{type:string,source:string,html:string,image_url:string}
 */
function velkymlyn_get_page_hero_data( $post_id ) {
	$data = array(
		'type'      => 'none',
		'source'    => 'none',
		'html'      => '',
		'image_url' => '',
	);
	$mode = get_post_meta( $post_id, VELKYMLYN_PAGE_HERO_MODE_META, true );

	if ( 'slider' === $mode ) {
		$slider_id = absint( get_post_meta( $post_id, VELKYMLYN_PAGE_HERO_SLIDER_META, true ) );
		if ( $slider_id && function_exists( 'mlyn_render_slider' ) ) {
			$slider_html = mlyn_render_slider(
				$slider_id,
				array(
					'variant' => 'hero',
					'class'   => 'velkymlyn-page-hero__slider',
				)
			);
			if ( '' !== trim( $slider_html ) ) {
				$data['type']   = 'slider';
				$data['source'] = 'slider';
				$data['html']   = $slider_html;
				return $data;
			}
		}
	}

	$featured_image = get_the_post_thumbnail_url( $post_id, 'full' );
	if ( $featured_image ) {
		$data['type']      = 'image';
		$data['source']    = 'featured-image';
		$data['image_url'] = $featured_image;
		return $data;
	}

	$custom_header_image = get_header_image();
	if ( $custom_header_image ) {
		$data['type']      = 'image';
		$data['source']    = 'custom-header';
		$data['image_url'] = $custom_header_image;
		return $data;
	}

	$default_image_path = get_theme_file_path( '/image/ptb.jpeg' );
	if ( is_readable( $default_image_path ) ) {
		$data['type']      = 'image';
		$data['source']    = 'theme-default';
		$data['image_url'] = get_theme_file_uri( '/image/ptb.jpeg' );
	}

	return $data;
}
