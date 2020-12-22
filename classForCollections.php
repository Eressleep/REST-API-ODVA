<?php
class collectionsForRestAPI{
	//additional field functions
	function setStructureOfAdditionalFields($name){
		if($name == 'post') {
			register_rest_field($name, 'content', ['get_callback' =>
				function ($object) {
					return $object['content']['rendered'];
				}, 'schema' => null,]);
			//repair
//			register_rest_field( $name, 'related',    	 ['get_callback' => 'related',           'schema' => null,]);
		}
		else {
			register_rest_field($name, 'content', ['get_callback' =>
				function ($object) {
					return strip_tags($object['content']['rendered']);
				}, 'schema' => null,]);
			register_rest_field($name, 'video',          ['get_callback' => 'video',          'schema' => null,]);
		}

		register_rest_field( $name, 'youtube',    	 ['get_callback' =>
			function($object){
				$ans = '';
				$str = get_post_field('video_in_post', $object['id']);
				$len = strlen($str);
				$flag = false;
				for ($i = 0; $i < $len - 4; $i++){
					if($str[$i].$str[$i+1].$str[$i+2].$str[$i+3] == 'rc="' || $flag == true){
						if($str[$i+4].$str[$i+5] == '" ')
							break;

						$ans .= $str[$i+4];
						$flag = true;
					}
				}
				return $ans;
			},      'schema' => null,]);
		register_rest_field( $name, 'categories', 	 ['get_callback' =>
			function($object){
				return  wp_get_post_categories($object['id']);
			}, 'schema' => null,]);
		register_rest_field( $name, 'ID',        	 ['get_callback' =>
			function($object){
				return $object['id'];
			}, 'schema' => null,]);
		register_rest_field( $name, 'excerpt',  	 ['get_callback' =>
			function($object){
				return strip_tags($object['excerpt']['rendered']);
			},'schema' => null,]);
		register_rest_field( $name, 'hashtag',  	 ['get_callback' =>
			function($object){
				$hashtags = [];
				foreach (get_the_tags($object['id']) as $tags)
					$hashtags[] = "#".$tags->name;

				return $hashtags;
			},'schema' => null,]);
		register_rest_field( $name, 'img',       	 ['get_callback' =>
			function($object){
				return get_the_post_thumbnail_url($object['id']);
			},               'schema' => null,]);
		register_rest_field( $name, 'title',      	 ['get_callback' =>
			function($object){
				return $object['title']['rendered'];
			},             'schema' => null,]);
		register_rest_field( $name, 'views',         ['get_callback' =>
			function($object){
				return get_field('views',$object['id']);
			},'schema' => null,]);
		register_rest_field( $name, 'comment_count', ['get_callback' =>
			function($object){
				return wp_count_comments($object['id'])->total_comments;
			}, 'schema' => null,]);
	}

	function cleanLink($str){
		$video_link = '';
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
		return $video_link;
	}
	//repair
	function related($object){
		$tags_post = get_the_tags( $object['id']);
		$tags_array = [];

		foreach ($tags_post as $tags_post_item) {
			$tags_array[] = $tags_post_item->term_id;
		}

		$wp_query_on_this_topic = new WP_Query([
			'post_status' 	 => 'publish',
			'posts_per_page' => 3,
			'tag__in' 		 => $tags_array,
			'post__not_in' 	 =>  [$object['id']],
		]);
		$rel_posts = [];
		foreach ($wp_query_on_this_topic->posts as $post) {
			preg_match_all('#(?:https?|ftp)://[^\s\,]+#i', get_post_field('video_in_post', $post->ID), $match_video);
			str_replace('"', '', $match_video[0]);


			$post_hashtag = [];
			foreach (get_the_tags($post->ID) as $tags) {
				$post_hashtag[] = "#".$tags->name;

			}
			$rel_posts[] = [
				'ID' 			=> $post->ID,
				'img' 			=> img($post->ID),
				'title' 		=> $post->post_title,
				'date' 			=> $post->post_date,
				'content' 		=> $post->post_content,
				'excerpt' 		=> $post->post_excerpt,
				'views' 		=> get_field('views', $post->ID),
				'comment_count' => $post->comment_count,
				'video' 		=> $match_video[0],
				'post_hashtag'  => $post_hashtag

			];
		}
		return $rel_posts;
	}

	public static function init(){
		self::getPost();
		self::getCategories();
		self::getIssues();
		self::getSpecialTeleproject();
		self::getTags();
		self::getTeleproject();
		self::getTvProgramma();
	}

	private function getPost(){
		add_action('rest_api_init', function (){
			self::setStructureOfAdditionalFields('post');
		});
	}

	private function getCategories()
	{
		add_action('rest_api_init',function () {
			register_rest_route('wp/v2/','categories',['methods' => WP_REST_Server::READABLE,'callback' => function(){
				$answer = [];
				$disallowed = [127,55,335,16348,30601,31139,345,157,349,16713,5226];
				foreach (get_categories(['exclude' => $disallowed]) as $category){
					$answer[] =
						[
							'id'					  => $category->cat_ID,
							'name'					  => $category->name,
							'post_with_thit_category' => $category->parent
						];
				}
				return $answer;
			}]);
		});
	}

	function getIssues()
	{
		add_action( 'rest_api_init', function () {
			register_rest_route( 'wp/v2/', 'issues',             ['methods' => 'GET','callback' => function(){
				$answer = [];

				$wp_query_all_teleprojects = new WP_Query([
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'post_type'      => 'teleproject',
					'orderby'        => 'date',
					'order'          => 'DESC',
				]);
				foreach ($wp_query_all_teleprojects->posts as $teleproject){
					//убрать регулярки
					preg_match('/src="([^"]+)"/', get_field("teleproject_release_video",$teleproject->ID), $match);
					$answer[] =
						[
							'ID'            => $teleproject->ID,
							'img'           => get_the_post_thumbnail_url($teleproject->ID),
							'title'         => $teleproject->post_title,
							'date'          => $teleproject->post_date,
							'content'       => $teleproject->post_content,
							'excerpt'       => $teleproject->post_excerpt,
							'views'         => get_field('views', $teleproject->ID),
							'comment_count' => wp_count_comments($teleproject->ID),
						];
				}
				return $answer;
			}]);
			register_rest_route( 'wp/v2/', 'issues/(?P<id>\d+)', ['methods' => 'GET','callback' => function(WP_REST_Request $request) {
				$answer = [];

				$wp_query_all_teleprojects = new WP_Query([
					'post_status' => 'publish',
					'posts_per_page' => 10,
					'post_type' => 'teleproject_release',
					'meta_query' => [
						'relation' => 'AND',
						[
							'key' => 'teleproject_id_parent',
							'value' => (int)$request['id'],
							'compare' => '=',
						],
					],
				]);

				foreach ($wp_query_all_teleprojects->posts as $teleproject) {
					$answer[] = [
						'ID' => $teleproject->ID,
						'img' => get_the_post_thumbnail_url($teleproject->ID),
						'title' => ($teleproject->post_title),
						'date' => $teleproject->post_date,
						'content' => ($teleproject->post_content),
						'excerpt' => $teleproject->post_excerpt,
						'views' => get_field('views', $teleproject->ID),
						'comment_count' => $teleproject->comment_count,
						'video' => selectLink(get_field("teleproject_release_video", $teleproject->ID)),
					];
				}
				return $answer;

			}]);
		});

			self::setStructureOfAdditionalFields('teleproject_release');

	}

	private function getSpecialTeleproject()
	{

		add_action( 'rest_api_init', function ()
		{
			register_rest_route( 'wp/v2/', 'special', ['methods' => WP_REST_Server::READABLE,'callback' => function() {

				$wp_query_special_project = new WP_Query( [
					'post_status' 		=> 'publish',
					'posts_per_page' 	=> -1,
					'post_type'		    => 'teleproject',
					'meta_query'		=> [
						'relation'		=> 'AND',
						[
							'key'	 	=> 'in_special_project',
							'value'	  	=> true,
							'compare' 	=> '=',
						]
					],
				]);
				$answer = [];
				foreach ($wp_query_special_project->posts as $posty)
				{
					$answer[] = [
						'ID' 					=> $posty->ID,
						'img' 					=> get_the_post_thumbnail_url($posty->ID),
						'title'					=> $posty->post_title,
						'excerpt' 				=> $posty->post_excerpt,
						'content'				=> $posty->post_content,
						'teleproject_main_time' => get_field('teleproject_main_time', $posty->ID),
						'teleproject_day' 		=> get_field('teleproject_day', $posty->ID),
						'views' 				=> get_field('views', $posty->ID)
					];
				}
				return $answer;
			}]);
		});

	}

	private function getTags()
	{
		add_action( 'rest_api_init', function (){
			register_rest_route('wp/v2', '/superTags', ['methods'  => WP_REST_Server::READABLE,'callback' => function(){
				global $wpdb;
				$term_ids = $wpdb->get_col("
				SELECT term_id FROM $wpdb->term_taxonomy
				INNER JOIN $wpdb->term_relationships ON $wpdb->term_taxonomy.term_taxonomy_id=$wpdb->term_relationships.term_taxonomy_id
				INNER JOIN $wpdb->posts ON $wpdb->posts.ID = $wpdb->term_relationships.object_id
				WHERE DATE_SUB(CURDATE(), INTERVAL 1 DAY) <= $wpdb->posts.post_date and $wpdb->term_taxonomy.taxonomy='post_tag'
			");
				$tags = get_tags(
					[
						'orderby' => 'count',
						'order'   => 'DESC',
						'number'  => 15,
						'include' => $term_ids,
					]);
				$superTags = [];
				foreach ( (array) $tags as $tag ) {
					$superTags[] = '#'.$tag->name;
				}
				return $superTags;
			}]);
		});
	}

	private function getTeleproject()
	{
		add_action( 'rest_api_init', function ()
		{
			register_rest_route( 'wp/v2/', 'teleproject_release',['methods' => WP_REST_Server::READABLE,'callback' => function(){
				$wp_query_teleproject_release = new WP_Query([
					'post_status' 	 => 'publish',
					'posts_per_page' => 10,
					'post_type' 	 => 'teleproject_release',
					'meta_key'   	 => 'teleproject_id_parent',
					'paged' 		 => ($_REQUEST['paged'] ? $_REQUEST['paged'] : 1)
				]);
				$answer = [];
				foreach ($wp_query_teleproject_release->posts as $stroke)
				{
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
							'video' 		=> self::cleanLink(get_field("teleproject_release_video",$stroke->ID)),
							'perents' 		=> get_field('teleproject_id_parent',$stroke->ID)
						];
				}
				return $answer;
			}]);
			self::setStructureOfAdditionalFields('teleproject');
		});
	}
	//clean
	private function getTvProgramma()
	{
		add_action( 'rest_api_init', function ()
		{
			register_rest_route( 'wp/v2/', 'tv_program', ['methods' => WP_REST_Server::READABLE,'callback' => function(){
				$current_day_tv_program = date('Ymd');
				$wp_query_tv_program = new WP_Query([
					'post_status' 	 => 'publish',
					'posts_per_page' => 1,
					'post_type' 	 =>  'tv_program',
					'meta_query'	 => [
						'relation'		=> 'AND',
						[
							'key'	 	=> 'tv_program_start_date',
							'compare' 	=> '<=',
							'value'	  	=>  $current_day_tv_program,
						],
						[
							'key'	 	=> 'tv_program_end_date',
							'compare' 	=> '>=',
							'value'	  	=>  $current_day_tv_program,
						],
					],
				]);

				$tele =  [];
				$tele_2 =  [];
				$valoftv = 0;

				while ( $wp_query_tv_program->have_posts()) {
					$wp_query_tv_program->the_post();
					$wp_query_tv_program->post;
					if(have_rows('tv_program_items')){
						$counter_tv_program=0;
						while ( have_rows('tv_program_items')){
							the_row();

							$counter_tv_program++;


							$tele[] = get_sub_field('tv_program_item_title').' '.get_sub_field('tv_program_item_date');
							if(have_rows('tv_program_item_program')) {
								while ( have_rows('tv_program_item_program') ) {
									the_row();
									$programma = get_sub_field('tv_program_item_program_date').'|';
									if(get_sub_field('tv_program_item_program_link')) {
										$programma .= get_sub_field('tv_program_item_program_title').'|'.get_the_permalink(get_sub_field('tv_program_item_program_link')).'|';
									}
									else {
										$programma .= get_sub_field('tv_program_item_program_title').'|';
									}

									if (get_sub_field('tv_program_item_program_age')) {
										$programma .= get_sub_field('tv_program_item_program_age').'+';
									}
									$programma = explode('|',$programma);
									if(strlen($programma[2]) == 3)
									{
										$programma = [
											'time'  => $programma[0],
											'title' => $programma[1],
											'age'   => $programma[2],
											'link'  => $programma[3]
										];
									}
									else
									{
										$programma = [
											'time'  => $programma[0],
											'title' => ($programma[1]),
											'age'   => $programma[3],
											'link'  => $programma[2]
										];
									}
									$tele[$tele[$valoftv]][] = $programma;
									$tele_2[$tele[$valoftv]][] = $programma;
								}
								$valoftv++;
							}
						}
					}
				}
				return $tele_2;
			}]);
		});
	}
}