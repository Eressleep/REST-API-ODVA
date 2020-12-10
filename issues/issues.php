<?php
function odvaIssues()
{
	function issues()
	{

		$answer = [];

		$wp_query_all_teleprojects = new WP_Query([
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'post_type' => 'teleproject',
			'orderby' => 'date',
			'order' => 'DESC',
		]);
		foreach ($wp_query_all_teleprojects->posts as $teleproject)
		{
			//убрать регулярки
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
	function issues_id(WP_REST_Request $request){

		$answer = [];

		$wp_query_all_teleprojects = new WP_Query([
			'post_status' => 'publish',
			'posts_per_page' => 10,
			'post_type' => 'teleproject_release',
			'meta_query'	=> [
				'relation'		=> 'AND',
				[
					'key'	 	=> 'teleproject_id_parent',
					'value'	  	=>  (int)$request['id'],
					'compare' 	=> '=',
				],
			],
		]);

		foreach ($wp_query_all_teleprojects->posts as $teleproject)
		{
			//убрать регулярки
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
		register_rest_route( 'wp/v2/', 'issues',             ['methods' => 'GET','callback' => 'issues',]);
		register_rest_route( 'wp/v2/', 'issues/(?P<id>\d+)', ['methods' => 'GET','callback' => 'issues_id',]);


		//additional fields
		register_rest_field( 'teleproject_release', 'content',       ['get_callback' => 'content',        'schema' => null,]);
		register_rest_field( 'teleproject_release', 'excerpt',       ['get_callback' => 'excerpt',        'schema' => null,]);
		register_rest_field( 'teleproject_release', 'ID',            ['get_callback' => 'id',             'schema' => null,]);
		register_rest_field( 'teleproject_release', 'img',           ['get_callback' => 'img',            'schema' => null,]);
		register_rest_field( 'teleproject_release', 'title',         ['get_callback' => 'title',          'schema' => null,]);

		register_rest_field( 'teleproject_release', 'comment_count', ['get_callback' => 'comments_count', 'schema' => null,]);
		register_rest_field( 'teleproject_release', 'video',         ['get_callback' => 'video',          'schema' => null,]);

	});
}
