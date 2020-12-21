<?
function odvaPost()
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