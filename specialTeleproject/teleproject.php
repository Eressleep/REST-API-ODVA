<?php
function specialTeleproject()
{
	function get_special() {

		$args_special_project = array(
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'post_type' => 'teleproject',
			'meta_query'	=> array(
				'relation'		=> 'AND',
				array(
					'key'	 	=> 'in_special_project',
					'value'	  	=> true,
					'compare' 	=> '=',
				)
			),
		);
		$wp_query_special_project = new WP_Query($args_special_project);
		$arr = array();
		foreach ($wp_query_special_project->posts as $posty)
		{
			$arr[] = array(
				'ID' => $posty->ID,
				'img' => get_the_post_thumbnail_url($posty->ID),
				'title'=> problem_special_symbols($posty->post_title),
				'excerpt' => problem_special_symbols($posty->post_excerpt),
				'content' => problem_special_symbols($posty->post_content),
				'teleproject_main_time' =>  get_field('teleproject_main_time', $posty->ID),
				'teleproject_day' =>  get_field('teleproject_day', $posty->ID),
				'views' => get_field('views', $posty->ID)
			);
		}
		return 2;
	}

	add_action( 'rest_api_init', function () {
		register_rest_route( 'wp/v2/', 'special', array(
			'methods' => 'GET',
			'callback' => 'get_special',
		));
	});
}