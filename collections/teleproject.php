<?php
function odvaTeleproject()
{


	function teleproject_release(){
		$answer = [];

		$wp_query_teleproject_release = new WP_Query([
			'post_status' => 'publish',
			'posts_per_page' => 10,
			'post_type' => 'teleproject_release',
			'meta_key'   => 'teleproject_id_parent',
			'paged' => ($_REQUEST['paged'] ? $_REQUEST['paged'] : 1)
		]);

		foreach ($wp_query_teleproject_release->posts as $stroke){
			//убрать регулярки

			preg_match('/src="([^"]+)"/', get_field("teleproject_release_video",$stroke->ID), $match);
			$answer[] =
			[
				'ID' => $stroke->ID,
				'img' => get_the_post_thumbnail_url($stroke->ID),
				'title' => ($stroke->post_title),
				'date' => get_field('teleproject_release_date_publish', $stroke->ID),
				'content' => ($stroke->post_content),
				'excerpt' => ($stroke->post_excerpt),
				'views' => get_field('views', $stroke->ID),
				'comment_count' => $stroke->comment_count,
				'video' => $match[1],
				'perents' => get_field('teleproject_id_parent',$stroke->ID)
			];
		}
		return $answer;
	}

	add_action( 'rest_api_init', function ()
	{
		register_rest_route( 'wp/v2/', 'teleproject_release',['methods' => WP_REST_Server::READABLE,'callback' => 'teleproject_release',]);

		register_rest_field( 'teleproject', 'ID',            ['get_callback' => 'id',      'schema' => null]);
		register_rest_field( 'teleproject', 'img',           ['get_callback' => 'img',     'schema' => null]);
		register_rest_field( 'teleproject', 'content',       ['get_callback' => 'content', 'schema' => null]);
		register_rest_field( 'teleproject', 'excerpt',       ['get_callback' => 'excerpt', 'schema' => null]);
		register_rest_field( 'teleproject', 'title',         ['get_callback' => 'title',   'schema' => null]);

	});
}
