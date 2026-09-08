<?php
  $event_id = $event->ID;
		$occupancy = velkymlyn_get_event_occupancy( $event_id );
		$event_schedule = velkymlyn_get_event_schedule_html( $event, ! $occupancy['fully_occupied'], $occupancy['fully_occupied'] );
        $event_title = get_the_title($event_id);
		$event_excerpt = wp_trim_words( get_the_excerpt( $event_id ), 20, '...' );
        $event_excerpt_mobile = wp_trim_words( get_the_excerpt( $event_id ), 5, '...' );

		$event_image = get_the_post_thumbnail( $event_id, 'three-two', array( 'class' => 'img-fluid' ) );
		if (!$event_image) {
			$event_image = sprintf( '<img width="600" height="400" src="%s" class="img-fluid wp-post-image" alt="" decoding="async" fetchpriority="high">', esc_url( get_theme_file_uri( '/image/placeholder.jpg' ) ) );
		}
        $event_link = get_permalink($event_id);
        $event_location = velkymlyn_get_event_location_html( $event_id );
			$event_tags = get_the_terms( $event_id, 'post_tag');
	        ?>

	        <div class="event-card d-flex flex-wrap align-items-center<?php echo $occupancy['fully_occupied'] ? ' event-card--fully-occupied' : ''; ?>">
			<div class="row align-items-center justify-content-center">
				<div class="event-date text-start col-lg-2 col-12">
					<?php echo $event_schedule; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>

					<?php if ( $occupancy['fully_occupied'] ) : ?>
						<div class="event-occupancy-note col-lg-3 col-12"><?php echo nl2br( esc_html( $occupancy['note'] ) ); ?></div>
					<?php else : ?>
						<div class="event-image col-lg-3 col-12">
							<?= $event_image ?>
						</div>
					<?php endif; ?>

            <div class="event-content col-lg-7 col-12" >

                <?php if ( ! $occupancy['fully_occupied'] ) : ?>
                <div class="event-card-meta mb-2">
                    <?php echo $event_location; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php if ( ! empty( $event_tags ) && ! is_wp_error( $event_tags ) ) : ?>
                <div class="event-tags">
                    <?php foreach ( $event_tags as $tag ) : 
                        // Načti barvu z ACF (pole pojmenuj např. "tag_color")
                        $tag_color = get_field( 'barva_stitku', 'term_' . $tag->term_id ); 
                    ?>
                        <div class="event-tag text-uppercase px-2 py-1 d-inline-block me-1 mb-1 <?= esc_attr( $tag->slug ); ?>"
                            style="background-color: <?= esc_attr( $tag_color ); ?>;">
                            <?= esc_html( $tag->name ) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="event-card-heading">
                <h3 class="event-title d-block mb-1">
                    <a href="<?= esc_url($event_link) ?>">
                        <?= esc_html($event_title) ?>
                    </a>
                </h3>
                    <?php if ( $occupancy['fully_occupied'] ) : ?>
                        <?php echo $event_location; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php endif; ?>
                </div>

	                <?php if ( ! $occupancy['fully_occupied'] ) : ?>
	                    <div class="d-md-none event-description text-muted">
	                        <?= esc_html($event_excerpt_mobile) ?>
	                    </div>
	                    <div class="d-none d-lg-flex event-description text-muted">
	                        <?= esc_html($event_excerpt) ?>
	                    </div>
	                <?php endif; ?>
            </div>

			</div>
            
        </div>
