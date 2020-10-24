<?php

/**
 * Provide an meta box on Events Calendar Events to show the Amazon Product Image URL for
 * this product - which can be used in the body source (or with a featured image).
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/ianoshorty/amazon-product-import
 * @since      1.0.0
 *
 * @package    Amazon_Product_Import
 * @subpackage Amazon_Product_Import/admin/partials
 */
?>

<?php $post = get_post(); ?>
<?php $url = esc_attr( get_post_meta( $post->ID, 'amazon_product_image_url', true ) ); ?>
<?php if (empty($url)): $url = 'Not Imported Yet'; endif; ?>
<p>
    <label for="amazon-identifier"><?php _e( "Amazon Product Image URL:" ); ?></label>
    <br />
    <strong><?php echo $url; ?></strong>
</p>