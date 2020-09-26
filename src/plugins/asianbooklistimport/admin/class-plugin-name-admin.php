<?php

use Amazon\ProductAdvertisingAPI\v1\ApiException;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\api\DefaultApi;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\PartnerType;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\ProductAdvertisingAPIClientException;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsRequest;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsResource;
use Amazon\ProductAdvertisingAPI\v1\Configuration;

require_once(__DIR__ .'../../vendor/autoload.php'); 

/**
 * The admin-specific functionality of the plugin
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class Plugin_Name_Admin {

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
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/plugin-name-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugin-name-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function import_isbn() {

		// Todo: 
		// $this->searchItems();
	}


	public function searchItems() {
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

		# Specify keywords
		$keyword = '1911622463';

		/*
		* Specify the category in which search request is to be made
		* For more details, refer:
		* https://webservices.amazon.com/paapi5/documentation/use-cases/organization-of-items-on-amazon/search-index.html
		*/
		$searchIndex = "All";

		# Specify item count to be returned in search result
		$itemCount = 1;

		/*
		* Choose resources you want from SearchItemsResource enum
		* For more details,
		* refer: https://webservices.amazon.com/paapi5/documentation/search-items.html#resources-parameter
		*/
		$resources = [
			SearchItemsResource::ITEM_INFOTITLE,
			SearchItemsResource::ITEM_INFOPRODUCT_INFO,
			SearchItemsResource::ITEM_INFOBY_LINE_INFO,
			SearchItemsResource::ITEM_INFOTECHNICAL_INFO,
			SearchItemsResource::ITEM_INFOCONTENT_INFO,
			SearchItemsResource::ITEM_INFOFEATURES,
			SearchItemsResource::IMAGESPRIMARYMEDIUM,
			SearchItemsResource::OFFERSLISTINGSMERCHANT_INFO,
			SearchItemsResource::OFFERSLISTINGSPRICE];

		# Forming the request
		$searchItemsRequest = new SearchItemsRequest();
		$searchItemsRequest->setSearchIndex($searchIndex);
		$searchItemsRequest->setKeywords($keyword);
		$searchItemsRequest->setItemCount($itemCount);
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

		echo '<pre>';

		# Sending the request
		try {
			$searchItemsResponse = $apiInstance->searchItems($searchItemsRequest);

			echo 'API called successfully', PHP_EOL;
			echo 'Complete Response: ', $searchItemsResponse, PHP_EOL;

			# Parsing the response
			if ($searchItemsResponse->getSearchResult() !== null) {
				echo 'Printing first item information in SearchResult:', PHP_EOL;
				$item = $searchItemsResponse->getSearchResult()->getItems()[0];
				if ($item !== null) {
					if ($item->getASIN() !== null) {
						echo "ISBN: ", $item->getASIN(), PHP_EOL;
					}
					if ($item->getDetailPageURL() !== null) {
						echo "DetailPageURL: ", $item->getDetailPageURL(), PHP_EOL;
					}
					if ($item->getItemInfo() !== null
						and $item->getItemInfo()->getTitle() !== null
						and $item->getItemInfo()->getTitle()->getDisplayValue() !== null) {
						echo "Title: ", $item->getItemInfo()->getTitle()->getDisplayValue(), PHP_EOL;
					}
					if ($item->getItemInfo() !== null && 
						$item->getItemInfo()->getByLineInfo() !== null &&
						$item->getItemInfo()->getByLineInfo()->getContributors() !== null) {

						$contributors = $item->getItemInfo()->getByLineInfo()->getContributors();

						foreach ($contributors as $contributor) {
							if ($contributor->getRoleType() === 'author') {
								echo "Author: ", $contributor->getName(), PHP_EOL;
							}
						}
					}
					if ($item->getImages() !== null && 
						$item->getImages()->getPrimary() !== null &&
						$item->getImages()->getPrimary()->getMedium() !== null) {
							echo "Image URL: " . $item->getImages()->getPrimary()->getMedium()->getURL() . PHP_EOL;
					}
					if ($item->getItemInfo()->getProductInfo() !== null && 
						$item->getItemInfo()->getProductInfo()->getReleaseDate() !== null &&
						$item->getItemInfo()->getProductInfo()->getReleaseDate()->getLabel() !== null) {
							echo "Release Date: " . $item->getItemInfo()->getProductInfo()->getReleaseDate()->getDisplayValue() . PHP_EOL;
					}
					if ($item->getOffers() !== null
						and $item->getOffers() !== null
						and $item->getOffers()->getListings() !== null
						and $item->getOffers()->getListings()[0]->getPrice() !== null
						and $item->getOffers()->getListings()[0]->getPrice()->getDisplayAmount() !== null) {
						echo "Buying price: ", $item->getOffers()->getListings()[0]->getPrice()
							->getDisplayAmount(), PHP_EOL;
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

		echo '</pre>';
	}
}
