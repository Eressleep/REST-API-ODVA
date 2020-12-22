<?php
class collectionsForAPI{
	//additional field functions

	public static function init(){
		self::odvaPost();
		self::odvaCategories();
		self::odvaIssues();
		self::odvaSpecialTeleproject();
		self::odvaTags();
		self::odvaTeleproject();
		self::odvaTvProgramma();
	}

	public function odvaPost()
	{
		add_action('rest_api_init','postProperties');

		function postProperties()
		{
			//desired attributes
			register_rest_field( 'post', 'youtube',    ['get_callback' => 'link_youtube',      'schema' => null,]);
			register_rest_field( 'post', 'categories', ['get_callback' => 'categories',        'schema' => null,]);
			register_rest_field( 'post', 'content',    ['get_callback' => 'contentWithoutTags','schema' => null,]);
			register_rest_field( 'post', 'excerpt',    ['get_callback' => 'excerpt',           'schema' => null,]);
			register_rest_field( 'post', 'hashtag',    ['get_callback' => 'hashtag',           'schema' => null,]);
			register_rest_field( 'post', 'img',        ['get_callback' => 'img',               'schema' => null,]);
			register_rest_field( 'post', 'related',    ['get_callback' => 'related',           'schema' => null,]);
			register_rest_field( 'post', 'title',      ['get_callback' => 'title',             'schema' => null,]);
			register_rest_field( 'post', 'views',      ['get_callback' => 'views',             'schema' => null,]);
			register_rest_field( 'post', 'views',      ['get_callback' => 'views',             'schema' => null,]);

		}
	}

	public function odvaCategories()
	{
		function allCategories()
		{
			$str = [];
			$disallowed = [127,55,335,16348,30601,31139,345,157,349,16713,5226];
			$argv = ['exclude' => $disallowed];
			foreach (get_categories($argv) as $category){
				$str[] =
					[
						'id'					  => $category->cat_ID,
						'name'					  => $category->name,
						'post_with_thit_category' => $category->parent
					];
			}
			return $str;
		}

		add_action('rest_api_init',function ()
		{
			register_rest_route('wp/v2/','categories',['methods' => WP_REST_Server::READABLE,'callback' => 'allCategories',]);
		});
	}

	public function odvaIssues()
	{
		function issues()
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
				//Replace regular expressions
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

			$args_teleproject_release_filter = [
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
			];

			$wp_query_all_teleprojects = new WP_Query($args_teleproject_release_filter);
			$answer = [];
			foreach ($wp_query_all_teleprojects->posts as $teleproject)
			{
				//Replace regular expressions
				preg_match('/src="([^"]+)"/', get_field("teleproject_release_video",$teleproject->ID), $match);
				$answer[] = [
					'ID' 			=> $teleproject->ID,
					'img' 			=> get_the_post_thumbnail_url($teleproject->ID),
					'title' 		=> ($teleproject->post_title),
					'date' 			=> $teleproject->post_date,
					'content' 		=> ($teleproject->post_content),
					'excerpt' 		=> $teleproject->post_excerpt,
					'views' 		=> get_field('views', $teleproject->ID),
					'comment_count' => $teleproject->comment_count,
					'video' 		=> $match[1]
				];
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

	public function odvaSpecialTeleproject()
	{
		function special() {

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
			$arr = [];
			foreach ($wp_query_special_project->posts as $posty)
			{
				$arr[] = [
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
			return $arr;
		}
		add_action( 'rest_api_init', function ()
		{
			register_rest_route( 'wp/v2/', 'special', ['methods' => WP_REST_Server::READABLE,'callback' => 'special',]);
		});

	}

	public function odvaTags()
	{
		function tagstoday() {
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
		}
		function tags_today() {
			register_rest_route('wp/v2', '/superTags', ['methods'  => WP_REST_Server::READABLE,'callback' => 'tagstoday',]);
		}
		add_action( 'rest_api_init', 'tags_today' );
	}

	public function odvaTeleproject()
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

	public function odvaTvProgramma()
	{
		function get_tv_program(){
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
		}
		add_action( 'rest_api_init', function ()
		{
			register_rest_route( 'wp/v2/', 'tv_program', ['methods' => WP_REST_Server::READABLE,'callback' => 'get_tv_program',]);
		});
	}
}