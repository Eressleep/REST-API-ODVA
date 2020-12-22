<?php
	class collections{
		public function odvaPost(){
			add_action('rest_api_init','postProperties');

			function postProperties(){
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

	}
