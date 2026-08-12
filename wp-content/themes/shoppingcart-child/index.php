<?php
get_header(); ?>

<div id="content" class="fullwidth-blog">

    <?php if ( have_posts() ) : ?>
        <div class="blog-articles-grid">
            <?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('post'); ?>>

                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="post-image-content">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium'); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="post-all-content">
                        <header class="entry-header">
                            <h2 class="entry-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            <div class="entry-meta">
                                <span class="posted-on">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php echo get_the_date(); ?>
                                    </a>
                                </span>
                            </div>
                        </header>

                        <div class="entry-summary">
                            <?php the_excerpt(); ?>
                        </div>
                    </div>

                </article>
            <?php endwhile; ?>
        </div> <!-- .blog-articles-grid -->

        <div class="pagination">
            <?php the_posts_pagination(); ?>
        </div>

    <?php endif; ?>

</div> <!-- #content fullwidth-blog -->

<?php get_footer(); ?>