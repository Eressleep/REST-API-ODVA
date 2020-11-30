<?php
function odvaTvProgramma()
{
	function get_tv_program(){
		$current_day_tv_program = date('Ymd');

		$args_tv_program = [
			'post_status' => 'publish',
			'posts_per_page' => 1,
			'post_type' =>  'tv_program',
			'meta_query'	=> [
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
		];

		$wp_query_tv_program = new WP_Query($args_tv_program);

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

					$tv_program_item_date = get_sub_field('tv_program_item_date');

					$class_tab_panel="panel panel_cont panel-default";
					$class_active_panel_collapse="";

					if($tv_program_item_date == date("d.m.Y")) {
						$class_tab_panel="panel panel_cont_active panel-default";
						$class_active_panel_collapse="in";
					}
					else if ($tv_program_item_date < date("d.m.Y")) {
						$class_tab_panel="panel panel_cont_past panel-default";
					}
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
									'title' => ($programma[1]),
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
		register_rest_route( 'wp/v2/', 'tv_program', ['methods' => 'GET','callback' => 'get_tv_program',]);
	});
}
