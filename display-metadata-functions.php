<?php
namespace display_metadata;

function get_post_type_enabled_display(){

	return null; //displays on all post types

/*
	$post_type = [

		'post',
		'page',
		'product',
		'shop_order',
		'shop_subscription'

	];

	return apply_filters('display_metadata_post_type',$post_type);
*/
}

function get_post_data_keys(){

	$post_data_keys = [
		'post_status',
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
			'wpsd-display-postmeta',
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
	
	$post_meta = get_post_meta( get_the_id() );

	display_values_in_table('Postmeta', $post_meta);
		
}

function display_values_in_table($title, $data){

	if ( empty($data) ) return;

	echo <<<HTML
	<h3>{$title}</h3>
	<table class="metadata-table">
	HTML;
	
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

function display_metadata_value($key, $value_to_display) {
	
	echo <<<HTML
	<tr class="metadata-row">
		<td class="metadata-key">{$key}</td>
		<td class="metadata-value">{$value_to_display}</td>
	</tr>
	HTML;
}

function get_value_display_markup($value){

	$multiple_values_as_string = get_value_string($value);

	$value_to_display = get_value_unserialized_or_json_decode($multiple_values_as_string);
	
	if ( is_string($value_to_display) || is_int($value_to_display) ) return htmlentities($value_to_display);

	return get_iterable_as_display_markup($value_to_display);
}

function get_iterable_as_display_markup($data){

	if( is_array($data) && empty($data) ) return '[ ]';

	if( is_object($data) && empty($data) ) return '[ ]';

	$result_loop = get_iterable_as_display_markup_loop($data);

	if($result_loop === '') return htmlentities( var_export($data,true) ); 

    $result = <<<HTML
    [<br><table class="iterable-table">
        <tbody>
            {$result_loop}
    </tbody>
    </table><br>]
    HTML;

    return trim($result);
}

function get_iterable_as_display_markup_loop($data){

	$result = '';

    foreach( $data as $key=> $value ){

        $key_display = htmlentities($key);

        $value_display = get_value_display( $value );

        $result .= <<<HTML
        <tr>
            <td class="iterable-key words-unbreakable">     $key_display</td>
            <td class="iterable-arrow words-unbreakable">=></td>
            <td class="iterable-value">$value_display</td>
        </tr>
        HTML;
    }

	return $result === '' ? htmlentities( var_export($data,true) ) : $result; 

}

function get_value_display( $value ){
	
	if( is_bool($value) ) return $value ? 'bool:true':'bool:false';

	if( $value === null ) return 'NULL';

	if( is_numeric($value) || is_string($value) ) return htmlentities( $value );

    return get_iterable_as_display_markup( $value );
}

function get_value_string($value_array){

	if ( count($value_array)===1 ) return reset($value_array);

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

function get_value_unserialized_or_json_decode( $value ){
	
	if( is_numeric($value) ) return $value;

	if( is_serialized($value) ) return unserialize($value);

	$result = json_decode($value,true);

	return json_last_error() === 0 ? $result : $value;
}

/*
 * inlined CSS because life is too short to enqueue
 * a few lines of CSS as an external file :)
 */

function the_metadata_metabox_css(){
	?>
<style>
.metadata-table{
	width: 100%; 
    table-layout: auto; 
    border-collapse: collapse;
}
.metadata-key{
	white-space: nowrap;
}
.metadata-value{

	white-space: break-spaces;
}

.metadata-key,
.metadata-value{
	padding:0 10px 10px 0;
	vertical-align: baseline;
}
.metadata-row{
	margin-bottom: 10px;
}

.iterable-table {
    width: 100%; 
    border-collapse: collapse;
}

.iterable-arrow {
    padding: 0 5px; 
}

.iterable-key {
    width: max-content;
    display: block;
}
.iterable-value {
    word-break: break-word;
    overflow-wrap: break-word; 
}


.words-unbreakable{
    word-break: keep-all;
}
.iterable-table, .iterable-wrap, .iterable-arrow{

    vertical-align: baseline;
	max-width: max-content;
}

</style>
	<?php
}
