<?php
/*
 Plugin Name: Hashtags Extractor
 Plugin URI: http://github.com/artlung/wordpress-hashtags
 Description: Adds hashtags to the post editor as tags when found in title and post content
 Author: Joe Crawford
 Author URI: http://artlung.com/
 Version: 0.0.1
*/

define( "HASHTAGS_REGEXP" ,  "(^|\s|>)#([^\s<>]+)\b" );

// Called on admin edit.
add_filter( 'terms_to_edit', function ( $terms_to_edit, $taxonomy ) {
    global $post;
    if ( ! isset( $post->ID ) || $taxonomy != 'post_tag' || ! $terms_to_edit ) {
        return $terms_to_edit;
    }
    $tags_to_edit = explode(',', $terms_to_edit);

    if (preg_match_all("/".HASHTAGS_REGEXP."/i", $post->post_content, $match)) {
        $tags = implode(", ", $match[2]);
        $tags_to_edit = array_merge($tags_to_edit, explode(", ", $tags));
    }
    if (preg_match_all("/".HASHTAGS_REGEXP."/i", $post->post_title, $match)) {
        $tags = implode(", ", $match[2]);
        $tags_to_edit = array_merge($tags_to_edit, explode(", ", $tags));
    }
    array_map('strtolower', $tags_to_edit);
    $tags_to_edit = array_unique($tags_to_edit);
    return implode(',', $tags_to_edit);
}, 10, 2 );

