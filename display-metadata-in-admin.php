<?php
namespace display_metadata;
/*
* Plugin Name: Display metadata in Admin
* Plugin URI: https://wpspeeddoctor.com/
* Description: Displays a metabox with post basic info and metadata
* Last update: 2023-12-10
* Version: 1.0.3
* Author: Jaro Kurimsky
* License: GPLv2 or later
*/	

defined( 'ABSPATH' ) || exit;

if ( $pagenow==='post.php' && ( ($_GET['action']??'') ==='edit' ) ) {

	require __DIR__.'/display-metadata-functions.php';
}
