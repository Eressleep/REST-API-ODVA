<?php
function odvaTeleproject()
{
	add_action( 'rest_api_init', function ()
	{
		register_rest_route( 'wp/v2/', 'teleproject_release',['methods' => WP_REST_Server::READABLE,'callback' => 'teleproject_release',]);
		register_rest_field( 'teleproject', 'ID',            ['get_callback' => 'id',      'schema' => null]);
		register_rest_field( 'teleproject', 'img',           ['get_callback' => 'img',     'schema' => null]);
		register_rest_field( 'teleproject', 'content',       ['get_callback' => 'content', 'schema' => null]);
		register_rest_field( 'teleproject', 'excerpt',       ['get_callback' => 'excerpt', 'schema' => null]);
		register_rest_field( 'teleproject', 'title',         ['get_callback' => 'title',   'schema' => null]);

	});

	function teleproject_release($request)
	{
		$args_teleproject_release =
			[
			'post_status' 	 => 'publish',
			'posts_per_page' => 10,
			'post_type' 	 => 'teleproject_release',
			'meta_key'   	 => 'teleproject_id_parent',
			'paged' 		 => ($_REQUEST['paged'] ? $_REQUEST['paged'] : 1)
			];

		$wp_query_teleproject_release = new WP_Query($args_teleproject_release);
		$answer = [];
		foreach ($wp_query_teleproject_release->posts as $stroke)
		{
			$video_link = '';
			$str = get_field("teleproject_release_video",$stroke->ID);
			$len = strlen($str);
			$flag = false;
			for ($i = 0; $i < $len - 4; $i++){
				if($str[$i].$str[$i+1].$str[$i+2].$str[$i+3] == 'rc="' || $flag == true){
					if($str[$i+4].$str[$i+5] == '" ')
						break;

					$video_link .= $str[$i+4];
					$flag = true;

				}
			}

			$answer[] =
			[
				'ID' 			=> $stroke->ID,
				'img' 			=> get_the_post_thumbnail_url($stroke->ID),
				'title' 		=> $stroke->post_title,
				'date' 			=> get_field('teleproject_release_date_publish', $stroke->ID),
				'content' 		=> $stroke->post_content,
				'excerpt' 		=> $stroke->post_excerpt,
				'views' 		=> get_field('views', $stroke->ID),
				'comment_count' => $stroke->comment_count,
				'video' 		=> $video_link,
				'perents' 		=> get_field('teleproject_id_parent',$stroke->ID)
			];
		}
		return $answer;
	}

}
