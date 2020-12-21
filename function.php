<?php
//additional field functions
function link_youtube($object){
	//убрать регулярки
	preg_match_all('#(?:https?|ftp)://[^\s\,]+#i', get_post_field('video_in_post', $object['id']), $matches);
	return str_replace('"', '', $matches[0])[0];
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
	$answer = [];
	foreach (get_the_tags($object['id']) as $tags)
		$answer[] = "#".$tags->name;

	return $answer;
}
function img($object){
	return get_the_post_thumbnail_url($object['id']);
}
function related($object){

	$tags_array = [];
	$tags_post = get_the_tags( $object['id']);

	foreach ($tags_post as $tags_post_item)
		$tags_array[] = $tags_post_item->term_id;


	$wp_query_on_this_topic = new WP_Query([
		'post_status' => 'publish',
		'posts_per_page' => 3,
		'tag__in' => $tags_array,
		'post__not_in' =>  [$object['id']],
	]);

	$rel_posts = [];
	foreach ($wp_query_on_this_topic->posts as $post)
	{
		//убрать регулярки
		preg_match_all('#(?:https?|ftp)://[^\s\,]+#i', get_post_field('video_in_post', $post->ID), $match_video);
		str_replace('"', '', $match_video[0]);
		$post_hashtag = [];
		foreach (get_the_tags($post->ID) as $tags)
			$post_hashtag[] = "#".$tags->name;

		$rel_posts[] = [
			'ID' => $post->ID,
			'img' => img($post->ID),
			'title' => $post->post_title,
			'date' => $post->post_date,
			'content' => $post->post_content,
			'excerpt' => $post->post_excerpt,
			'views' => get_field('views', $post->ID),
			'comment_count' => $post->comment_count,
			'video' => $match_video[0],
			'post_hashtag' => $post_hashtag
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
	//убрать регулярки
	preg_match('/src="([^"]+)"/', get_field("teleproject_release_video",$object['id']), $match);
	return $match[1];
}
