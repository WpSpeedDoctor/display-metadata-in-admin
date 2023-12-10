<?php
namespace display_metadata;
/*
* Plugin Name: Display metadata in Admin
* Plugin URI: https://wpspeeddoctor.com/
* Description: When editing post, page or product,order and subscription it will display metabox with wp_post and wp_postmeta of current page
* Last update: 2023-12-10
* Version: 1.0.1
* Author: Jaro Kurimsky
* License: GPLv2 or later
* Filters included: display_metadata_post_type, display_data_keys
*/	

defined( 'ABSPATH' ) || exit;

if ( $pagenow==='post.php' && ( ($_GET['action']??'') ==='edit' ) ) {

	require __DIR__.'/display-metadata-functions.php';
}
