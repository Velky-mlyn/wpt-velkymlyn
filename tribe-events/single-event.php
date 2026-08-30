<?php
get_header();

// Get event data
$event_id = get_the_ID();
$start_date = tribe_get_start_date($event_id, false, 'j. n. Y');
$start_time = tribe_get_start_date($event_id, false, 'H:i');
$event_tags = get_the_terms( $event_id, 'post_tag');
$ticket_link = get_field('ticket_link');
$background_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
$image_focal_point = function_exists( 'mlyn_event_get_image_focal_point' )
	? mlyn_event_get_image_focal_point( $event_id )
	: array( 'x' => 50, 'y' => 50 );
$image_focal_x = max( 0, min( 100, (int) ( $image_focal_point['x'] ?? 50 ) ) );
$image_focal_y = max( 0, min( 100, (int) ( $image_focal_point['y'] ?? 50 ) ) );
$ticket_price = tribe_get_cost( get_the_ID(), true ); // true = include currency symbol
if(!$ticket_price){
	$ticket_price = 'Zdarma';
}
$event_capacity_is_set  = metadata_exists( 'post', $event_id, '_mlyn_event_capacity' );
$event_available_is_set = metadata_exists( 'post', $event_id, '_mlyn_event_available_places' );
$event_capacity         = $event_capacity_is_set ? (int) get_post_meta( $event_id, '_mlyn_event_capacity', true ) : null;
$event_available        = $event_available_is_set ? (int) get_post_meta( $event_id, '_mlyn_event_available_places', true ) : null;
$event_occupancy_note   = (string) get_post_meta( $event_id, '_mlyn_event_occupancy_note', true );

$event_cats = get_the_terms($event_id, 'tribe_events_cat'); // 💡 Kategorie akce

// Výpis kategorií (pokud chceš jako text)
if ( $event_cats && ! is_wp_error($event_cats) ) {
    $event_categories = wp_list_pluck($event_cats, 'name');
    $event_categories_output = implode(', ', $event_categories);
} else {
    $event_categories_output = ''; // nebo třeba 'Nezařazeno'
}

?>

<div class="detail-akce container custom-container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-12 mb-5">
            <div class="uvod pt-3">
            <p class="datum d-inline"><?php echo esc_html($start_date); ?> od <?php echo esc_html($start_time); ?></p>
            <?php if ( ! empty( $event_tags ) && ! is_wp_error( $event_tags ) ) : ?>
                <div class="event-tags d-inline mb-2">
                    <?php foreach ( $event_tags as $tag ) : 
                        // Načti barvu z ACF pole "barva_stitku"
                        $tag_color = get_field( 'barva_stitku', 'term_' . $tag->term_id ); 
                    ?>
                        <div class="event-tag text-uppercase px-1 py-1 d-inline-block me-1 mb-1 <?= esc_attr( $tag->slug ); ?>"
                            style="background-color: <?= esc_attr( $tag_color ); ?>;">
                            <?= esc_html( $tag->name ) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <h1><?php the_title(); ?></h1>
            </div>
            <?php if (has_post_thumbnail()): ?>
                <div class="banner-wrapper col-12 text-center align-items-center d-flex flex-column justify-content-center"
							style="background-image: url('<?php echo esc_url($background_image); ?>'); background-position: <?php echo esc_attr( $image_focal_x . '% ' . $image_focal_y . '%' ); ?>;">
            </div>
            <?php endif; ?>
        </div>
        <div class="col-lg-8 px-5">
            <div class="event-content px-3">
                <?php the_content(); ?>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4 px-5">
            <div class=" mb-4 detail-info">
                <h5 class="mb-3">Informace</h5>
                <p><strong>Datum:</strong> <?php echo esc_html($start_date); ?></p>
                <p><strong>Čas:</strong> <?php echo esc_html($start_time); ?></p>
                <p><strong>Místo:</strong> <?php echo esc_html($event_categories_output); ?></p>
                <p><strong>Vstupné:</strong> <?php echo esc_html($ticket_price); ?></p>
                <?php if ( $event_capacity_is_set ) : ?>
                    <p><strong>Kapacita:</strong> <?php echo esc_html( (string) $event_capacity ); ?></p>
                <?php endif; ?>
                <?php if ( $event_available_is_set ) : ?>
                    <p><strong>Volná místa:</strong> <?php echo 0 === $event_available ? '<span class="event-occupancy-full">Obsazeno</span>' : esc_html( (string) $event_available ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                <?php endif; ?>
                <?php if ( '' !== $event_occupancy_note ) : ?>
                    <p class="event-occupancy-note-detail"><strong>Poznámka:</strong> <span><?php echo nl2br( esc_html( $event_occupancy_note ) ); ?></span></p>
                <?php endif; ?>
                <?php
                    $website = tribe_get_event_website_url( get_the_ID() );
                    if ( $website ) {?>
                <div class="row mt-3 justify-content-between align-items-center">
                    <div class="col-6 text-start"><?php echo '<a class="btn btn-primary btn-vstupenky" href="' . esc_url( $website ) . '" target="_blank" rel="noopener">Vstupenky</a>';?>
                    </div>
                    <div class="col-6 text-end">
                        <img class="img-fluid" src="<?php echo esc_url( get_theme_file_uri( '/image/goout.png' ) ); ?>" alt="">
                    </div>
                </div>
               <?php  } ?>
            </div>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d6725.638313583752!2d14.463104894467111!3d50.10751579340787!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x470beb5542d9634b%3A0x6aa5e96e880d0edb!2zVmVsa8O9IG1sw71uIOKAoiBNxJtzdHNrw6Ega25paG92bmEgdiBQcmF6ZQ!5e0!3m2!1scs!2scz!4v1754938842558!5m2!1scs!2scz" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
    
</div>

<?php get_footer(); ?>
