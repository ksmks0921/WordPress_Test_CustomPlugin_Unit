<?php
/*
Plugin Name: Test Plugin
Description: A custom plugin that makes an API call and creates unit records.
*/

//// Create Unit CPT
function register_unit_cpt() {
    register_post_type( 'unit', [
        'label' => 'units',
        'public' => true,
        'capability_type' => 'post'
    ]);
}
add_action( 'init', 'register_unit_cpt' );
// remove post content editor
add_action('init', 'my_rem_editor_from_post_type');
function my_rem_editor_from_post_type() {
    remove_post_type_support( 'unit', 'editor' );
}

// Add the admin page
add_action('admin_menu', 'my_custom_plugin_add_admin_page');
function my_custom_plugin_add_admin_page() {
    add_menu_page('My Custom Plugin', 'My Custom Plugin', 'manage_options', 'my-custom-plugin', 'my_custom_plugin_render_admin_page');
}

// Render the admin page
function my_custom_plugin_render_admin_page() {
    ?>
    <div class="wrap">
        <h1>My Custom Plugin</h1>
        <button id="my-custom-plugin-button">Get Custom Posts</button>
    </div>
    <?php
}

// Add the JavaScript file
add_action('admin_enqueue_scripts', 'my_custom_plugin_enqueue_scripts');
function my_custom_plugin_enqueue_scripts() {
    wp_enqueue_script('my-custom-plugin-script', plugins_url('my-custom-plugin.js', __FILE__), array('jquery'), '1.0', true);
}

// Call API to save unit records
add_action('wp_ajax_my_custom_plugin_get_custom_posts', 'my_custom_plugin_get_custom_posts');
function my_custom_plugin_get_custom_posts() {

    $response = wp_remote_get('https://api.sightmap.com/v1/assets/1273/multifamily/units?per-page=250', array( 'headers' => array( 'API-Key' => "7d64ca3869544c469c3e7a586921ba37")));
    $data = json_decode(wp_remote_retrieve_body($response));    

    foreach ($data->data as $post) {
        $post_id = wp_insert_post(array(
            'post_name' => $post->unit_number,
            'post_title' => $post->unit_number,
            'post_type' => 'unit',
            'post_status' => 'publish'
        ));  
        update_field("asset_id", $post->asset_id,$post_id);   
        update_field("building_id", $post->building_id,$post_id);  
        update_field("floor_plan_id", $post->floor_plan_id,$post_id);  
        update_field("floor_id", $post->floor_id,$post_id);  
        update_field("area", $post->area,$post_id);    
    }   
    wp_die();
}
// Add custom colum in 'unit' posts admin page
add_filter( "manage_unit_posts_columns", function ( $defaults ) {	
	$defaults['custom-one'] = 'floor_plan_id';
	return $defaults;
} );
// Handle the value for each of the new columns.
add_action( "manage_unit_posts_custom_column", function ( $column_name, $post_id ) {	
	if ( $column_name == 'custom-one' ) {
        $terms = get_field( 'floor_plan_id', $post_id );     
        if ( is_string( $terms ) )
            echo $terms;
        else
            _e( 'Unable to get data', 'your_text_domain' );        
	}		
}, 10, 2 );

// Add css 
wp_register_style( 'dataTableCSS', 'https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.css' );
wp_register_style( 'bootstrapCSS', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' );
wp_enqueue_style('bootstrapCSS');
wp_enqueue_style('dataTableCSS');
// Create Shortcode to Display Unit Post Types  
function diwp_create_shortcode_unit_post_type(){  
    $args = array(
                    'post_type'      => 'unit', 
                    'posts_per_page' => '10',
                    'publish_status' => 'published',
                 );  
    $query = new WP_Query($args);  
    $result ="";
    if($query->have_posts()) :
        $result ='<table id="table" class="table table-bordered">
        <thead>
          <tr>                             
            <th>asset_id</th>
            <th>building_id</th>
            <th>floor_id</th>
            <th>floor_plan_id</th>
            <th>area</th>
          </tr>
        </thead>
        <tbody id="tablecontents">';
        $list_one = [];
        while($query->have_posts()) :  

            $query->the_post();               
            if(get_post_meta(get_the_id(), 'area')[0] != 1) {
                $result .= '<tr class="row1">';    	                               
                $result .= '<td>'. get_post_meta(get_the_id(), 'asset_id')[0] . '</td>';
                $result .= '<td>'. get_post_meta(get_the_id(), 'building_id')[0] . '</td>';
                $result .= '<td>'. get_post_meta(get_the_id(), 'floor_id')[0] . '</td>';
                $result .= '<td>'. get_post_meta(get_the_id(), 'floor_plan_id')[0] . '</td>';
                $result .= '<td>'. get_post_meta(get_the_id(), 'area')[0] . '</td>';           
                $result .= '</tr>';  
            }
            else {
                $unit_item = "";
                $unit_item .= '<tr class="row1">';    	                               
                $unit_item .= '<td>'. get_post_meta(get_the_id(), 'asset_id')[0] . '</td>';
                $unit_item .= '<td>'. get_post_meta(get_the_id(), 'building_id')[0] . '</td>';
                $unit_item .= '<td>'. get_post_meta(get_the_id(), 'floor_id')[0] . '</td>';
                $unit_item .= '<td>'. get_post_meta(get_the_id(), 'floor_plan_id')[0] . '</td>';
                $unit_item .= '<td>'. get_post_meta(get_the_id(), 'area')[0] . '</td>';           
                $unit_item .= '</tr>';  
                array_push($list_one, $unit_item);
            }           

        endwhile;
        foreach ($list_one as $list) {
            $result .= $list;                                            
        }
        $result .='</tbody>                  
        </table>';
        wp_reset_postdata();  
    endif;      
    return $result;            
}  
add_shortcode( 'unit-list', 'diwp_create_shortcode_unit_post_type' ); 

//Add js
wp_register_script( 'dataTableJS', 'https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.js', null, null, true );
wp_enqueue_script('dataTableJS');
