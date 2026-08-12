<?php
/**
 * Blog Template — Child Theme Override
 * wp-content/themes/shoppingcart-child/home.php
 */
get_header();
?>

<style>
.blog .fullwidth-blog,
.blog .blog-articles-grid,
.blog .blog-list,
.blog #primary .post.hentry,
.archive .fullwidth-blog,
.archive .blog-articles-grid,
.archive .blog-list,
.archive #primary .post.hentry {
	display: none !important;
}

.blog #page, .blog #content,
.archive #page, .archive #content {
	background-color: #ffffff;
}

.werdu-blog-wrap {
	max-width: 1300px;
	margin: 0 auto;
	padding: 40px 20px 60px;
	background: #fff;
	min-height: 50vh;
}

.werdu-blog-titel {
	text-align: center;
	font-size: 32px;
	font-weight: 700;
	color: #1a1a2e;
	margin-bottom: 8px;
}

.werdu-blog-sub {
	text-align: center;
	color: #666;
	font-size: 16px;
	margin-bottom: 40px;
}

/* Neuestes Artikel */
.werdu-featured {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 0;
	margin-bottom: 45px;
	background: #fff;
	border-radius: 12px;
	overflow: hidden;
	box-shadow: 0 4px 20px rgba(0,0,0,0.08);
	border: 1px solid #eee;
}

.werdu-featured-img {
	min-height: 320px;
	background-size: cover;
	background-position: center;
	position: relative;
}

.werdu-featured-badge {
	position: absolute;
	top: 20px;
	left: 20px;
	background: #ff6600;
	color: #fff;
	padding: 8px 16px;
	border-radius: 4px;
	font-size: 12px;
	font-weight: 700;
	text-transform: uppercase;
	letter-spacing: 0.5px;
}

.werdu-featured-body {
	padding: 35px;
	display: flex;
	flex-direction: column;
	justify-content: center;
}

.werdu-featured-cat {
	color: #ff6600;
	font-size: 12px;
	font-weight: 600;
	text-transform: uppercase;
	letter-spacing: 1px;
	margin-bottom: 10px;
}

.werdu-featured-title {
	font-size: 24px;
	font-weight: 700;
	color: #1a1a2e;
	line-height: 1.3;
	margin-bottom: 12px;
}

.werdu-featured-title a {
	color: #1a1a2e;
	text-decoration: none;
}

.werdu-featured-title a:hover {
	color: #ff6600;
}

.werdu-featured-excerpt {
	font-size: 15px;
	color: #555;
	line-height: 1.7;
	margin-bottom: 18px;
}

.werdu-featured-meta {
	font-size: 13px;
	color: #888;
}

.werdu-featured-cta {
	display: inline-block;
	margin-top: 18px;
	background: #ff6600;
	color: #fff;
	padding: 12px 26px;
	border-radius: 6px;
	font-size: 14px;
	font-weight: 600;
	text-decoration: none;
}

.werdu-featured-cta:hover {
	background: #e55a00;
}

/* Tegels 3x3 */
.werdu-grid {
	display: grid;
	grid-template-columns: repeat(3, 1fr);
	gap: 25px;
	margin-bottom: 40px;
}

.werdu-card {
	background: #fff;
	border-radius: 10px;
	overflow: hidden;
	box-shadow: 0 2px 12px rgba(0,0,0,0.06);
	border: 1px solid #eee;
	transition: transform 0.3s, box-shadow 0.3s;
}

.werdu-card:hover {
	transform: translateY(-4px);
	box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.werdu-card-img {
	height: 180px;
	background-size: cover;
	background-position: center;
	position: relative;
}

.werdu-card-cat {
	position: absolute;
	bottom: 12px;
	left: 12px;
	background: rgba(26,26,46,0.85);
	color: #fff;
	padding: 5px 12px;
	border-radius: 3px;
	font-size: 11px;
	font-weight: 600;
	text-transform: uppercase;
}

.werdu-card-body {
	padding: 18px;
}

.werdu-card-title {
	font-size: 16px;
	font-weight: 600;
	color: #1a1a2e;
	line-height: 1.4;
	margin-bottom: 8px;
}

.werdu-card-title a {
	color: #1a1a2e;
	text-decoration: none;
}

.werdu-card-title a:hover {
	color: #ff6600;
}

.werdu-card-meta {
	font-size: 12px;
	color: #888;
}

/* Pagination */
.werdu-pagi {
	text-align: center;
	padding: 20px 0;
}

.werdu-pagi a,
.werdu-pagi span {
	display: inline-block;
	padding: 10px 16px;
	margin: 0 4px;
	background: #fff;
	border: 1px solid #ddd;
	border-radius: 5px;
	color: #333;
	text-decoration: none;
	font-size: 14px;
}

.werdu-pagi a:hover,
.werdu-pagi span.current {
	background: #ff6600;
	color: #fff;
	border-color: #ff6600;
}

/* Mobile */
@media (max-width: 768px) {
	.werdu-featured {
		grid-template-columns: 1fr;
	}
	.werdu-featured-img {
		min-height: 200px;
	}
	.werdu-grid {
		grid-template-columns: 1fr;
	}
	.werdu-blog-titel {
		font-size: 24px;
	}
}
</style>

<div class="werdu-blog-wrap">

<?php
$args = array(
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => 10,
	'paged'          => get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1,
	'orderby'        => 'date',
	'order'          => 'DESC',
);

$q = new WP_Query( $args );

if ( $q->have_posts() ) :

	$posts = $q->posts;
	$first = array_shift( $posts );
	$rest  = array_slice( $posts, 0, 9 );
?>

<h1 class="werdu-blog-titel">ACC Solarspeicher Blog</h1>
<p class="werdu-blog-sub">Expertenwissen zu LiFePO4, Solarbatterien, Heimspeicher &amp; Eigenverbrauch</p>

<?php
// === NEUESTES ARTIKEL ===
$fcats = get_the_category( $first->ID );
$fcat  = ! empty( $fcats ) ? $fcats[0]->name : 'Allgemein';
$fimg  = get_the_post_thumbnail_url( $first->ID, 'large' );
$fdat  = get_the_date( 'j. F Y', $first->ID );
$fread = max( 1, ceil( str_word_count( strip_tags( $first->post_content ) ) / 200 ) );
$fexc  = wp_trim_words( $first->post_content, 35, '...' );
?>

<article class="werdu-featured">
	<div class="werdu-featured-img" style="<?php echo $fimg ? 'background-image:url(' . esc_url( $fimg ) . ');' : 'background:#f3f4f8;'; ?>">
		<span class="werdu-featured-badge">Neuestes Artikel</span>
	</div>
	<div class="werdu-featured-body">
		<div class="werdu-featured-cat"><?php echo esc_html( $fcat ); ?></div>
		<h2 class="werdu-featured-title">
			<a href="<?php echo esc_url( get_permalink( $first->ID ) ); ?>"><?php echo esc_html( $first->post_title ); ?></a>
		</h2>
		<div class="werdu-featured-excerpt"><?php echo esc_html( $fexc ); ?></div>
		<div class="werdu-featured-meta">
			<?php echo esc_html( $fdat ); ?> &bull; <?php echo esc_html( $fread ); ?> Min. Lesezeit
		</div>
		<a href="<?php echo esc_url( get_permalink( $first->ID ) ); ?>" class="werdu-featured-cta">Jetzt lesen</a>
	</div>
</article>

<?php if ( ! empty( $rest ) ) : ?>
<div class="werdu-grid">
<?php foreach ( $rest as $p ) :
	$pcats = get_the_category( $p->ID );
	$pcat  = ! empty( $pcats ) ? $pcats[0]->name : 'Allgemein';
	$pimg  = get_the_post_thumbnail_url( $p->ID, 'medium' );
	$pdat  = get_the_date( 'j. F Y', $p->ID );
?>
	<article class="werdu-card">
		<div class="werdu-card-img" style="<?php echo $pimg ? 'background-image:url(' . esc_url( $pimg ) . ');' : 'background:#f3f4f8;'; ?>">
			<span class="werdu-card-cat"><?php echo esc_html( $pcat ); ?></span>
		</div>
		<div class="werdu-card-body">
			<h3 class="werdu-card-title">
				<a href="<?php echo esc_url( get_permalink( $p->ID ) ); ?>"><?php echo esc_html( wp_trim_words( $p->post_title, 8, '...' ) ); ?></a>
			</h3>
			<div class="werdu-card-meta"><?php echo esc_html( $pdat ); ?></div>
		</div>
	</article>
<?php endforeach; ?>
</div>
<?php endif; ?>

<?php if ( $q->max_num_pages > 1 ) : ?>
<div class="werdu-pagi">
<?php
echo paginate_links( array(
	'base'      => str_replace( 999999, '%#%', esc_url( get_pagenum_link( 999999 ) ) ),
	'format'    => '?paged=%#%',
	'current'   => max( 1, get_query_var( 'paged' ) ),
	'total'     => $q->max_num_pages,
	'prev_text' => 'Zurueck',
	'next_text' => 'Weiter',
) );
?>
</div>
<?php endif; ?>

<?php else : ?>
	<p style="text-align:center;padding:60px 20px;color:#888;">Keine Artikel gefunden.</p>
<?php endif; ?>

<?php wp_reset_postdata(); ?>

</div>

<?php get_footer(); ?>