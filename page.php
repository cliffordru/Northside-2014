<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header(); ?>

<div class="socialmediabuttons">
<a href="http://www.facebook.com/northsidefest" target="blank"><img class="smpop" style="padding-right: 3px;" src="http://northsidefestival.com/wp-content/uploads/2013/01/facebook-NSF.png"/></a><a href="http://www.twitter.com/northsidefest" target="blank"><img class="smpop" style="padding-right: 3px;" src="http://northsidefestival.com/wp-content/uploads/2013/01/twitter-nsf.png"/></a><a href="http://instagram.com/thelmagazine" class="ig-b- ig-b-48" target="blank"><img class="smpop" style="padding-right: 3px;" src="http://www.northsidefestival.com/wp-content/uploads/2013/01/instagram-NSF.png" width="38"/></a>
</div>

<div style="left: 180px; margin-bottom: 0; margin-top: 0; padding-bottom: 0; position: relative; top: -330px; width: 175px;" class="fblike"><iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2FNorthsideFest&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=false&amp;font&amp;colorscheme=light&amp;action=like&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:21px;" allowTransparency="true"></iframe></div>

		<div id="primary"> 
			<div id="content" role="main">

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', 'page' ); ?>

					<?php comments_template( '', true ); ?>

				<?php endwhile; // end of the loop. ?>

			</div><!-- #content -->
<?php get_footer(); ?>

		</div><!-- #primary -->

