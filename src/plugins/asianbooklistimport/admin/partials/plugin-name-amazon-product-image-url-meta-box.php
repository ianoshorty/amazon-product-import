<?php

/**
 * Provide 
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin/partials
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