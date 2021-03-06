<?php

/**
 * Twenty Eleven functions and definitions
 */


/**
 * Display navigation to next/previous pages when applicable
 */
function my_nav() {
	global $wp_query;

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav class="my-nav">
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> 이전', 'twentyeleven' ) ); ?></div>
			<div class="nav-next"><?php previous_posts_link( __( '다음 <span class="meta-nav">&rarr;</span>', 'twentyeleven' ) ); ?></div>
		</nav><!-- #nav-above -->
	<?php endif;
}


if ( ! function_exists( 'twentyeleven_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own twentyeleven_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( '여기를 링크한 문서:', 'twentyeleven' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					<?php
						$avatar_size = 68;
						if ( '0' != $comment->comment_parent )
							$avatar_size = 39;

						echo get_avatar( $comment, $avatar_size );

						/* translators: 1: comment author, 2: date and time */
						printf( __( '%1$s on %2$s <span class="says">said:</span>', 'twentyeleven' ),
							sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
							sprintf( '<a href="%1$s"><time pubdate datetime="%2$s">%3$s</time></a>',
								esc_url( get_comment_link( $comment->comment_ID ) ),
								get_comment_time( 'c' ),
								/* translators: 1: date, 2: time */
								sprintf( __( '%1$s at %2$s', 'twentyeleven' ), get_comment_date(), get_comment_time() )
							)
						);
					?>

					<?php edit_comment_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>
				</div><!-- .comment-author .vcard -->

				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'twentyeleven' ); ?></em>
					<br />
				<?php endif; ?>

			</footer>

			<div class="comment-content"><?php comment_text(); ?></div>

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply <span>&darr;</span>', 'twentyeleven' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->

	<?php
			break;
	endswitch;
}
endif; // ends check for twentyeleven_comment()

/**
 * 사용자 정의 분류 추가
 *
 * @author Jeonghwan
 * @since 2013. 9. 11.
 */
function create_my_taxonomies() {
	register_taxonomy('book_author', 'post', 
		array('hierarchial'=>false, /* false:tag, true: category */
			  'label'=>'지은이',
			  'query_var'=>true, 
			  'rewrite'=>true));
	register_taxonomy('publisher', 'post', 
		array('hierarchial'=>false, 
			  'label'=>'출판사',
			  'query_var'=>true, 
			  'rewrite'=>true));
}
add_action('init', 'create_my_taxonomies', 0);


/**
 * 저자정보가져오기 
 * @since 2013. 9. 13. 
 */
function get_book_author($postID) 
{
	return get_the_term_list( $postID, 'book_author', '', ', ', '' ); 
}

 
/**
 * 출판사 이름 가져오기 
 * @since 2013. 9. 13. 
 */
function get_book_publisher($postID) 
{
	return get_the_term_list( $postID, 'publisher', '', ', ', '' ); 
}


/**
 * 에디터 스타일 시트 로딩
 * @since 2013. 9. 16. 
 */
add_editor_style();


/**
 * 위키api호출
 * 입력: 위키 키워드
 *      option: article
 * 출력: 위키 본문 (설명만)
 */
// function get_wiki_content($keyword) {
// 	$keyword = urlencode($keyword);
// 	$query = "http://ko.wikipedia.org/w/api.php?action=parse&page=$keyword&format=json";
// 	$result = "";

// 	// api 호출
// 	$wiki = json_decode( file_get_contents($query) );

// 	// 에러처리
// 	if (isset($wiki->error)) return $result;

// 	// html 얻기
// 	$html = "";
// 	$wiki = $wiki->parse->text;
// 	foreach ($wiki as $key => $value) {
// 		if ($key == "*") {
// 			$html = $value;
// 			break;
// 		}
// 	}
// 	if ($html == "") return $result;

// 	// <p> 태그만 추출
// 	require_once ('simple_html_dom.php');
// 	$html = str_get_html($html);
// 	foreach ($html->find('p') as $elem) {
// 		$result = $result . ' ' . $elem->innertext;
// 	}
// 	if ($result == "") return $result;

// 	// 링크 제거
// 	$result = str_get_html($result);
// 	foreach ($result->find('a') as $elem) {
// 		$elem->href = null;
// 	}
// 	if ($result == "") return $result;

// 	// 결과 길이 제한
// 	if (strlen($result) > 1000) {
// 		$result = substr($result, 0, 1000);

// 		$last_space_pos = strrpos($result, ' ');
// 		$result = substr($result, 0, $last_space_pos);
// 		$result .= '......';
// 	}

// 	return $result;
// }
