<?php
function odvaTags()
{
	function tagstoday() {
		global $wpdb;
		$term_ids = $wpdb->get_col("
				SELECT term_id FROM $wpdb->term_taxonomy
				INNER JOIN $wpdb->term_relationships ON $wpdb->term_taxonomy.term_taxonomy_id=$wpdb->term_relationships.term_taxonomy_id
				INNER JOIN $wpdb->posts ON $wpdb->posts.ID = $wpdb->term_relationships.object_id
				WHERE DATE_SUB(CURDATE(), INTERVAL 1 DAY) <= $wpdb->posts.post_date and $wpdb->term_taxonomy.taxonomy='post_tag'
			");
		$tags = get_tags(array(
			'orderby' => 'count',
			'order'   => 'DESC',
			'number'  => 10,
			'include' => $term_ids,
		));
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