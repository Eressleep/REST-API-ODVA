<?php
//улучить запросы
function odvaTvProgramma()
{
	function get_tv_program(){

		$wp_query_tv_program = new WP_Query([
			'post_status' => 'publish',
			'posts_per_page' => 1,
			'post_type' =>  'tv_program',
			'meta_query'	=> [
				'relation'		=> 'AND',
				[
					'key'	 	=> 'tv_program_start_date',
					'compare' 	=> '<=',
					'value'	  	=>  date('Ymd'),
				],
				[
					'key'	 	=> 'tv_program_end_date',
					'compare' 	=> '>=',
					'value'	  	=>  date('Ymd'),
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
									'title' => $programma[1],
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
