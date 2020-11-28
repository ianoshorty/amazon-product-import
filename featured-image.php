<?php
/**
 * View: List View - Single Event Featured Image
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/list/event/featured-image.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://m.tri.be/1aiy
 *
 * @version 5.0.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

$image_url = get_post_meta($event->ID, 'amazon_product_image_url');

if (empty($image_url) && $event->thumbnail->exists) : ?>

<div class="tribe-events-calendar-list__event-featured-image-wrapper tribe-common-g-col">
	<a
		href="<?php echo esc_url( $event->permalink ); ?>"
		title="<?php echo esc_attr( $event->title ); ?>"
		rel="bookmark"
		class="tribe-events-calendar-list__event-featured-image-link"
	>
		<img
			src="<?php echo esc_url( $event->thumbnail->full->url ); ?>"
			<?php if ( ! empty( $event->thumbnail->srcset ) ) : ?>
				srcset="<?php echo esc_attr( $event->thumbnail->srcset ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $event->thumbnail->alt ) ) : ?>
				alt="<?php echo esc_attr( $event->thumbnail->alt ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $event->thumbnail->title ) ) : ?>
				title="<?php echo esc_attr( $event->thumbnail->title ); ?>"
			<?php endif; ?>
			class="tribe-events-calendar-list__event-featured-image"
		/>
	</a>
</div>

<?php elseif (!empty($image_url)) : ?>

<div class="tribe-events-calendar-list__event-featured-image-wrapper tribe-common-g-col">
    <img
        src="<?php echo esc_url( $image_url[0] ); ?>"
        class="tribe-events-calendar-list__event-featured-image"
    />
</div>

<?php endif; ?>