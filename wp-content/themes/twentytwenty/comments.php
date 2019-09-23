<?php
/**
 * The template file for displaying the comments and comment form for the
 * Twenty Twenty theme.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since 1.0.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
*/
if ( post_password_required() ) {
	return;
}

if ( $comments ) {
	?>

	<div class="comments" id="comments">

		<?php
		$comments_number = absint( get_comments_number() );
		?>

		<div class="comments-header section-inner small max-percentage">

			<h2 class="comment-reply-title">
			<?php
			if ( ! have_comments() ) {
				esc_html_e( 'Leave a comment', 'twentytwenty' );
			} elseif ( '1' === $comments_number ) {
				/* translators: %s: post title */
				printf( esc_html_x( 'One reply on &ldquo;%s&rdquo;', 'comments title', 'twentytwenty' ), esc_html( get_the_title() ) );
			} else {
				echo esc_html(
					sprintf(
						/* translators: 1: number of comments, 2: post title */
						_nx(
							'%1$s reply on &ldquo;%2$s&rdquo;',
							'%1$s replies on &ldquo;%2$s&rdquo;',
							$comments_number,
							'comments title',
							'twentytwenty'
						),
						number_format_i18n( $comments_number ),
						get_the_title()
					)
				);
			}

			?>
			</h2><!-- .comments-title -->

		</div><!-- .comments-header -->

		<div class="comments-inner section-inner thin max-percentage">

			<?php

			wp_list_comments(
				array(
					'walker'      => new TwentyTwenty_Walker_Comment(),
					'avatar_size' => 120,
					'style'       => 'div',
				)
			);

			$comment_pagination = paginate_comments_links(
				array(
					'echo'      => false,
					'end_size'  => 0,
					'mid_size'  => 0,
					'next_text' => __( 'Newer Comments', 'twentytwenty' ) . ' &rarr;',
					'prev_text' => '&larr; ' . __( 'Older Comments', 'twentytwenty' ),
				)
			);

			if ( $comment_pagination ) {

				// If we're only showing the "Next" link, add a class indicating so.
				if ( strpos( $comment_pagination, 'prev page-numbers' ) === false ) {
					$pagination_classes = ' only-next';
				} else {
					$pagination_classes = '';
				}
				?>

				<nav class="comments-pagination pagination<?php echo esc_attr( $pagination_classes ); ?>" aria-label="<?php esc_attr_e( 'Comments', 'twentytwenty' ); ?>">
					<?php echo wp_kses_post( $comment_pagination ); ?>
				</nav>

				<?php
			}
			?>

		</div><!-- .comments-inner -->

	</div><!-- comments -->

	<?php
}

if ( comments_open() || pings_open() ) {

	if ( $comments ) {
		echo '<hr class="is-style-wide" aria-hidden="true" />';
	}

	comment_form(
		array(
			'class_form'         => 'section-inner thin max-percentage',
			'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
			'title_reply_after'  => '</h2>',
		)
	);

} elseif ( is_single() ) {

	if ( $comments ) {
		echo '<hr class="is-style-wide" aria-hidden="true" />';
	}

	?>

	<div class="comment-respond" id="respond">

		<p class="comments-closed"><?php _e( 'Comments are closed.', 'twentytwenty' ); // phpcs:ignore WordPress.Security.EscapeOutput.UnsafePrintingFunction -- core trusts translations ?></p>

	</div><!-- #respond -->

	<?php
}
