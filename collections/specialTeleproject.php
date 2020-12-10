<?php
function specialTeleproject()
{
	function special() {

		$answer = [];
		$wp_query_special_project = new WP_Query([
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'post_type' => 'teleproject',
			'meta_query'	=> [
				'relation'		=> 'AND',
				[
					'key'	 	=> 'in_special_project',
					'value'	  	=> true,
					'compare' 	=> '=',
				]
			],
		]);
		foreach ($wp_query_special_project->posts as $posty)
		{
			//убрта обращения к объектам
			$answer[] = [
				'ID'                    => $posty->ID,
				'img'                   => get_the_post_thumbnail_url($posty->ID),
				'title'                 => $posty->post_title,
				'excerpt'               => $posty->post_excerpt,
				'content'               => $posty->post_content,
				'teleproject_main_time' =>  get_field('teleproject_main_time', $posty->ID),
				'teleproject_day'       =>  get_field('teleproject_day', $posty->ID),
				'views'                 => get_field('views', $posty->ID)
			];
		}
		return $answer;
	}
	add_action( 'rest_api_init', function ()
	{
		register_rest_route( 'wp/v2/', 'special', ['methods' => WP_REST_Server::READABLE,'callback' => 'special',]);
	});

}