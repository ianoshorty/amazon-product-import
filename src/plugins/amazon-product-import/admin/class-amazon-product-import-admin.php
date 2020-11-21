<?php

use Amazon\ProductAdvertisingAPI\v1\ApiException;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\api\DefaultApi;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\PartnerType;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\ProductAdvertisingAPIClientException;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsRequest;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsRequest;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsResource;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsResource;
use Amazon\ProductAdvertisingAPI\v1\Configuration;

require_once(__DIR__ .'../../vendor/autoload.php'); 

/**
 * The admin-specific functionality of the plugin
 *
 * @link       https://github.com/ianoshorty/amazon-product-import
 * @since      1.0.0
 *
 * @package    Amazon_Product_Import
 * @subpackage Amazon_Product_Import/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * add meta boxes and handle request to fetch from the Product SDK and
 * update events.
 *
 * @package    Amazon_Product_Import
 * @subpackage Amazon_Product_Import/admin
 * @author     Ian Outterside
 */
class Amazon_Product_Import_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the "Amazon Product Import" menu item into the admin navigation menu
	 *
	 * @since    1.0.0
	 */
	public function register_import_settings_page() {
		add_menu_page(
			__( 'Amazon Product Import', 'textdomain' ),
			'Amazon Product Import',
			'manage_options',
			__DIR__ . '/partials/amazon-product-import-admin-display.php',
			'',
			'dashicons-download',
			6
		);
	}

	/**
	 * Add meta boxes to The Events Calendar pages for events (tribe_events post type)
	 * to allow admins to enter an Amazon Product Identifier (or ISBN) and save it 
	 * with the event. Also adds a meta box to display the image URL for this 
	 * product from Amazon.
	 *
	 * @since    1.0.0
	 */
	public function amazon_identifier_add_meta_box() {
		add_meta_box(
			'amazon-identifier-post-class',      						// Unique ID
			esc_html__( 'Amazon Identifier', 'amazon_identifier' ),    // Title
			array( $this, 'amazon_identifier_meta_box' ),   // Callback function
			'tribe_events',         						// Admin page (or post type)
			'side',         								// Context
			'default'       								// Priority
		);

		add_meta_box(
			'amazon-product-image-url-post-class',      						// Unique ID
			esc_html__( 'Amazon Product Image URL', 'amazon_product_image_url' ),    // Title
			array( $this, 'amazon_product_image_url_meta_box' ),   // Callback function
			'tribe_events',         						// Admin page (or post type)
			'side',         								// Context
			'default'       								// Priority
		);
	}

	/**
	 * Method to handle saving of an Amazon Product Identifier (or ISBN) alongside a "trive-event"
	 * (The Events Calendar - Event Post Type).
	 *
	 * @since    1.0.0
	 * @param      int    		$post_id       	The ID of the post in the database
	 */
	public function amazon_identifier_save_post_meta( $post_id ) {

		/* Verify the nonce before proceeding. */
		if ( !isset( $_POST['amazon_identifier_post_nonce'] ) ||
		 	 !wp_verify_nonce( $_POST['amazon_identifier_post_nonce'], 'amazon_identifier_nonce_file' ) ) {
			return $post_id;
		}

		/* Get the posted data and sanitize it for use as an HTML class. */
		$new_meta_value = ( isset( $_POST['amazon_identifier'] ) ? trim( sanitize_html_class( $_POST['amazon_identifier'] )) : '' );

		/* Get the meta key. */
		$meta_key = 'amazon_identifier';

		/* Get the meta value of the custom field key. */
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		/* If a new meta value was added and there was no previous value, add it. */
		if ( !empty( $new_meta_value ) && empty( $meta_value ) ) {
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );
		}

		/* If the new meta value does not match the old value, update it. */
		elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
			update_post_meta( $post_id, $meta_key, $new_meta_value );
		}

		/* If there is no new meta value but an old value exists, delete it. */
		elseif ( empty( $new_meta_value ) && !empty( $meta_value ) ) {
			delete_post_meta( $post_id, $meta_key, $meta_value );
		}
	}

	/**
	 * Load the PHP template for the product identifier (ISBN) meta box
	 *
	 * @since    1.0.0
	 */
	public function amazon_identifier_meta_box() {
		require __DIR__ . '/partials/amazon-product-import-amazon-identifier-meta-box.php';
	}

	/**
	 * Load the PHP template for the product image URL meta box
	 *
	 * @since    1.0.0
	 */
	public function amazon_product_image_url_meta_box() {
		require __DIR__ . '/partials/amazon-product-import-amazon-product-image-url-meta-box.php';
	}

	/**
	 * Method to import products (called when pressing the "Import Products" button in the admin)
	 *
	 * @since    1.0.0
	 */
	public function import_products() {

		// Make sure this was triggered by a real person
		if ( ! check_admin_referer( 'amazon_product_import') ) {
			wp_die( 
				__( 'Invalid nonce specified', $this->plugin_name ), 
				__( 'Error', $this->plugin_name ), 
				[
					'response' 	=> 403,
					'back_link' => 'admin.php?page=' . $this->plugin_name,
				]
			);
			exit;
		}

		// Get a list of all the product IDs that we want to update
		$products = $this->collect_product_identifiers();

		// If we have some IDs
		if ( !empty($products) ) {

			// Lets fetch the updates from Amazon via the SDK
			$updates = $this->fetch_items($products);

			if ( !empty($updates) ) {
				// Now lets update our DB with the new information
				$this->update_items($updates);
			}
		}
		
		// Finally lets throw up a confirmation box to the user and redirect
		wp_redirect( admin_url( 'admin.php?page=amazon-product-import%2Fadmin%2Fpartials%2Famazon-product-import-admin-display.php&success=true' ) );
        exit;
	}

	/**
	 * Method to pull all the product identifiers that need updating from the database
	 *
	 * @since    1.0.0
	 */
	protected function collect_product_identifiers() {

		// Get all the products stored as Events in tribe
		// with their associated identifiers (ISBN etc)
		$args = [
			'post_type'=> 'tribe_events',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'order'    => 'ASC',
			'fields'	=> 'ids',
			'meta_query' => [
				[ 
					'key' => '_EventStartDate',
					'value' => (new DateTime())->format('Y-m-d H:i:s'),
					'compare' => '>=',
				],
			]
		];              

		$posts = get_posts( $args );
		$product_ids = [];
		
		foreach ( $posts as $post ) {
			$id = get_post_meta($post, 'amazon_identifier', true);

			if ( !empty($id) ) {
				$product_ids[$id] = $post;
			}
		}

		return $product_ids;
	}

	/**
	 * Method to update tribe_events records with details about a product from its amazon
	 * product identifier.
	 * 
	 * An update looks like:
	 * 
	 * [product_id] = [
	 * 	 post,  //post id (int)
	 *   title, //post title (string)
	 *   author[], // array of post authors,
	 *   image, // post image URL (string)
	 *   release, // post release date (string)
	 * ]
	 *
	 * @since    1.0.0
	 * @param    update[]   		$updates       	The ID of the post in the database
	 */
	public function update_items($updates = []) {
		foreach ($updates as $update) {

			// Update all the relevant post meta

			// Update the author
			if (isset($update['author']) && !empty($update['author'])) {

				$author_name = $update['author'][0];

				// split string, take everything after ,\s and place at the start followed by space
				$keywords = preg_split("/,\s+/", $author_name);
				$author_name = implode(' ', array_reverse($keywords));

				$args = [
					'post_type'			=> 'tribe_organizer',
					'post_status' 		=> 'publish',
					'posts_per_page' 	=> 1,
					'order'    			=> 'ASC',
					'fields'			=> 'ids',
					'title' 			=> $author_name,
				];              

				$posts = get_posts( $args );

				// If we dont, we need to create a new organizer in the post table
				if (empty($posts)) {

					$new_author_args = [
						'post_title' 	=> $author_name,
						'post_status' 	=> 'publish',
						'post_type' 	=> 'tribe_organizer',
					];

					$new_author = wp_insert_post( $new_author_args );

					// Let WP error handling take over
					if (is_wp_error( $new_author)) {
						return $new_author;
					}

					$posts[0] = $new_author;
				}

				// Get the post content so that we can replace values for gutenburg editor
				$content = get_post_field('post_content', $update['post']);

				// Searching for 
				// <!-- wp:tribe/event-organizer /-->
				// Replacing with
				// <!-- wp:tribe/event-organizer {"organizer":711} /-->

				$updated_post_content = str_replace( 
					'<!-- wp:tribe/event-organizer /-->', 
					'<!-- wp:tribe/event-organizer {"organizer": ' . $posts[0] . '} /-->',
					$content
				);

				// Update the title and content
				wp_update_post( [
					'ID' => $update['post'],
					'post_title' => $update['title'],
					'post_content' => $updated_post_content,
				] );

				if (!empty($posts[0])) {
					// we can just update this posts's author with the new organizer
					update_post_meta($update['post'], '_EventOrganizerID', $posts[0]);
				}
			}
			
			// Save the product image
			update_post_meta($update['post'], 'amazon_product_image_url', $update['image']);

			// Update the release date
			$format = 'Y-m-d\TH:i:s\Z';
			$release_date = DateTime::createFromFormat($format, $update['release']);

			update_post_meta($update['post'], '_EventStartDate', $release_date->format('Y-m-d H:i:s'));
			update_post_meta($update['post'], '_EventEndDate', $release_date->format('Y-m-d H:i:s'));
			update_post_meta($update['post'], '_EventStartDateUTC', $release_date->format('Y-m-d H:i:s'));
			update_post_meta($update['post'], '_EventEndDateUTC', $release_date->format('Y-m-d H:i:s'));
		}
	}

	/**
	 * Method to fetch items from the Amazon Product SDK based on their Amazon Product Identifier
	 * or ISBN.
	 *
	 * @since    1.0.0
	 * @param    product[]    		$products       	Array of product IDs mapped to WordPress post ids.
	 */
	public function fetch_items($products = []) {
		$config = new Configuration();
		$access_key_id = $_ENV['AWS_ACCESS_KEY_ID'];
		$secret_access_key = $_ENV['AWS_SECRET_ACCESS_KEY'];
		$tracking_id = $_ENV['AWS_TRACKING_ID'];

		/*
		* Add your credentials
		*/
		# Please add your access key here
		$config->setAccessKey($access_key_id);
		# Please add your secret key here
		$config->setSecretKey($secret_access_key);

		# Please add your partner tag (store/tracking id) here
		$partnerTag = $tracking_id;

		/*
		* PAAPI host and region to which you want to send request
		* For more details refer:
		* https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
		*/
		$config->setHost('webservices.amazon.co.uk');
		$config->setRegion('eu-west-1');

		$apiInstance = new DefaultApi(
			/*
			* If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
			* This is optional, `GuzzleHttp\Client` will be used as default.
			*/
			new GuzzleHttp\Client(),
			$config
		);

		# Request initialization

		/*
		* Choose resources you want from GetItemsResource enum
		* For more details,
		* refer: https://webservices.amazon.com/paapi5/documentation/search-items.html#resources-parameter
		*/
		$resources = [
			GetItemsResource::ITEM_INFOTITLE,
			GetItemsResource::ITEM_INFOPRODUCT_INFO,
			GetItemsResource::ITEM_INFOBY_LINE_INFO,
			GetItemsResource::ITEM_INFOTECHNICAL_INFO,
			GetItemsResource::ITEM_INFOCONTENT_INFO,
			GetItemsResource::ITEM_INFOFEATURES,
			GetItemsResource::IMAGESPRIMARYMEDIUM
		];

		$product_ids = array_map(function($value) {
			return (string) $value;
		}, array_keys($products));

		// Batch into groups of 10
		$updates = [];

		$product_ids_batched = array_chunk( $product_ids, 10 );
		
		for ($i = 0; $i < count($product_ids_batched); $i++) {
			# Forming the request
			$searchItemsRequest = new GetItemsRequest();
			$searchItemsRequest->setItemIds($product_ids_batched[$i]);
			$searchItemsRequest->setPartnerTag($partnerTag);
			$searchItemsRequest->setPartnerType(PartnerType::ASSOCIATES);
			$searchItemsRequest->setResources($resources);

			# Validating request
			$invalidPropertyList = $searchItemsRequest->listInvalidProperties();
			$length = count($invalidPropertyList);
			if ($length > 0) {
				echo "Error forming the request", PHP_EOL;
				foreach ($invalidPropertyList as $invalidProperty) {
					echo $invalidProperty, PHP_EOL;
				}
				return;
			}

			# Sending the request
			try {
				$searchItemsResponse = $apiInstance->getItems($searchItemsRequest);

				# Parsing the response
				if ($searchItemsResponse->getItemsResult() !== null) {
					foreach ($searchItemsResponse->getItemsResult()->getItems() as $item) {
						if ($item !== null) {
							
							if ($item->getASIN() === null) {
								// Without an ASIN we can't update
								continue;
							}

							$asin = $item->getASIN();
							$updates[$asin]['ASIN'] = $asin;
							$updates[$asin]['post'] = $products[$asin];

							if ($item->getDetailPageURL() !== null) {
								$updates[$asin]['detail'] = $item->getDetailPageURL();
							}

							if ($item->getItemInfo() !== null
								&& $item->getItemInfo()->getTitle() !== null
								&& $item->getItemInfo()->getTitle()->getDisplayValue() !== null) {
								
								$updates[$asin]['title'] = $item->getItemInfo()->getTitle()->getDisplayValue();
							}

							if ($item->getItemInfo() !== null && 
								$item->getItemInfo()->getByLineInfo() !== null &&
								$item->getItemInfo()->getByLineInfo()->getContributors() !== null) {

								$contributors = $item->getItemInfo()->getByLineInfo()->getContributors();

								foreach ($contributors as $contributor) {
									if ($contributor->getRoleType() === 'author') {
										$updates[$asin]['author'][] = $contributor->getName();
									}
								}
							}

							if ($item->getImages() !== null && 
								$item->getImages()->getPrimary() !== null &&
								$item->getImages()->getPrimary()->getMedium() !== null) {
									$updates[$asin]['image'] = $item->getImages()->getPrimary()->getMedium()->getURL();	
							}

							if ($item->getItemInfo()->getProductInfo() !== null && 
								$item->getItemInfo()->getProductInfo()->getReleaseDate() !== null &&
								$item->getItemInfo()->getProductInfo()->getReleaseDate()->getLabel() !== null) {
									$updates[$asin]['release'] = $item->getItemInfo()->getProductInfo()->getReleaseDate()->getDisplayValue();	
							}
						}
					}
				}

				if ($searchItemsResponse->getErrors() !== null) {
					echo PHP_EOL, 'Printing Errors:', PHP_EOL, 'Printing first error object from list of errors', PHP_EOL;
					echo 'Error code: ', $searchItemsResponse->getErrors()[0]->getCode(), PHP_EOL;
					echo 'Error message: ', $searchItemsResponse->getErrors()[0]->getMessage(), PHP_EOL;
				}

			} catch (ApiException $exception) {
				echo "Error calling PA-API 5.0!", PHP_EOL;
				echo "HTTP Status Code: ", $exception->getCode(), PHP_EOL;
				echo "Error Message: ", $exception->getMessage(), PHP_EOL;
				if ($exception->getResponseObject() instanceof ProductAdvertisingAPIClientException) {
					$errors = $exception->getResponseObject()->getErrors();
					foreach ($errors as $error) {
						echo "Error Type: ", $error->getCode(), PHP_EOL;
						echo "Error Message: ", $error->getMessage(), PHP_EOL;
					}
				} else {
					echo "Error response body: ", $exception->getResponseBody(), PHP_EOL;
				}
			} catch (Exception $exception) {
				echo "Error Message: ", $exception->getMessage(), PHP_EOL;
			}
		}

		return $updates;
	}
}
