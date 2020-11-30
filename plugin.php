<?php
/**
 * Plugin Name: WP REST API 
 * Plugin URI:  nope
 * Description: nope
 * Author:      Alex Sem
 * Author URI: 	nope
 * Version: 	0.1
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 **/

add_action( 'rest_api_init', 'rest_api_add_post_date_query_column_each_post_type' );

function rest_api_add_post_date_query_column_each_post_type() {
	foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
		add_filter( 'rest_' . $post_type->name . '_collection_params', 'rest_api_add_post_date_query_column_param');
        add_filter( 'rest_' . $post_type->name . '_query', 'rest_api_filter_posts_using_date_query_column', 10, 2 );
	}
}

function rest_api_add_post_date_query_column_param( $query_params ) {
	$query_params['date_query_column'] = [
            'description' => __( 'The date query column.' ),
            'type'        => 'string',
            'enum'        => ['post_date', 'post_date_gmt', 'post_modified', 'post_modified_gmt'],
        ];
    return $query_params;
}

function rest_api_filter_posts_using_date_query_column( $args, $request ) {
	if ( ! isset( $request['before'] ) && ! isset( $request['after'] )  )
        return $args;

    if( isset( $request['date_query_column'] ) )
        $args['date_query'][0]['column'] = 'post_' . $request['date_query_column'];

    return $args;
}