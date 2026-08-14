<?php
/**
 * Template Name: Werdu Homepage V2
 * Description: Standalone custom homepage template for Werdu.de
 */
if (!defined('ABSPATH')) exit; // Exit if accessed directly
get_header();
?>
<style id="werdu-v2-layout-override">
  @font-face { font-display: swap !important; }
  a.skip-link, .skip-link, .skip, .screen-reader-text:focus { display: none !important; }
  .wv2-page { background: #f8fafc; color: #0f172a; }
  /* Verberg standaard thema-elementen die de lay-out breken (uitsluitend
     op deze pagina actief, want deze <style> staat alleen in dit template) */
  .page-header, .entry-header, .sidebar, #secondary, .breadcrumb, .breadcrumbs { display: none !important; }

  /* Forceer de hoofdid's en klassen naar volle breedte */
  #primary, #main, .main-content-section, article, .post-content, .entry-content {
    width: 100% !important;
    max-width: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
    float: none !important;
  }

  /* Reset container beperkingen voor de V2 homepage */
  .site-main, .container {
    max-width: 100% !important;
    width: 100% !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
</style>
<?php
/**
 * Losstaand, review-baar homepage-experiment (Werdu Homepage V2 — Stage 2).
 * Dit template wordt UITSLUITEND geladen wanneer een pagina in WP-Admin
 * bewust dit "Werdu Homepage V2"-template krijgt toegewezen. Zolang dat niet
 * gebeurt (de daadwerkelijke cutover is een losstaande, handmatige Stap 3),
 * heeft dit bestand nul effect op de live homepage of enige andere pagina —
 * geen bestaande hook, CSS-bestand of Elementor-content wordt aangeraakt.
 *
 * De CSS/JS-enqueue voor dit template staat bewust in functions.php
 * (werdu_enqueue_homepage_v2_assets(), gate: is_page_template('template-werdu-v2.php')),
 * zodat dit bestand hierboven exact kan beginnen met de officiële Template
 * Name-header gevolgd door get_header() — precies wat WordPress nodig heeft
 * om het template altijd correct in de Sjabloon-dropdown te tonen.
 *
 * Alle CTA's gebruiken de bestaande, globale helpers uit
 * wp-content/mu-plugins/werdu-homepage-seo-upgrade.php
 * (werdu_home_seo_beratung_url() / werdu_home_seo_rechner_url()), zodat er
 * nooit een hardgecodeerde host of een /kontakt/-link ontstaat.
 */
$wv2_beratung = function_exists( 'werdu_home_seo_beratung_url' ) ? werdu_home_seo_beratung_url() : home_url( '/beratung-anfragen/' );
$wv2_rechner  = function_exists( 'werdu_home_seo_rechner_url' ) ? werdu_home_seo_rechner_url() : home_url( '/solarbatterie-rechner/' );
?>

<a class="skip" href="#wv2-main">Zum Hauptinhalt springen</a>

<main id="wv2-main" class="wv2-page">

	<!-- =====================================================
	     SECTION 1 — HERO
	     ===================================================== -->
	<header class="wv2-section" style="padding-top:56px;">
		<div class="wv2-container">
			<div style="display:grid;grid-template-columns:1.1fr 1fr;gap:48px;align-items:center;">

				<div>
					<span class="wv2-eyebrow">🇩🇪 Made for Germany · 0&nbsp;% MwSt. auf PV-Speicher</span>
					<h1 style="font-size:clamp(2rem,4vw,3rem);font-weight:800;letter-spacing:-0.02em;line-height:1.1;margin:0 0 20px;">
						PV-Speicher kaufen: Ihre Unabhängigkeit beginnt mit der richtigen Kapazität
					</h1>
					<p style="font-size:1.15rem;color:var(--wv2-muted);max-width:520px;margin:0 0 28px;">
						Berechnen Sie in wenigen Sekunden, wie viel Solarstrom Sie mit einem PV-Speicher zusätzlich selbst nutzen können — und sichern Sie sich anschließend eine kostenlose, unverbindliche Fachberatung.
					</p>

					<ul style="list-style:none;padding:0;margin:0 0 32px;display:flex;flex-direction:column;gap:12px;">
						<?php
						$wv2_usps = array(
							'0 % Umsatzsteuer auf Kauf &amp; Installation (§ 12 Abs. 3 UStG)',
							'LiFePO4-Technologie mit 15–20 Jahren Lebensdauer',
							'Transparente Festpreise — kein individuelles Angebot nötig',
						);
						foreach ( $wv2_usps as $wv2_usp ) :
							?>
							<li style="display:flex;align-items:flex-start;gap:10px;font-weight:600;color:var(--wv2-text);">
								<svg width="22" height="22" viewBox="0 0 24 24" fill="none" style="flex-shrink:0;margin-top:1px;" aria-hidden="true">
									<circle cx="12" cy="12" r="12" fill="var(--accent)"></circle>
									<path d="M7 12.5l3.2 3.2L17 9" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"></path>
								</svg>
								<span><?php echo wp_kses_post( $wv2_usp ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>

					<a href="#wv2-calculator" class="wv2-btn wv2-btn-primary btn-shimmer">Jetzt Kapazität berechnen →</a>
				</div>

				<div>
					<?php
					// Reeds geüploade, door de klant aangeleverde hero-asset (test.werdu.de,
					// /2026/08). Bewust via home_url() opgebouwd i.p.v. een hardgecodeerde
					// host, zodat dezelfde afbeelding op test én productie correct oplost.
					// Er is geen apart klein "tiny-blur"-bestand aangeleverd voor deze foto —
					// de subtiele achtergrondkleur van .img-blur-wrapper dient daarom als
					// placeholder totdat de volledige afbeelding is geladen (onload -> .loaded).
					$wv2_hero_rel = '/wp-content/uploads/2026/08/pv-speicher-kaufen-modernes-deutsches-wohnhaus-alpen_1024_572.webp';
					?>
					<div class="img-blur-wrapper" style="aspect-ratio:1024/572;">
						<img
							class="full-img"
							src="<?php echo esc_url( home_url( $wv2_hero_rel ) ); ?>"
							alt="Modernes Wohnhaus mit Photovoltaik und LiFePO4-Heimspeicher in Deutschland"
							width="1024"
							height="572"
							fetchpriority="high"
							decoding="async"
							onload="this.classList.add('loaded')"
							onerror="this.closest('.img-blur-wrapper').style.display='none';"
						/>
						<span class="img-overlay-badge">0&nbsp;% MwSt.-Vorteil inklusive</span>
					</div>

					<div class="glass-card" style="margin-top:20px;padding:20px 24px;display:flex;justify-content:space-between;gap:16px;">
						<div>
							<div style="font-size:0.8rem;color:var(--wv2-muted);text-transform:uppercase;letter-spacing:0.04em;">Max. Autarkiegrad</div>
							<div style="font-size:1.4rem;font-weight:800;color:var(--wv2-text);">bis 85&nbsp;%</div>
						</div>
						<div>
							<div style="font-size:0.8rem;color:var(--wv2-muted);text-transform:uppercase;letter-spacing:0.04em;">Notstrom-Option</div>
							<div style="font-size:1.4rem;font-weight:800;color:var(--wv2-text);">Verfügbar</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</header>

	<!-- =====================================================
	     SECTION 1b — EINSATZSZENARIEN (de resterende 4 aangeleverde WebP-foto's)
	     ===================================================== -->
	<?php
	$wv2_scenarios = array(
		array(
			'rel' => '/wp-content/uploads/2026/08/heimpeicher-kaufen-ev-garage-abends-modernes-deutsches-wohnhaus._1024_572.webp',
			'alt' => 'PV-Batteriespeicher in Garage mit Wallbox',
			'label' => 'Garage &amp; E-Mobilität',
		),
		array(
			'rel' => '/wp-content/uploads/2026/08/pv-speicher-holzhaus-challet-all-in-one-notstrom_1024_572.webp',
			'alt' => 'LFP-Batteriespeicher mit Notstromfunktion im Holzhaus',
			'label' => 'Holzhaus &amp; Notstrom',
		),
		array(
			'rel' => '/wp-content/uploads/2026/08/lifepo4-stromspeicher-haushalt-serre_1024_572.webp',
			'alt' => 'LiFePO4 Stromspeicher im Hauswirtschaftsraum',
			'label' => 'Haushalt &amp; Technikraum',
		),
		array(
			'rel' => '/wp-content/uploads/2026/08/batteriespeicher-keller-nachruesten-unter-wechselrichter_1024_572.webp',
			'alt' => 'Modularer Batteriespeicher zur Nachrüstung im Keller',
			'label' => 'Keller &amp; Nachrüstung',
		),
	);
	?>
	<section class="wv2-section" style="padding-top:8px;padding-bottom:8px;">
		<div class="wv2-container">
			<div class="wv2-section-head">
				<span class="wv2-eyebrow">Für jedes Zuhause</span>
				<h2>Ihr PV-Speicher passt überall hin</h2>
				<p>Ob Garage, Keller, Technikraum oder Holzhaus — ein moderner PV-Speicher lässt sich flexibel in nahezu jede bauliche Situation integrieren.</p>
			</div>
			<div class="wv2-card-grid" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr));">
				<?php foreach ( $wv2_scenarios as $wv2_scenario ) : ?>
					<div class="glass-card wv2-hover-lift" style="overflow:hidden;">
						<div class="img-blur-wrapper" style="aspect-ratio:1024/572;">
							<img
								class="full-img"
								src="<?php echo esc_url( home_url( $wv2_scenario['rel'] ) ); ?>"
								alt="<?php echo esc_attr( $wv2_scenario['alt'] ); ?>"
								loading="lazy"
								width="1024"
								height="572"
								onload="this.classList.add('loaded')"
								onerror="this.closest('.glass-card').style.display='none';"
							/>
						</div>
						<div style="padding:14px 18px;font-weight:700;color:var(--wv2-text);font-size:0.95rem;">
							<?php echo wp_kses_post( $wv2_scenario['label'] ); ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- =====================================================
	     SECTION 2 — INTERAKTIVE KAPAZITÄTS-SLIDER (→ werduCalcHandoff)
	     ===================================================== -->
	<section class="wv2-section" id="wv2-calculator">
		<div class="wv2-container">
			<div class="wv2-section-head">
				<span class="wv2-eyebrow">Schnellrechner</span>
				<h2>Wie viel Speicherkapazität brauchen Sie?</h2>
				<p>Bewegen Sie den Regler auf Ihren geschätzten kWh-Bedarf. Für eine detaillierte Berechnung empfehlen wir unseren <a href="<?php echo esc_url( $wv2_rechner ); ?>" style="color:var(--accent);font-weight:700;">vollständigen Autarkie-Rechner</a>.</p>
			</div>

			<div class="glass-card" style="max-width:640px;margin:0 auto;padding:40px;">
				<div id="wv2-slider-track" class="wv2-slider" style="--p:28%;">
					<div class="wv2-slider-preview" id="wv2-slider-tooltip">12 kWh</div>
					<div class="wv2-slider-fill"></div>
					<input type="range" id="wv2-capacity-range" min="5" max="32" step="1" value="12" aria-label="Gewünschte Speicherkapazität in kWh" />
				</div>
				<div style="display:flex;justify-content:space-between;color:var(--wv2-muted);font-size:0.85rem;margin-bottom:28px;">
					<span>5 kWh</span>
					<span>32 kWh</span>
				</div>

				<div class="input-group" style="margin-bottom:24px;max-width:220px;">
					<label for="wv2-plz-input" style="font-weight:700;font-size:0.9rem;">Ihre Postleitzahl (optional)</label>
					<input type="text" id="wv2-plz-input" inputmode="numeric" pattern="[0-9]*" maxlength="5" placeholder="z. B. 10115" />
				</div>

				<a href="<?php echo esc_url( $wv2_beratung ); ?>" id="wv2-slider-submit" class="wv2-btn wv2-btn-primary btn-shimmer" style="width:100%;">
					Kostenlose Beratung zu dieser Kapazität anfordern
				</a>
			</div>
		</div>
	</section>

	<!-- =====================================================
	     SECTION 3 — BESTSELLER: KLIKBARE VARIANT-CARDS (WooCommerce)
	     ===================================================== -->
	<?php if ( class_exists( 'WooCommerce' ) && function_exists( 'wc_get_products' ) ) : ?>
		<?php
		$wv2_products = wc_get_products( array(
			'status'  => 'publish',
			'limit'   => 3,
			'orderby' => 'popularity',
			'order'   => 'DESC',
		) );
		if ( empty( $wv2_products ) ) {
			$wv2_products = wc_get_products( array(
				'status'  => 'publish',
				'limit'   => 3,
				'orderby' => 'date',
				'order'   => 'DESC',
			) );
		}
		?>
		<?php if ( ! empty( $wv2_products ) ) : ?>
			<section class="wv2-section">
				<div class="wv2-container">
					<div class="wv2-section-head">
						<span class="wv2-eyebrow">Unsere Bestseller</span>
						<h2>Wählen Sie Ihre passende Dimensionierung</h2>
						<p>Reale, sofort lieferbare Systeme aus unserem Shop — transparente Festpreise, keine versteckten Kosten.</p>
					</div>

					<div class="wv2-card-grid" role="radiogroup" aria-label="Produktauswahl">
						<?php
						global $product;
						$wv2_first = true;
						foreach ( $wv2_products as $wv2_product ) :
							if ( ! $wv2_product instanceof WC_Product ) {
								continue;
							}
							wc_setup_product_data( $wv2_product );

							$wv2_img_id   = $wv2_product->get_image_id();
							$wv2_img_full = $wv2_img_id ? wp_get_attachment_image_url( $wv2_img_id, 'medium' ) : '';
							$wv2_img_blur = $wv2_img_id ? wp_get_attachment_image_url( $wv2_img_id, 'thumbnail' ) : '';
							?>
							<label class="variant-card">
								<input type="radio" name="wv2-variant" value="<?php echo esc_attr( $wv2_product->get_id() ); ?>" <?php echo $wv2_first ? 'checked' : ''; ?> />
								<div class="glass-card">
									<?php if ( $wv2_img_full ) : ?>
										<div class="img-blur-wrapper" style="aspect-ratio:4/3;margin-bottom:16px;">
											<img class="tiny-blur" src="<?php echo esc_url( $wv2_img_blur ); ?>" alt="" aria-hidden="true" />
											<img class="full-img" src="<?php echo esc_url( $wv2_img_full ); ?>" alt="<?php echo esc_attr( $wv2_product->get_name() ); ?>" width="400" height="300" loading="lazy" decoding="async" />
										</div>
									<?php endif; ?>
									<h3><?php echo esc_html( $wv2_product->get_name() ); ?></h3>
									<p><?php echo wp_kses_post( wp_trim_words( $wv2_product->get_short_description() ? $wv2_product->get_short_description() : $wv2_product->get_description(), 16 ) ); ?></p>
									<strong><?php echo wp_kses_post( $wv2_product->get_price_html() ); ?></strong>
									<div style="margin-top:16px;">
										<?php woocommerce_template_loop_add_to_cart(); ?>
									</div>
								</div>
							</label>
							<?php
							$wv2_first = false;
						endforeach;
						wp_reset_postdata();
						?>
					</div>
				</div>
			</section>
		<?php endif; ?>
	<?php endif; ?>

	<!-- =====================================================
	     SECTION 4 — TECHNOLOGIE-VERGLEICH (semantische tabel)
	     ===================================================== -->
	<section class="wv2-section">
		<div class="wv2-container">
			<div class="wv2-section-head">
				<span class="wv2-eyebrow">Technologie-Vergleich</span>
				<h2>Welche Speicher-Technologie passt zu Ihnen?</h2>
				<p>Ein objektiver Überblick über die gängigen Batterie-Technologien für Heimspeicher.</p>
			</div>

			<div class="wv2-table-wrapper">
				<table class="wv2-table">
					<caption class="screen-reader-text">Vergleich von LiFePO4, Natrium-Ionen und Blei-Säure Batterietechnologie</caption>
					<thead>
						<tr>
							<th scope="col">Technologie</th>
							<th scope="col">Lebensdauer</th>
							<th scope="col">Ladezyklen</th>
							<th scope="col">Besonderheit</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th scope="row">LiFePO4 (Standard-Empfehlung)</th>
							<td>15–20 Jahre</td>
							<td>6.000–8.000</td>
							<td>Beste Balance aus Sicherheit, Lebensdauer und Preis</td>
						</tr>
						<tr>
							<th scope="row">Natrium-Ionen</th>
							<td>10–15 Jahre</td>
							<td>3.000–5.000</td>
							<td>Kobalt- &amp; lithiumfrei, sehr gute Kälteleistung</td>
						</tr>
						<tr>
							<th scope="row">Blei-Säure (Referenz, veraltet)</th>
							<td>5–8 Jahre</td>
							<td>500–1.500</td>
							<td>Günstige Anschaffung, deutlich höhere Folgekosten</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</section>

	<!-- =====================================================
	     SECTION 5 — BLOG-GRID (WP_Query, laatste 6 posts)
	     ===================================================== -->
	<?php
	$wv2_blog_query = new WP_Query( array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 6,
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );
	?>
	<?php if ( $wv2_blog_query->have_posts() ) : ?>
		<section class="wv2-section">
			<div class="wv2-container">
				<div class="wv2-section-head">
					<span class="wv2-eyebrow">Wissen &amp; Ratgeber</span>
					<h2>Aktuelles aus unserem Blog</h2>
					<p>Fundierte Antworten auf die wichtigsten Fragen rund um PV-Speicher und Energieunabhängigkeit.</p>
				</div>

				<div class="wv2-card-grid">
					<?php while ( $wv2_blog_query->have_posts() ) : $wv2_blog_query->the_post(); ?>
						<article class="wv2-blog-card glass-card wv2-hover-lift">
							<?php
							$wv2_thumb_id   = get_post_thumbnail_id();
							$wv2_thumb_full = $wv2_thumb_id ? wp_get_attachment_image_url( $wv2_thumb_id, 'medium_large' ) : '';
							$wv2_thumb_blur = $wv2_thumb_id ? wp_get_attachment_image_url( $wv2_thumb_id, 'thumbnail' ) : '';
							if ( $wv2_thumb_full ) :
								?>
								<div class="img-blur-wrapper">
									<img class="tiny-blur" src="<?php echo esc_url( $wv2_thumb_blur ); ?>" alt="" aria-hidden="true" />
									<img class="full-img" src="<?php echo esc_url( $wv2_thumb_full ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy" />
								</div>
							<?php endif; ?>
							<div class="wv2-blog-card-body">
								<?php
								$wv2_cats = get_the_category();
								if ( ! empty( $wv2_cats ) ) :
									?>
									<span class="wv2-blog-badge"><?php echo esc_html( $wv2_cats[0]->name ); ?></span>
								<?php endif; ?>
								<span class="wv2-blog-date"><?php echo esc_html( get_the_date() ); ?></span>
								<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
								<a class="wv2-blog-readmore" href="<?php the_permalink(); ?>">Weiterlesen →</a>
							</div>
						</article>
					<?php endwhile; ?>
				</div>
			</div>
		</section>
		<?php wp_reset_postdata(); ?>
	<?php endif; ?>

	<!-- =====================================================
	     SECTION 6 — SEO-BODY, FAQ, JSON-LD (shared with live homepage)
	     ===================================================== -->
	<?php if ( function_exists( 'werdu_home_seo_body_html' ) ) : ?>
		<?php echo werdu_home_seo_body_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup is built server-side with esc_url / wp_kses ?>
	<?php elseif ( function_exists( 'werdu_home_seo_faq_data' ) ) : ?>
		<section class="wv2-section">
			<div class="wv2-container" style="max-width:760px;">
				<div class="wv2-section-head">
					<span class="wv2-eyebrow">FAQ</span>
					<h2>Häufig gestellte Fragen</h2>
				</div>

				<?php foreach ( werdu_home_seo_faq_data() as $wv2_i => $wv2_faq ) : ?>
					<details class="accordion" <?php echo ( 0 === $wv2_i ) ? 'open' : ''; ?>>
						<summary><?php echo esc_html( $wv2_faq['q'] ); ?></summary>
						<div class="accordion-content">
							<div>
								<p><?php echo wp_kses_post( $wv2_faq['a'] ); ?></p>
							</div>
						</div>
					</details>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
		if ( function_exists( 'werdu_home_seo_faq_json_ld' ) ) {
			echo werdu_home_seo_faq_json_ld(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( function_exists( 'werdu_home_seo_software_json_ld' ) ) {
			echo werdu_home_seo_software_json_ld(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>
	<?php endif; ?>

	<!-- =====================================================
	     FINALE CTA
	     ===================================================== -->
	<section class="wv2-section">
		<div class="wv2-container" style="max-width:720px;text-align:center;">
			<div class="glass-card" style="padding:48px 32px;">
				<h2 style="margin:0 0 12px;font-size:1.75rem;">Bereit für Ihre eigene Energieunabhängigkeit?</h2>
				<p style="color:var(--wv2-muted);margin:0 0 28px;">Unsere Fachberater analysieren Ihren individuellen Bedarf kostenlos und unverbindlich.</p>
				<a href="<?php echo esc_url( $wv2_beratung ); ?>" class="wv2-btn wv2-btn-primary btn-shimmer">Jetzt unverbindliches Angebot anfordern</a>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>
