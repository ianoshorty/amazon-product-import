<?php

/**
 * Provide 
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Amazon_Product_Import
 * @subpackage Amazon_Product_Import/admin/partials
 */
?>

<?php $post = get_post(); ?>

<?php wp_nonce_field( 'amazon_identifier_nonce_file', 'amazon_identifier_post_nonce' ); ?>

<p>
    <label for="amazon-identifier"><?php _e( "Enter the Amazon Product Identifier (e.g. ISBN number)." ); ?></label>
    <br />
    <input class="widefat" type="text" name="amazon_identifier" id="amazon-identifier" value="<?php echo esc_attr( get_post_meta( $post->ID, 'amazon_identifier', true ) ); ?>" size="30" />
</p>