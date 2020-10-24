<?php

/**
 * Provide an admin area view for the plugin to allow users to press a button to import data from
 * the amazon product SDK by their amazon product identifier.
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

<h1>Amazon Product Import</h1>

<p>This plugin allows you to automatically update products imported from the Amazon Product Listing APIs.</p>

<p>The plugin only updates content that has an ISBN stored with it.</p>

<p><strong>Please note: this plugin may take a long time to execute depending on how many products need to be imported.</strong></p>

<hr />

<p>To trigger a manual update, click the button below.</p>

<form action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
<input type="hidden" name="action" value="amazon_product_import_triggered">
<?php
    wp_nonce_field( 'amazon_product_import' );
    submit_button('Import Products');
?>

<?php if (isset($_GET['success']) && $_GET['success'] === 'true') : ?>
    <p><strong><i>Import successful!</i></strong></p>
<?php endif ?>

</form>