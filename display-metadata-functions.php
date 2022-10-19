<?php
namespace display_metadata;

function get_post_type_enabled_display(){

	$post_type = [
		'post',
		'page',
		'product',
		'shop_order',
		'shop_subscription'
	];

	return apply_filters('display_metadata_postype',$post_type);

}

function get_post_data_keys(){

	$post_data_keys = [
		'post_parent',
		'post_type',
	];

	return apply_filters('display_data_keys',$post_data_keys);

}

add_action( 'init', 'display_metadata\add_my_metabox');

function add_my_metabox(){

	if (!current_user_can( 'manage_options' ) ) return;
	
	add_action('add_meta_boxes', 'display_metadata\add_display_metadata_metabox');
}

function add_display_metadata_metabox(){

		add_meta_box(
			'postmeta',
			'Post and Postmeta',
			'display_metadata\display_postmeta_in_metabox', 
			get_post_type_enabled_display(),                
			'normal', 
			'low');
}

function display_postmeta_in_metabox() {

	the_metadata_metabox_css();

	$post_data = get_post_data();

	display_values_in_table('Post', $post_data);
	
	?><hr><?php
	
	$post_meta = get_post_meta( get_the_id());

	display_values_in_table('Postmeta', $post_meta);
		
}

function display_values_in_table($title, $data){

	if ( empty($data) ) return;

	?>
<h3><?=$title?></h3>
<table>
	<?php
	
	foreach( $data as $key => $value ){

		$value_to_display = get_value_display_markup($value);
		
		display_metadata_value($key,$value_to_display);
		
	}

?></table>
<?php

}

function get_post_data(){
	
	global $post;

	$post_data_keys = get_post_data_keys();

	foreach($post as $key => $value){

		if(!in_array($key,$post_data_keys)) continue;

		$result[$key][0]= $value;

	}

	return $result??false;

}

function display_metadata_value($key,$value_to_display){
	?>
	<tr class="metadata-row">
		<td class="metadata-key"><?=$key?></td>
		<td class="metadata-value"><?=$value_to_display?></td>
	</tr>
	<?php
}

function get_value_display_markup($value){

	$multiple_values_as_string = get_value_string($value);

	$value_to_display = get_value_unserialized_or_json_decode($multiple_values_as_string);

	if ( is_string($value_to_display) || is_int($value_to_display) ) return $value_to_display;

	return htmlentities( var_export($value_to_display,true));
}

function get_value_string($value_array){

	if ( count($value_array)===1 ) return $value_array[0];

	$result = '';

	foreach( $value_array as $key=> $value ){

		$value_to_display = get_value_unserialized_or_json_decode($value);

		$result .= "[$key] => ".get_value_based_on_type($value_to_display).PHP_EOL;
	}

	return $result;
}

function get_value_based_on_type($value_to_display){

	if ( is_string($value_to_display) || is_int($value_to_display) ) return $value_to_display;
	
	return var_export($value_to_display,true);
}

function get_value_unserialized_or_json_decode($value){

	$result = get_unserialised_value($value);

	if ($result !== null ) return $result;

	$result = get_json_decode_value($value);

	if ($result !== null ) return $result;

	return $value;
}

function get_json_decode_value($value){
	
	$value_without_spaces = trim($value);
	
	if ( $value_without_spaces ==='' || !isset($value_without_spaces[0]) ) return '';

	$json_starting_characters=['"','{','['];

	if ( !in_array($value_without_spaces[0],$json_starting_characters) ) return null;
	
	return json_decode($value_without_spaces,true);
}

function get_unserialised_value($value){

	if ( ($value[1]??'') !==':') return null;

	$unserialised_value = @unserialize($value);

	return is_invalid_unserialization( $value, $unserialised_value) ? null: $unserialised_value;
}

function is_invalid_unserialization( $value, $unserialised_value){

	if( $unserialised_value === $value ) return true;

	return $value !=="b:0;" && $unserialised_value === false;
}

/*
 * inlined CSS because life is too short to enqueue
 * 8 lines of CSS as an external file :)
 */

function the_metadata_metabox_css(){
	?>
<style>
.metadata-key,
.metadata-value{
	padding:0 10px 10px 0;
	vertical-align: baseline;
	white-space: break-spaces;
}
.metadata-row{
	margin-bottom: 10px;
}
</style>
	<?php
}