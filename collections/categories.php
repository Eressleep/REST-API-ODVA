<?php

class odva{
	static  public function test(){
		return test;
	}
}

function odvaCategories(){
	function allCategories(){
		$categories = [];
		//ос категории
		$disallowed = [127,55,335,16348,30601,31139,345,157,349,16713,5226];

		foreach (get_categories(['exclude' => $disallowed]) as $category){
			$categories[] =
				[
					'id'                      => $category->cat_ID,
					'name'                    => $category->name,
					'post_with_thit_category' => $category->parent
				];
		}
		return $categories;
	}

	add_action('rest_api_init',function (){
		register_rest_route('wp/v2/','categories',['methods' => WP_REST_Server::READABLE,'callback' => 'allCategories',]);
	});
}