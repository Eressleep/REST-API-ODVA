<?
function link_youtube($object)
{
	preg_match_all('#(?:https?|ftp)://[^\s\,]+#i', get_post_field('video_in_post', $object['id']), $matches);
	return str_replace('"', '', $matches[0])[0];
}
function categories($object)
{
	return  wp_get_post_categories($object['id']);
}
function content($object)
{
	return $object['content']['rendered'];
}
function excerpt($object)
{
	return $object['excerpt']['rendered'];
}
function hashtag($object)
{
	$arr = [];
	foreach (get_the_tags($object['id']) as $tags)
	{
		$arr[] = "#".$tags->name;

	}
	return $arr;
}
function img($object)
{
	return get_the_post_thumbnail_url($object['id']);
}
function related($object){
	$tags_post = get_the_tags( $object['id']);
	$tags_array = array();

	foreach ($tags_post as $tags_post_item) {
		array_push($tags_array, $tags_post_item->term_id);
	}
	$args_on_this_topic = array(
		'post_status' => 'publish',
		'posts_per_page' => 3,
		'tag__in' => $tags_array,
		'post__not_in' =>  array( $object['id']),
	);
	$wp_query_on_this_topic = new WP_Query($args_on_this_topic);
	$rel_posts = [];
	foreach ($wp_query_on_this_topic->posts as $post)
	{
		preg_match_all('#(?:https?|ftp)://[^\s\,]+#i', get_post_field('video_in_post', $post->ID), $match_video);
		str_replace('"', '', $match_video[0]);
		$post_hashtag = [];
		foreach (get_the_tags($post->ID) as $tags)
		{
			$post_hashtag[] = "#".$tags->name;

		}
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
function odvaPost()
{
	add_action('rest_api_init','postProperties');
	function postProperties()
	{
		//desired attributes
		register_rest_field( 'post', 'youtube',    ['get_callback' => 'link_youtube','schema' => null,]);
		register_rest_field( 'post', 'categories', ['get_callback' => 'categories',  'schema' => null,]);
		register_rest_field( 'post', 'content',    ['get_callback' => 'content',     'schema' => null,]);
		register_rest_field( 'post', 'excerpt',    ['get_callback' => 'excerpt',     'schema' => null,]);
		register_rest_field( 'post', 'hashtag',    ['get_callback' => 'hashtag',     'schema' => null,]);
		register_rest_field( 'post', 'img',        ['get_callback' => 'img',         'schema' => null,]);
		register_rest_field( 'post', 'related',    ['get_callback' => 'related',     'schema' => null,]);
		register_rest_field( 'post', 'title',      ['get_callback' => 'title',       'schema' => null,]);
		register_rest_field( 'post', 'views',      ['get_callback' => 'views',       'schema' => null,]);
		register_rest_field( 'post', 'views',      ['get_callback' => 'views',       'schema' => null,]);

	}


}