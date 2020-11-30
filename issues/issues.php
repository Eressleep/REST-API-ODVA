<?php
function odvaIssues()
{
	function get_issues()
	{

		$args_all_teleprojects =
			[
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'post_type' => 'teleproject',
				'orderby' => 'date',
				'order' => 'DESC',
			];
		$wp_query_all_teleprojects = new WP_Query($args_all_teleprojects);
		$answer = [];
		foreach ($wp_query_all_teleprojects->posts as $teleproject)
		{
			preg_match('/src="([^"]+)"/', get_field("teleproject_release_video",$teleproject->ID), $match);
			$answer[] =
				[
					'ID' => $teleproject->ID,
					'img' => get_the_post_thumbnail_url($teleproject->ID),
					'title' => ($teleproject->post_title),
					'date' => $teleproject->post_date,
					'content' => ($teleproject->post_content),
					'excerpt' => ($teleproject->post_excerpt),
					'views' => get_field('views', $teleproject->ID),
					'comment_count' =>wp_count_comments($teleproject->ID),
				];
		}
		return $answer;
	}
	function get_issues_id(WP_REST_Request $request){

		$args_teleproject_release_filter = array(
			'post_status' => 'publish',
			'posts_per_page' => 10,
			'post_type' => 'teleproject_release',
			'meta_query'	=> array(
				'relation'		=> 'AND',
				array(
					'key'	 	=> 'teleproject_id_parent',
					'value'	  	=>  (int)$request['id'],
					'compare' 	=> '=',
				),
			),
		);

		$wp_query_all_teleprojects = new WP_Query($args_teleproject_release_filter);
		$answer = [];
		foreach ($wp_query_all_teleprojects->posts as $teleproject)
		{
			preg_match('/src="([^"]+)"/', get_field("teleproject_release_video",$teleproject->ID), $match);
			$answer[] = array(
				'ID' => $teleproject->ID,
				'img' => get_the_post_thumbnail_url($teleproject->ID),
				'title' => ($teleproject->post_title),
				'date' => $teleproject->post_date,
				'content' => ($teleproject->post_content),
				'excerpt' => $teleproject->post_excerpt,
				'views' => get_field('views', $teleproject->ID),
				'comment_count' => $teleproject->comment_count,
				'video' => $match[1]
			);
		}
		return $answer;
	}
	add_action( 'rest_api_init', function () {
		register_rest_route( 'wp/v2/', 'issues',             ['methods' => 'GET','callback' => 'get_issues',]);
		register_rest_route( 'wp/v2/', 'issues/(?P<id>\d+)', ['methods' => 'GET','callback' => 'get_issues_id',	]);
	});


}
