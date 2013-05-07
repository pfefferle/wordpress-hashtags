<?php
/*
 Plugin Name: Hashtags
 Plugin URI: http://github.com/pfefferle/wordpress-hashtags
 Description: Adds twitter like hashtags to your blog-posts
 Author: Matthias Pfefferle
 Author URI: http://notizblog.org/
 Version: 1.0.0
*/

define( "HASHTAGS_REGEXP" ,  "(^|\s|>)#([^\s<>]+)\b" );

/**
 * filter to save #tags as real wordpress tags
 * 
 * @param int $id the rev-id
 * @param array $data the post-data as array
 */
function hashtags_insert_post( $id, $data ) {
  if (preg_match_all("/".HASHTAGS_REGEXP."/i", $data->post_content, $match)) {
    $tags = implode(", ", $match[2]);

    wp_add_post_tags( $data->post_parent, $tags );
  }
  
  return $id;
}
add_filter( 'wp_insert_post', 'hashtags_insert_post', 99, 2 );

/**
 * filter to replace the #tags in the content with links
 * 
 * @param string $the_content the post-content
 */
function hashtags_the_content( $the_content ) {
  $the_content = preg_replace_callback("/".HASHTAGS_REGEXP."/i", "_hashtags_replace_with_links", $the_content);

  return $the_content;
}
add_filter( 'the_content', 'hashtags_the_content', 99, 2 );

/**
 * a callback for preg_replace to build the term links
 *
 * @param array $result the preg_match results
 * @return string the final string
 */
function _hashtags_replace_with_links( $result ) {
  $tag = $result[2];
  $space = $result[1];
  $tag_object = get_term_by("name", $result[2], "post_tag");
  
  if ($tag_object) {
    $link = get_term_link($tag_object, "post_tag");
    return "$space<a href='$link' rel='tag'>#$tag</a>";
  }  
  
  return $space."#".$tag;
}