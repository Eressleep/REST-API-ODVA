<?php
//additional field functions
//mv to class
function link_youtube($object){
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
}
function categories($object){
	return  wp_get_post_categories($object['id']);
}
function content($object){
	return strip_tags($object['content']['rendered']);
}
function contentWithoutTags($object){
	return $object['content']['rendered'];
}
function excerpt($object){
	return strip_tags($object['excerpt']['rendered']);
}
function hashtag($object){
	$hashtags = [];
	foreach (get_the_tags($object['id']) as $tags){
		$hashtags[] = "#".$tags->name;
	}
	return $hashtags;
}
function img($object){
	return get_the_post_thumbnail_url($object['id']);
}
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
		$link_video = '';
		$str = get_post_field('video_in_post', $post->ID);
		$len = strlen($str);
		$flag = false;
		for ($i = 0; $i < $len - 4; $i++){
			if($str[$i].$str[$i+1].$str[$i+2].$str[$i+3] == 'rc="' || $flag == true){
				if($str[$i+4].$str[$i+5] == '" ')
					break;

				$link_video .= $str[$i+4];
				$flag = true;
			}
		}


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
			'video' 		=> $link_video,
			'post_hashtag'  => $post_hashtag

		];
	}
	return $rel_posts;
}
function title($object){
	return $object['title']['rendered'];
}
function views($object){
	return get_field('views',$object['id']);
}
function comments_count($object){
	return wp_count_comments($object['id'])->total_comments;
}
function id($object){
	return $object['id'];
}
function video($object){
	$ans = '';
	$str = get_field("teleproject_release_video",$object['id']);
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
