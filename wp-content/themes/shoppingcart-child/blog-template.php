<?php
/**
 * Template Name: Werdu Blog Template
 * Template Post Type: post, page
 */

get_header(); ?>

<style>
    /* CSS direct overgenomen uit jouw voorbeeld, geoptimaliseerd voor 900px */
    .werdu-blog-container { max-width: 900px; margin: 0 auto; padding: 0 15px; }
    .werdu-blog-hero { background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); padding: 80px 20px; text-align: center; color: white; border-radius: 0 0 20px 20px; margin-top: -20px; }
    .werdu-blog-hero h1 { font-size: clamp(1.8em, 4vw, 2.5em); margin-bottom: 15px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); color: #fff !important; }
    .werdu-blog-hero p { font-size: 1.2em; max-width: 700px; margin: 0 auto; opacity: 0.95; color: #fff !important; }
    
    .werdu-content-section { padding: 50px 0; background: white; }
    .werdu-content-section h2 { color: #1a5276; font-size: 1.8em; margin: 40px 0 20px; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
    .werdu-content-section h3 { color: #2980b9; font-size: 1.4em; margin: 30px 0 15px; }
    .werdu-content-section p { color: #444; line-height: 1.8; font-size: 1.05em; margin-bottom: 20px; }
    
    /* Technische elementen */
    .werdu-tech-box { background: #ebf5fb; border-left: 5px solid #3498db; padding: 25px; margin: 30px 0; border-radius: 0 8px 8px 0; }
    .werdu-tech-box h4 { color: #2980b9; margin-bottom: 15px; font-size: 1.2em; margin-top:0; }
    
    .werdu-bms-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 30px 0; }
    .werdu-bms-card { background: white; border: 2px solid #3498db; padding: 25px; border-radius: 12px; }
    .werdu-bms-card h4 { color: #1a5276; margin-bottom: 10px; margin-top:0; }
    
    .werdu-cta-box { background: linear-gradient(135deg, #3498db, #2980b9); color: white; padding: 40px; text-align: center; border-radius: 12px; margin: 40px 0; }
    .werdu-btn-orange { display: inline-block; background: #ff6600; color: white !important; padding: 15px 40px; border-radius: 50px; font-weight: bold; text-decoration: none; margin-top: 20px; transition: 0.3s; }
    .werdu-btn-orange:hover { transform: scale(1.05); background: #e65c00; }

    /* Verberg standaard thema-titel als die dubbel verschijnt */
    .entry-header { display: none; }
</style>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    
    <?php while ( have_posts() ) : the_post(); ?>

    <!-- HERO SECTIE -->
    <div class="werdu-blog-hero">
        <div class="werdu-blog-container">
            <h1><?php the_title(); ?></h1>
            <?php if ( has_excerpt() ) : ?>
                <p><?php echo get_the_excerpt(); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- CONTENT SECTIE -->
    <section class="werdu-content-section">
        <div class="werdu-blog-container">
            <div class="entry-content">
                <?php the_content(); ?>
            </div>
            
            <!-- STANDAARD CTA ONDERAAN ELK ARTIKEL -->
            <div class="werdu-cta-box">
                <h3>Bereit für maximale Energieunabhängigkeit?</h3>
                <p>Entdecken Sie unsere intelligenten Batteriesysteme für Ihr Zuhause.</p>
                <a href="https://werdu.de" class="btn-orange werdu-btn-orange">Jetzt Shop besuchen</a>
            </div>
        </div>
    </section>

    <?php endwhile; ?>

</article>

<?php get_footer(); ?>
