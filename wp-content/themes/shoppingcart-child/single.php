<?php
/**
 * single.php — Werdu Blog Artikel (Child Theme)
 * Plaatsing: /wp-content/themes/JOUW-CHILD-THEME/single.php
 *
 * Design: Editorial Magazine (consistent met home.php)
 * Features: Breadcrumbs, Schema.org Article markup, Related posts,
 *           Sticky sidebar, Reading progress, Autoren-Bio
 */

get_header();

// ── Helpers ───────────────────────────────────────────────────────
function werdu_reading_time_single($post_id) {
    return max(1, ceil(str_word_count(strip_tags(get_post_field('post_content', $post_id))) / 200));
}
function werdu_first_cat_single($post_id) {
    $c = get_the_category($post_id);
    return $c ? $c[0] : null;
}
function werdu_get_related_posts($post_id, $limit = 3) {
    $cat = werdu_first_cat_single($post_id);
    if (!$cat) return null;
    $q = new WP_Query([
        'post_type'      => 'post',
        'posts_per_page' => $limit,
        'post__not_in'   => [$post_id],
        'category__in'   => [$cat->term_id],
        'post_status'    => 'publish',
    ]);
    return $q->have_posts() ? $q : null;
}

$post_id   = get_the_ID();
$cat       = werdu_first_cat_single($post_id);
$cat_slug  = $cat ? $cat->slug : '';
$cat_name  = $cat ? $cat->name : 'Blog';
$rt        = werdu_reading_time_single($post_id);
$author    = get_the_author();
$author_id = get_the_author_meta('ID');
if (!$author) { $author = 'Werdu Redaktion'; }
$date_iso  = get_the_date('c');
$date_human= get_the_date('j. F Y');
$mod_iso   = get_the_modified_date('c');
$thumb_url = get_the_post_thumbnail_url($post_id, 'large');
$excerpt   = get_the_excerpt();

// Breadcrumbs
$blog_page = get_option('page_for_posts');
$blog_link = $blog_page ? get_permalink($blog_page) : home_url('/');
?>

<style>
/* ═══ TOKENS (consistent met home.php, verbeterd contrast) ═══ */
:root {
  --cream:   #faf8f4;
  --warm:    #f3efe8;
  --gold:    #c8922a;      /* alleen voor grote tekst >18px */
  --gold-dk: #8a5e0f;      /* voldoende contrast voor kleine tekst */
  --gold-lt: #fdf3e3;
  --forest:  #1d4d2f;
  --forest-lt:#e8f0eb;
  --ink:     #1c1c1c;
  --body:    #3d3d3d;
  --muted:   #6b6b6b;      /* donkerder voor beter contrast */
  --border:  #e2ddd6;
  --white:   #ffffff;
  --r:       10px;
  --max:     1280px;
}

/* ═══ BASE ══════════════════════════════════════════════════════ */
.wd-single * { box-sizing: border-box; }
.wd-single {
  font-family: 'Georgia', 'Times New Roman', serif;
  background: var(--cream);
  color: var(--body);
  min-height: 100vh;
}

/* ═══ READING PROGRESS ═══════════════════════════════════════════ */
.wd-progress {
  position: fixed;
  top: 0; left: 0; z-index: 9999;
  height: 3px;
  background: var(--forest);
  width: 0%;
  transition: width .1s linear;
}

/* ═══ BREADCRUMBS ═══════════════════════════════════════════════ */
.wd-breadcrumbs {
  font-family: 'Helvetica Neue', Arial, sans-serif;
  font-size: 12px;
  color: var(--muted);
  padding: 20px 32px 0;
  max-width: var(--max);
  margin: 0 auto;
}
.wd-breadcrumbs a {
  color: var(--muted);
  text-decoration: none;
  transition: color .2s;
}
.wd-breadcrumbs a:hover { color: var(--forest); text-decoration: underline; }
.wd-breadcrumbs .sep { margin: 0 8px; opacity: .5; }
.wd-breadcrumbs [aria-current="page"] { color: var(--ink); font-weight: 600; }

/* ═══ ARTICLE HEADER ════════════════════════════════════════════ */
.wd-article-header {
  max-width: var(--max);
  margin: 0 auto;
  padding: 32px 32px 40px;
}
.wd-article-kicker {
  font-family: 'Helvetica Neue', Arial, sans-serif;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 3px;
  text-transform: uppercase;
  color: var(--forest);
  margin-bottom: 14px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.wd-article-kicker::before {
  content: '';
  display: inline-block;
  width: 28px; height: 2px;
  background: var(--gold-dk);
}
.wd-article-kicker a {
  color: var(--forest);
  text-decoration: none;
}
.wd-article-kicker a:hover { text-decoration: underline; }
.wd-article-header h1 {
  font-size: clamp(32px, 4.5vw, 56px);
  line-height: 1.08;
  color: var(--ink);
  font-weight: 700;
  letter-spacing: -1px;
  margin: 0 0 20px;
  font-style: italic;
  max-width: 900px;
}
.wd-article-meta {
  font-family: 'Helvetica Neue', Arial, sans-serif;
  font-size: 14px;
  color: var(--muted);
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
  align-items: center;
}
.wd-article-meta .sep { opacity: .4; }
.wd-article-meta a { color: var(--muted); text-decoration: none; }
.wd-article-meta a:hover { color: var(--forest); text-decoration: underline; }

/* ═══ FEATURED IMAGE ════════════════════════════════════════════ */
.wd-article-hero {
  max-width: var(--max);
  margin: 0 auto 48px;
  padding: 0 32px;
}
.wd-article-hero img {
  width: 100%;
  height: auto;
  max-height: 520px;
  object-fit: cover;
  border-radius: 16px;
  display: block;
  background: var(--warm);
}
.wd-article-hero-placeholder {
  width: 100%; height: 320px;
  background: linear-gradient(135deg, var(--forest) 0%, #2d7a4f 100%);
  border-radius: 16px;
  display: flex; align-items: center; justify-content: center;
  color: rgba(255,255,255,.2); font-size: 72px;
}

/* ═══ LAYOUT ════════════════════════════════════════════════════ */
.wd-article-body {
  max-width: var(--max);
  margin: 0 auto;
  padding: 0 32px 80px;
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 56px;
  align-items: start;
}
@media (max-width: 960px) {
  .wd-article-body { grid-template-columns: 1fr; padding-bottom: 40px; }
  .wd-sidebar { display: none; }
  .wd-breadcrumbs { padding: 16px 20px 0; }
  .wd-article-header { padding: 24px 20px 32px; }
  .wd-article-hero { padding: 0 20px; margin-bottom: 32px; }
}

/* ═══ CONTENT ═══════════════════════════════════════════════════ */
.wd-content {
  font-size: 17px;
  line-height: 1.75;
  color: var(--body);
}
.wd-content > *:first-child { margin-top: 0; }
.wd-content h2 {
  font-size: clamp(24px, 3vw, 32px);
  color: var(--ink);
  margin: 48px 0 20px;
  line-height: 1.2;
  font-weight: 700;
  letter-spacing: -.3px;
}
.wd-content h3 {
  font-size: clamp(20px, 2.2vw, 24px);
  color: var(--ink);
  margin: 36px 0 16px;
  line-height: 1.25;
  font-weight: 700;
}
.wd-content p { margin: 0 0 20px; }
.wd-content a {
  color: var(--forest);
  text-decoration: underline;
  text-underline-offset: 3px;
  text-decoration-thickness: 1.5px;
  transition: color .2s;
}
.wd-content a:hover { color: var(--gold-dk); }
.wd-content ul, .wd-content ol {
  margin: 0 0 20px 24px;
  padding: 0;
}
.wd-content li { margin-bottom: 8px; }
.wd-content blockquote {
  margin: 32px 0;
  padding: 24px 28px;
  border-left: 4px solid var(--gold-dk);
  background: var(--gold-lt);
  border-radius: 0 12px 12px 0;
  font-style: italic;
  color: var(--ink);
}
.wd-content blockquote p:last-child { margin-bottom: 0; }
.wd-content img {
  max-width: 100%; height: auto;
  border-radius: 12px; margin: 24px 0;
  display: block;
}
.wd-content .alignleft { float: left; margin: 0 24px 24px 0; }
.wd-content .alignright { float: right; margin: 0 0 24px 24px; }
.wd-content .aligncenter { display: block; margin: 24px auto; }
.wd-content .wp-caption { max-width: 100%; }
.wd-content figcaption,
.wd-content .wp-caption-text {
  font-family: 'Helvetica Neue', Arial, sans-serif;
  font-size: 13px;
  color: var(--muted);
  text-align: center;
  margin-top: -16px; margin-bottom: 24px;
}
.wd-content pre {
  background: var(--ink);
  color: var(--cream);
  padding: 20px;
  border-radius: 10px;
  overflow-x: auto;
  font-size: 14px;
  line-height: 1.5;
}
.wd-content code {
  background: var(--warm);
  padding: 2px 6px;
  border-radius: 4px;
  font-size: .9em;
}

/* ═══ PAGE LINKS (mehrseitige Artikel) ══════════════════════════ */
.wd-page-links {
  margin-top: 32px;
  font-family: 'Helvetica Neue', Arial, sans-serif;
}
.wd-page-links a,
.wd-page-links span {
  display: inline-block;
  margin: 0 4px 4px 0;
  padding: 6px 12px;
  border: 1px solid var(--border);
  border-radius: 6px;
  text-decoration: none;
  color: var(--body);
}
.wd-page-links span { background: var(--forest); color: var(--white); border-color: var(--forest); }

/* ═══ TAGS ══════════════════════════════════════════════════════ */
.wd-tags {
  margin-top: 40px;
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}
.wd-tags a {
  font-family: 'Helvetica Neue', Arial, sans-serif;
  font-size: 12px;
  font-weight: 600;
  padding: 6px 14px;
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 20px;
  color: var(--body);
  text-decoration: none;
  transition: background .2s, border-color .2s;
}
.wd-tags a:hover { background: var(--forest-lt); border-color: var(--forest); color: var(--forest); }

/* ═══ AUTHOR BIO ════════════════════════════════════════════════ */
.wd-author {
  margin-top: 48px;
  padding: 28px;
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--r);
  display: flex;
  gap: 20px;
  align-items: flex-start;
}
.wd-author-avatar {
  width: 64px; height: 64px;
  border-radius: 50%;
  flex-shrink: 0;
  object-fit: cover;
  background: var(--warm);
}
.wd-author-info h4 {
  font-family: 'Helvetica Neue', Arial, sans-serif;
  font-size: 15px;
  font-weight: 700;
  color: var(--ink);
  margin: 0 0 4px;
}
.wd-author-info span {
  font-family: 'Helvetica Neue', Arial, sans-serif;
  font-size: 12px;
  color: var(--muted);
}
.wd-author-info p {
  font-family: 'Helvetica Neue', Arial, sans-serif;
  font-size: 14px;
  line-height: 1.6;
  color: var(--body);
  margin: 10px 0 0;
}

/* ═══ PREV / NEXT ═══════════════════════════════════════════════ */
.wd-post-nav {
  margin-top: 48px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}
.wd-post-nav a {
  display: block;
  padding: 20px 24px;
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--r);
  text-decoration: none;
  transition: transform .2s, box-shadow .2s;
}
.wd-post-nav a:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 32px rgba(0,0,0,.08);
}
.wd-post-nav .label {
  font-family: 'Helvetica Neue', Arial, sans-serif;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: var(--gold-dk);
  margin-bottom: 8px;
  display: block;
}
.wd-post-nav .title {
  font-size: 16px;
  font-weight: 700;
  color: var(--ink);
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.wd-post-nav .next { text-align: right; }
@media (max-width: 640px) {
  .wd-post-nav { grid-template-columns: 1fr; }
  .wd-post-nav .next { text-align: left; }
}

/* ═══ RELATED POSTS ═══════════════════════════════════════════ */
.wd-related {
  margin-top: 56px;
}
.wd-related > h3 {
  font-family: 'Helvetica Neue', Arial, sans-serif;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 3px;
  text-transform: uppercase;
  color: var(--muted);
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  gap: 16px;
}
.wd-related > h3::after {
  content: ''; flex: 1; height: 1px; background: var(--border);
}
.wd-related-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}
@media (max-width: 700px) {
  .wd-related-grid { grid-template-columns: 1fr; }
}
.wd-rel-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--r);
  overflow: hidden;
  text-decoration: none;
  display: flex;
  flex-direction: column;
  transition: transform .25s, box-shadow .25s;
}
.wd-rel-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 40px rgba(0,0,0,.09);
}
.wd-rel-thumb {
  width: 100%; aspect-ratio: 16/9;
  object-fit: cover; display: block;
  background: var(--warm);
}
.wd-rel-body { padding: 18px 20px 20px; flex: 1; display: flex; flex-direction: column; }
.wd-rel-cat {
  font-family: 'Helvetica Neue', Arial, sans-serif;
  font-size: 10px;
  font-weight: 800;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: var(--forest);
  margin-bottom: 8px;
}
.wd-rel-card h4 {
  font-size: 16px;
  line-height: 1.3;
  color: var(--ink);
  margin: 0 0 auto;
  font-weight: 700;
}
.wd-rel-date {
  font-family: 'Helvetica Neue', Arial, sans-serif;
  font-size: 12px;
  color: var(--muted);
  margin-top: 12px;
  padding-top: 12px;
  border-top: 1px solid var(--border);
}

/* ═══ SIDEBAR (consistent met home.php) ════════════════════════ */
.wd-sidebar {
  position: sticky;
  top: 72px;
  display: flex;
  flex-direction: column;
  gap: 28px;
}
.wd-widget {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--r);
  overflow: hidden;
}
.wd-widget-head {
  padding: 16px 20px;
  border-bottom: 1px solid var(--border);
  font-family: 'Helvetica Neue', Arial, sans-serif;
  font-size: 10px;
  font-weight: 800;
  letter-spacing: 2.5px;
  text-transform: uppercase;
  color: var(--muted);
}
.wd-widget-body { padding: 16px 20px; }

/* Newsletter */
.wd-newsletter p {
  font-family: 'Helvetica Neue', Arial, sans-serif;
  font-size: 13px;
  line-height: 1.6;
  color: var(--muted);
  margin-bottom: 14px;
}
.wd-newsletter-form {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.wd-newsletter-form .werdu-float-field {
  margin-bottom: 0;
}
.wd-newsletter-form .werdu-float-field .field {
  font-family: 'Helvetica Neue', Arial, sans-serif;
}
.wd-newsletter-form label.dsgvo {
  font-family: 'Helvetica Neue', Arial, sans-serif;
  font-size: 12px;
  line-height: 1.5;
  color: var(--muted);
  display: flex;
  gap: 8px;
  align-items: flex-start;
  cursor: pointer;
}
.wd-newsletter-form label.dsgvo input {
  margin-top: 2px;
  accent-color: var(--forest);
  width: 16px; height: 16px;
  flex-shrink: 0;
}
.wd-newsletter-form button {
  padding: 11px;
  background: var(--forest);
  color: var(--white);
  border: none;
  border-radius: 7px;
  font-size: 13px;
  font-weight: 700;
  font-family: 'Helvetica Neue', Arial, sans-serif;
  cursor: pointer;
  letter-spacing: .5px;
  transition: background .2s;
}
.wd-newsletter-form button:hover { background: #2d6b45; }
.wd-newsletter-form button:focus-visible {
  outline: 3px solid var(--gold-dk);
  outline-offset: 2px;
}

/* CTA widgets */
.wd-cta-calc {
  background: var(--forest-lt);
  border-color: var(--forest);
}
.wd-cta-calc .wd-widget-head {
  color: var(--forest);
  border-color: rgba(29,77,47,.2);
}
.wd-cta-calc p {
  font-family: 'Helvetica Neue', Arial, sans-serif;
  font-size: 13px;
  line-height: 1.6;
  color: var(--body);
  margin-bottom: 14px;
}
.wd-cta-calc a.btn {
  display: block; text-align: center;
  background: var(--forest); color: #fff;
  padding: 12px; border-radius: 8px;
  font-family: 'Helvetica Neue', sans-serif;
  font-size: 13px; font-weight: 700;
  text-decoration: none; letter-spacing: .5px;
  transition: background .2s, transform .2s;
}
.wd-cta-calc a.btn:hover { background: #2d6b45; transform: translateY(-2px); }

.wd-cta-beratung {
  background: var(--gold-lt);
  border-color: var(--gold-dk);
}
.wd-cta-beratung .wd-widget-head {
  color: var(--gold-dk);
  border-color: rgba(138,94,15,.2);
}
.wd-cta-beratung p {
  font-family: 'Helvetica Neue', Arial, sans-serif;
  font-size: 13px;
  line-height: 1.6;
  color: var(--body);
  margin-bottom: 14px;
}
.wd-cta-beratung a.btn {
  display: block; text-align: center;
  background: var(--gold-dk); color: #fff;
  padding: 12px; border-radius: 8px;
  font-family: 'Helvetica Neue', sans-serif;
  font-size: 13px; font-weight: 700;
  text-decoration: none; letter-spacing: .5px;
  transition: background .2s, transform .2s;
}
.wd-cta-beratung a.btn:hover { background: #6b4a0f; transform: translateY(-2px); }

/* Popular posts */
.wd-pop-list { list-style: none; padding: 0; margin: 0; }
.wd-pop-item {
  display: flex; gap: 12px;
  padding: 12px 0;
  border-bottom: 1px solid var(--border);
  text-decoration: none;
  align-items: flex-start;
}
.wd-pop-item:last-child { border-bottom: none; padding-bottom: 0; }
.wd-pop-num {
  font-family: 'Georgia', serif;
  font-size: 22px; font-weight: 700; font-style: italic;
  color: var(--border);
  line-height: 1; flex-shrink: 0; min-width: 24px;
}
.wd-pop-item h4 {
  font-size: 13px; line-height: 1.4;
  color: var(--ink); margin: 0 0 4px; font-weight: 600;
}
.wd-pop-item span {
  font-family: 'Helvetica Neue', Arial, sans-serif;
  font-size: 11px; color: var(--muted);
}

/* Category list */
.wd-cat-list { list-style: none; padding: 0; margin: 0; }
.wd-cat-list li { border-bottom: 1px solid var(--border); }
.wd-cat-list li:last-child { border-bottom: none; }
.wd-cat-list a {
  display: flex; justify-content: space-between; align-items: center;
  padding: 10px 0;
  font-family: 'Helvetica Neue', Arial, sans-serif;
  font-size: 14px;
  color: var(--body);
  text-decoration: none;
  transition: color .15s;
}
.wd-cat-list a:hover { color: var(--forest); }
.wd-cat-list .wd-cat-count {
  font-size: 11px; color: var(--white);
  background: var(--forest);
  border-radius: 20px;
  padding: 2px 8px;
  font-weight: 700;
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
  .wd-post-nav a, .wd-rel-card, .wd-cta-calc a.btn, .wd-cta-beratung a.btn,
  .wd-newsletter-form .werdu-float-field .label,
  .wd-newsletter-form .werdu-float-field .field {
    transition: none !important;
  }
  .wd-progress { display: none; }
}

/* Focus visible */
.wd-single a:focus-visible,
.wd-single button:focus-visible,
.wd-single input:focus-visible {
  outline: 3px solid var(--gold-dk);
  outline-offset: 2px;
}
</style>

<div class="wd-single">

  <!-- Reading progress -->
  <div class="wd-progress" id="wd-progress" aria-hidden="true"></div>

  <!-- Breadcrumbs -->
  <nav class="wd-breadcrumbs" aria-label="Breadcrumb">
    <a href="<?php echo esc_url(home_url('/')); ?>">Startseite</a>
    <span class="sep">›</span>
    <?php if ($blog_link) : ?>
      <a href="<?php echo esc_url($blog_link); ?>">Wissen</a>
    <?php else : ?>
      <a href="<?php echo esc_url(home_url('/')); ?>">Wissen</a>
    <?php endif; ?>
    <?php if ($cat) : ?>
      <span class="sep">›</span>
      <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>"><?php echo esc_html($cat_name); ?></a>
    <?php endif; ?>
    <span class="sep">›</span>
    <span aria-current="page"><?php the_title(); ?></span>
  </nav>

  <!-- Article Header -->
  <header class="wd-article-header">
    <div class="wd-article-kicker">
      <?php if ($cat) : ?>
        <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>"><?php echo esc_html($cat_name); ?></a>
      <?php else : ?>
        Blog
      <?php endif; ?>
    </div>
    <h1><?php the_title(); ?></h1>
    <div class="wd-article-meta">
      <time datetime="<?php echo esc_attr($date_iso); ?>"><?php echo esc_html($date_human); ?></time>
      <span class="sep">·</span>
      <span><?php echo $rt; ?> Min. Lesezeit</span>
      <span class="sep">·</span>
      <span>Von <?php echo esc_html($author); ?></span>
      <?php if (get_comments_number() > 0) : ?>
        <span class="sep">·</span>
        <a href="#comments"><?php comments_number('0 Kommentare', '1 Kommentar', '% Kommentare'); ?></a>
      <?php endif; ?>
    </div>
  </header>

  <!-- Featured Image -->
  <div class="wd-article-hero">
    <?php if ($thumb_url) : ?>
      <img src="<?php echo esc_url($thumb_url); ?>"
           alt="<?php the_title_attribute(); ?>"
           width="1200" height="630"
           fetchpriority="high">
    <?php else : ?>
      <div class="wd-article-hero-placeholder" role="img" aria-label="Beitragsbild Platzhalter">☀️</div>
    <?php endif; ?>
  </div>

  <!-- Main + Sidebar -->
  <div class="wd-article-body">
    <article>

      <!-- Content -->
      <div class="wd-content">
        <?php the_content(); ?>
        <?php wp_link_pages([
          'before' => '<div class="wd-page-links">',
          'after'  => '</div>',
          'link_before' => '<span>',
          'link_after' => '</span>',
        ]); ?>
      </div>

      <!-- Tags -->
      <?php
      $tags = get_the_tags();
      if ($tags) :
      ?>
        <div class="wd-tags">
          <?php foreach ($tags as $tag) : ?>
            <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" rel="tag"><?php echo esc_html($tag->name); ?></a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Author Bio -->
      <div class="wd-author">
        <?php echo get_avatar($author_id, 128, '', '', ['class' => 'wd-author-avatar']); ?>
        <div class="wd-author-info">
          <h4><?php echo esc_html($author); ?></h4>
          <span>Werdu.de Redaktion</span>
          <p>Experten für Heimspeicher, LiFePO4-Batterien und Photovoltaik. Bei Fragen: <a href="mailto:service@werdu.de">service@werdu.de</a></p>
        </div>
      </div>

      <!-- Prev / Next -->
      <nav class="wd-post-nav" aria-label="Beitragsnavigation">
        <?php
        $prev = get_previous_post();
        $next = get_next_post();
        ?>
        <?php if ($prev) : ?>
          <a href="<?php echo esc_url(get_permalink($prev)); ?>" class="prev" rel="prev">
            <span class="label">← Vorheriger</span>
            <span class="title"><?php echo esc_html(get_the_title($prev)); ?></span>
          </a>
        <?php else : ?>
          <div></div>
        <?php endif; ?>
        <?php if ($next) : ?>
          <a href="<?php echo esc_url(get_permalink($next)); ?>" class="next" rel="next">
            <span class="label">Nächster →</span>
            <span class="title"><?php echo esc_html(get_the_title($next)); ?></span>
          </a>
        <?php else : ?>
          <div></div>
        <?php endif; ?>
      </nav>

      <!-- Related Posts -->
      <?php
      $related = werdu_get_related_posts($post_id, 3);
      if ($related && $related->have_posts()) :
      ?>
      <section class="wd-related" aria-labelledby="wd-related-heading">
        <h3 id="wd-related-heading">Das könnte Sie interessieren</h3>
        <div class="wd-related-grid">
          <?php while ($related->have_posts()) : $related->the_post();
            $r_pid = get_the_ID();
            $r_cat = werdu_first_cat_single($r_pid);
            $r_thumb = get_the_post_thumbnail_url($r_pid, 'medium');
          ?>
          <a href="<?php the_permalink(); ?>" class="wd-rel-card">
            <?php if ($r_thumb) : ?>
              <img class="wd-rel-thumb" src="<?php echo esc_url($r_thumb); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
            <?php else : ?>
              <div class="wd-rel-thumb" style="display:flex;align-items:center;justify-content:center;background:var(--warm);color:var(--border);font-size:32px;">🔋</div>
            <?php endif; ?>
            <div class="wd-rel-body">
              <div class="wd-rel-cat"><?php echo esc_html($r_cat ? $r_cat->name : 'Blog'); ?></div>
              <h4><?php the_title(); ?></h4>
              <div class="wd-rel-date"><?php echo get_the_date('j. M Y'); ?></div>
            </div>
          </a>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>
      </section>
      <?php endif; ?>

      <!-- Comments -->
      <?php if (comments_open() || get_comments_number()) : ?>
        <div id="comments" style="margin-top:56px;">
          <?php comments_template(); ?>
        </div>
      <?php endif; ?>

    </article>

    <!-- Sidebar -->
    <aside class="wd-sidebar">

      <!-- Calculator CTA -->
      <div class="wd-widget wd-cta-calc">
        <div class="wd-widget-head">Ersparnis berechnen</div>
        <div class="wd-widget-body">
          <p>
            <strong style="display:block;font-size:15px;color:var(--forest);margin-bottom:6px">Solarbatterie-Rechner</strong>
            Eigenverbrauch, Autarkiegrad & Amortisation in 2 Minuten kalkuliert.
          </p>
          <a href="<?php echo esc_url(home_url('/solarbatterie-rechner/')); ?>" class="btn">Jetzt berechnen →</a>
        </div>
      </div>

      <!-- Newsletter -->
      <div class="wd-widget">
        <div class="wd-widget-head">Newsletter</div>
        <div class="wd-widget-body wd-newsletter">
          <p>Neue Artikel zu LiFePO4, Förderungen & Preisen direkt ins Postfach.</p>
          <form class="wd-newsletter-form" onsubmit="return false;">
            <div class="werdu-float-field">
              <label class="label" for="wd-newsletter-email">E-Mail-Adresse</label>
              <input type="email" class="field" id="wd-newsletter-email" name="email" placeholder=" " autocomplete="email" required>
            </div>
            <label class="dsgvo">
              <input type="checkbox" required>
              <span>Ich stimme dem Erhalt von E-Mails zu und akzeptiere die <a href="<?php echo esc_url(home_url('/datenschutzerklaerung/')); ?>" target="_blank">Datenschutzerklärung</a>.</span>
            </label>
            <button type="submit">Jetzt abonnieren</button>
          </form>
        </div>
      </div>

      <!-- Popular Posts -->
      <?php
      $popular = new WP_Query([
        'posts_per_page' => 4,
        'post_status'    => 'publish',
        'orderby'        => 'comment_count',
        'order'          => 'DESC',
      ]);
      if ($popular->have_posts()) :
      ?>
      <div class="wd-widget">
        <div class="wd-widget-head">Beliebt</div>
        <div class="wd-widget-body">
          <ul class="wd-pop-list">
            <?php $n = 1; while ($popular->have_posts()) : $popular->the_post(); ?>
            <a href="<?php the_permalink(); ?>" class="wd-pop-item">
              <span class="wd-pop-num">0<?php echo $n; ?></span>
              <div>
                <h4><?php the_title(); ?></h4>
                <span><?php echo get_the_date('j. M Y'); ?></span>
              </div>
            </a>
            <?php $n++; endwhile; wp_reset_postdata(); ?>
          </ul>
        </div>
      </div>
      <?php endif; ?>

      <!-- Categories -->
      <?php $cats = get_categories(['hide_empty' => true]); if ($cats) : ?>
      <div class="wd-widget">
        <div class="wd-widget-head">Kategorien</div>
        <div class="wd-widget-body">
          <ul class="wd-cat-list">
            <?php foreach ($cats as $c) : ?>
            <li>
              <a href="<?php echo esc_url(get_category_link($c->term_id)); ?>">
                <?php echo esc_html($c->name); ?>
                <span class="wd-cat-count"><?php echo $c->count; ?></span>
              </a>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <?php endif; ?>

      <!-- Beratung CTA -->
      <div class="wd-widget wd-cta-beratung">
        <div class="wd-widget-head">Persönliche Beratung</div>
        <div class="wd-widget-body">
          <p>
            <strong style="display:block;font-size:15px;color:var(--gold-dk);margin-bottom:6px">Unsicher welcher Speicher passt?</strong>
            Wir helfen Ihnen bei der Auswahl des optimalen Heimspeichers.
          </p>
          <a href="<?php echo esc_url(home_url('/beratung-anfragen/')); ?>" class="btn">Beratung anfragen →</a>
        </div>
      </div>

    </aside>
  </div>
</div>

<!-- Schema.org Article JSON-LD -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "<?php echo esc_js(get_the_title()); ?>",
  "description": "<?php echo esc_js($excerpt ? $excerpt : wp_trim_words(wp_strip_all_tags(get_the_content()), 30, '…')); ?>",
  "image": "<?php echo esc_js($thumb_url ? $thumb_url : ''); ?>",
  "datePublished": "<?php echo esc_js($date_iso); ?>",
  "dateModified": "<?php echo esc_js($mod_iso); ?>",
  "author": {
    "@type": "Organization",
    "name": "Werdu.de",
    "url": "<?php echo esc_url(home_url('/')); ?>"
  },
  "publisher": {
    "@type": "Organization",
    "name": "Werdu.de",
    "logo": {
      "@type": "ImageObject",
      "url": "<?php echo esc_url(home_url('/')); ?>"
    }
  },
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "<?php echo esc_url(get_permalink()); ?>"
  }
}
</script>

<!-- Reading progress -->
<script>
(function(){
  var bar = document.getElementById('wd-progress');
  if(!bar) return;
  function update(){
    var st = window.scrollY || document.documentElement.scrollTop;
    var max = document.documentElement.scrollHeight - window.innerHeight;
    var pct = max > 0 ? (st / max) * 100 : 0;
    bar.style.width = pct + '%';
  }
  window.addEventListener('scroll', update, { passive: true });
  update();
})();
</script>

<?php get_footer(); ?>