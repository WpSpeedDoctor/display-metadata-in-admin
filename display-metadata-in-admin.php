<?php
namespace display_metadata;
/*
* Plugin Name: Display metadata in Admin
* Plugin URI: https://wpspeeddoctor.com/
* Description: When editing post, page or product,order and subscription it will display metabox with wp_post and wp_postmeta of current page
* Last update: 2022-10-19
* Version: 1.0.0
* Author: Jaro Kurimsky
* License: GPLv2 or later
* Filters included: display_metadata_postype, display_data_keys
*/	

defined( 'ABSPATH' ) || exit;

if ( is_admin() && ($_GET['action']??''==='edit' && isset($_GET['page'])) ) {

	require __DIR__.'/display-metadata-functions.php';
}
