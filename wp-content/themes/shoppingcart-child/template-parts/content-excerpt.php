<?php
$shoppingcart_settings = shoppingcart_get_theme_options(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
    <div class="post-card-inner">
        <?php if ( has_post_thumbnail() ) : ?>
        <div class="post-image-content">
            <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                <?php the_post_thumbnail('medium'); ?>
            </a>
        </div>
        <?php endif; ?>

        <div class="post-card-text">
            <header class="entry-header">
                <h2 class="entry-title">
                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                        <?php the_title(); ?>
                    </a>
                </h2>

                <div class="entry-meta">
                    <?php if ( 'post' === get_post_type() ) : ?>
                        <span class="posted-on">
                            <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( get_the_time() ); ?>">
                                <?php echo esc_html( get_the_date() ); ?>
                            </a>
                        </span>
                        <span class="by-author">
                            <?php echo esc_html( get_the_author() ); ?>
                        </span>
                        <span class="categories">
                            <?php the_category(', '); ?>
                        </span>
                    <?php endif; ?>
                </div><!-- .entry-meta -->
            </header><!-- .entry-header -->

            <div class="entry-summary">
                <?php the_excerpt(); ?>
            </div>
        </div><!-- .post-card-text -->
    </div><!-- .post-card-inner -->
</article><!-- #post -->