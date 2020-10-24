# Amazon Product Import
A WordPress Plugin to import products from the Amazon Product SDK into The Events Calendar Plugin.

# Requirements

This plugin requires The Events Calendar plugin (see https://theeventscalendar.com/products/wordpress-events-calendar/) to be installed in your WordPress application / site.

# Setup

To get setup for development:

 - Clone the repo
 - Make sure you have docker installed (development requirement only)
 - Start the docker container
   - `cd docker`
   - `docker-compose up`
 - Wordpress should now be running on localhost with a test admin account
   (see `docker/docker-compose.yml` for details)
 - If you would like to include The Events calendar, just copy the plugin into the `src/plugins` directory (it will be automatically ignored by GIT).
 - Start Coding!

# Installation / Usage

To install the plugin:

 - Copy the `src/plugins/amazon-product-import` folder from the repo and put it in your 
   Wordpress plugins directory.
 - Add a `.env` file into `src/plugins/amazon-product-import` with AWS API credential values from the [Amazon Product SDK](https://webservices.amazon.com/paapi5/documentation/):
   - AWS_ACCESS_KEY_ID
   - AWS_SECRET_ACCESS_KEY
   - AWS_TRACKING_ID
 - Activate the plugin in the WordPress Admin

# Featured Images (Optional)

Optionally, you can use the provided `featured-image.php` to also use product images from Amazon as the featured image for The Events Calendar Events posts (tribe_events). To do this:

 - Copy `featured-image.php`
 - Place it within your activated WordPress theme at 
    `[your-theme]/tribe/events/v2/list/event/featured-image.php`
 - Note: make sure to add the intermediate folders named as above so that WordPress template heirarchy correctly locates the template.
 - e.g. To use this file with the twentytwenty default WordPress theme, place `feature-image.php` at
 `wordpress-install-directory/wp-content/themes/twentytwenty/tribe/events/v2/list/event/featured-image.php`

Happy coding!