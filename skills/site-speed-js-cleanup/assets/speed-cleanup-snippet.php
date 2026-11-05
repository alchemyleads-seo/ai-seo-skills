// TEMPLATE (site-speed-js-cleanup skill). WPCode PHP snippet, run everywhere. Rename the sc_ prefix per site.
// Fill in: SITE_SPEED_OPT_MONTH, the SVG->WebP map, the poster map. Ship in test mode first (see the TEST MODE line).
if ( ! defined( 'SITE_SPEED_OPT_MONTH' ) ) { define( 'SITE_SPEED_OPT_MONTH', 'YYYY/MM' ); }
// Works on the final HTML for logged-out visitors, so every removal is content-aware: an asset is only
// dropped from pages that don't use it. Editors (edit_posts) and the Cornerstone builder see the page untouched.
// Rollback: deactivate this snippet, then clear the page cache.

if ( ! function_exists( 'sc_speed_svg_map' ) ) {
	// Raster images wrapped in SVG (Canva/Figma exports) -> WebP rendered at 2x in the browser and uploaded as <name>-opt.webp. Values are the SVG's intrinsic size, added to <img> tags that lack width/height
	// so the 2x WebP displays at the same size the SVG did.
	function sc_speed_svg_map() {
		$dims = array(
			// 'YYYY/MM/file-name' => array( svg_width, svg_height ), // -> /wp-content/uploads/<UPLOAD_MONTH>/file-name-opt.webp
		);
		$map = array();
		foreach ( $dims as $path => $wh ) {
			$map[ '/wp-content/uploads/' . $path . '.svg' ] = array( '/wp-content/uploads/' . SITE_SPEED_OPT_MONTH . '/' . basename( $path ) . '-opt.webp', $wh[0], $wh[1] );
		}
		return $map;
	}
}

if ( ! function_exists( 'sc_speed_poster_map' ) ) {
	// Videos -> poster frames captured from the video, so they can use preload="none".
	function sc_speed_poster_map() {
		return array(
			// 'video-file-name.mp4' => '/wp-content/uploads/YYYY/MM/poster-name.webp',
		);
	}
}

if ( ! function_exists( 'sc_speed_remove_tags' ) ) {
	// Removes <script id="..."> / <link id="..."> / <style id="..."> tags whose id matches $id_re.
	function sc_speed_remove_tags( $html, $id_re ) {
		$html = preg_replace( '#<script\b[^>]*\bid=["\'](?:' . $id_re . ')["\'][^>]*>.*?</script>\s*#is', '', $html );
		$html = preg_replace( '#<link\b[^>]*\bid=["\'](?:' . $id_re . ')["\'][^>]*>\s*#i', '', $html );
		return preg_replace( '#<style\b[^>]*\bid=["\'](?:' . $id_re . ')["\'][^>]*>.*?</style>\s*#is', '', $html );
	}
}

if ( ! function_exists( 'sc_speed_cleanup_buffer' ) ) {
	function sc_speed_cleanup_buffer( $html ) {
		if ( strlen( $html ) < 1000 || false === stripos( $html, '<html' ) || false === stripos( $html, '</body>' ) ) {
			return $html;
		}
		$before = strlen( $html );

		// 1. Figma clipboard data pasted into the builder (invisible, can be hundreds of KB per page).
		$html = preg_replace( '/\sdata-(?:buffer|metadata)="(?:&lt;|<)!--\(fig[^"]*"/i', '', $html );

		// 2. Raster-in-SVG images -> WebP. Keep the displayed size by adding the SVG's dimensions when missing.
		$map  = sc_speed_svg_map();
		$html = preg_replace_callback(
			'#<img\b[^>]*>#i',
			function ( $m ) use ( $map ) {
				$tag = $m[0];
				foreach ( $map as $svg => $to ) {
					if ( false === strpos( $tag, $svg ) ) {
						continue;
					}
					$tag = str_replace( $svg, $to[0], $tag );
					if ( ! preg_match( '/\swidth=/i', $tag ) && ! preg_match( '/\sheight=/i', $tag ) ) {
						$tag = preg_replace( '#^<img\b#i', '<img width="' . $to[1] . '" height="' . $to[2] . '"', $tag );
					}
				}
				return $tag;
			},
			$html
		);
		foreach ( $map as $svg => $to ) { // CSS backgrounds or links that point at the same files
			$html = str_replace( $svg, $to[0], $html );
		}

		// 3. Contact Form 7 + its add-ons only where a CF7 form is rendered.
		if ( false === strpos( $html, 'class="wpcf7-form' ) ) {
			$plugins = '(?:contact-form-7|country-phone-field-contact-form-7|wpcf7-redirect)';
			$html    = preg_replace( '#<link\b[^>]*href=["\'][^"\']*/plugins/' . $plugins . '/[^>]*>\s*#i', '', $html );
			$html    = preg_replace( '#<script\b[^>]*src=["\'][^"\']*/plugins/' . $plugins . '/[^>]*>\s*</script>\s*#i', '', $html );
			$html    = sc_speed_remove_tags( $html, 'contact-form-7-js-(?:before|after|extra)|swv-js-(?:before|after|extra)|nbcpf-[A-Za-z-]+-js-(?:before|after|extra)|wpcf7-redirect-script-js-(?:before|after|extra)' );
		}

		// 4. MediaElement player only where a video or audio element is rendered.
		if ( ! preg_match( '/<(?:video|audio)\b/i', $html ) ) {
			$html = sc_speed_remove_tags( $html, '(?:wp-)?mediaelement(?:-core|-migrate)?-(?:js|css)(?:-before|-after|-extra)?' );
		}

		// 5. comment-reply only where a comment form is rendered.
		if ( false === strpos( $html, 'comment-respond' ) ) {
			$html = sc_speed_remove_tags( $html, 'comment-reply-js' );
		}

		// 6. Emoji polyfill (modern browsers render emoji natively).
		$html = sc_speed_remove_tags( $html, 'wp-emoji-settings|wp-emoji-styles-inline-css' );
		$html = preg_replace( '#<script\b[^>]*>\s*/\*! This file is auto-generated \*/\s*(?:const|var|let) e="script\#wp-emoji-settings".*?</script>\s*#is', '', $html );

		// 7. Videos: nothing downloads until play ; show a poster frame instead.
		$posters = sc_speed_poster_map();
		$html    = preg_replace_callback(
			'#<video\b[^>]*>(.*?)</video>#is',
			function ( $m ) use ( $posters ) {
				$block = $m[0];
				$open  = substr( $block, 0, strpos( $block, '>' ) + 1 );
				$new   = preg_match( '/\spreload=/i', $open ) ? preg_replace( '/\spreload=(["\'])[^"\']*\1/i', ' preload="none"', $open ) : str_replace( '<video', '<video preload="none"', $open );
				foreach ( $posters as $file => $poster ) {
					if ( false !== strpos( $m[1], $file ) && ! preg_match( '/\sposter=(["\'])[^"\']+\1/i', $new ) ) {
						$new = preg_match( '/\sposter=/i', $new ) ? preg_replace( '/\sposter=(["\'])\1/i', ' poster="' . $poster . '"', $new ) : str_replace( '<video', '<video poster="' . $poster . '"', $new );
						break;
					}
				}
				return $new . substr( $block, strlen( $open ) );
			},
			$html
		);

		// 8. HubSpot forms: load the embed (and the reCAPTCHA it pulls in) on the
		// visitor's first scroll/tap/keypress, or right away if the form is already near the viewport.
		$hs_src = '';
		$html   = preg_replace_callback(
			'#<script\b[^>]*\bsrc=["\']((?:https?:)?//js(?:-[a-z0-9]+)?\.hsforms\.net/forms/(?:embed/)?v2\.js)["\'][^>]*>\s*</script>#i',
			function ( $m ) use ( &$hs_src ) {
				$hs_src = $hs_src ? $hs_src : $m[1];
				return '';
			},
			$html
		);
		if ( $hs_src ) {
			$targets = array();
			$html    = preg_replace_callback(
				'#(<script\b[^>]*>)(\s*hbspt\.forms\.create\(.*?)</script>#is',
				function ( $m ) use ( &$targets ) {
					if ( preg_match( '/target:\s*["\']([^"\']+)["\']/', $m[2], $t ) ) {
						$targets[] = $t[1];
					}
					return $m[1] . '(window.alHsQ=window.alHsQ||[]).push(function(){' . $m[2] . "\n});</script>";
				},
				$html
			);
			$src    = 0 === strpos( $hs_src, '//' ) ? 'https:' . $hs_src : $hs_src;
			$loader = '<script>(function(){var q=window.alHsQ=window.alHsQ||[],go=false,ev=["scroll","pointerdown","keydown","touchstart","mousemove"];'
				. 'function load(){if(go)return;go=true;ev.forEach(function(e){removeEventListener(e,load,{passive:true})});var s=document.createElement("script");s.src=' . wp_json_encode( $src ) . ';s.async=true;'
				. 's.onload=function(){var f;while((f=q.shift())){try{f()}catch(e){console.error(e)}}q.push=function(fn){try{fn()}catch(e){console.error(e)}}};document.head.appendChild(s)}'
				. 'ev.forEach(function(e){addEventListener(e,load,{passive:true})});'
				. 'if("IntersectionObserver"in window){var io=new IntersectionObserver(function(es){if(es.some(function(x){return x.isIntersecting})){io.disconnect();load()}},{rootMargin:"400px"});'
				. wp_json_encode( array_values( array_unique( $targets ) ) ) . '.forEach(function(t){try{document.querySelectorAll(t).forEach(function(el){io.observe(el)})}catch(e){}})}})();</script>';
			$html   = preg_replace( '#</body>#i', $loader . "\n</body>", $html, 1 );
		}

		// 9. The header logo is above the fold: don't lazy-load the first one.
		$html = preg_replace_callback(
			'#<img\b[^>]*/wp-content/uploads/\d{4}/\d{2}/logo\.(?:svg|png|webp)[^>]*>#i',
			function ( $m ) {
				static $done = false;
				if ( $done ) {
					return $m[0];
				}
				$done = true;
				return preg_replace( '/\sloading=(["\'])lazy\1/i', ' fetchpriority="high"', $m[0] );
			},
			$html,
			1
		);

		return $html . "\n<!-- speed-cleanup-v1: " . round( ( $before - strlen( $html ) ) / 1024 ) . " KB removed -->";
	}
}

add_action(
	'template_redirect',
	function () {
		if ( is_admin() || is_feed() || is_preview() || is_customize_preview() || current_user_can( 'edit_posts' ) || isset( $_GET['sc_speed_off'] ) || ! isset( $_GET['sc_speed_on'] ) ) { // TEST MODE: delete `|| ! isset( $_GET['sc_speed_on'] )` to go live
			return;
		}
		ob_start( 'sc_speed_cleanup_buffer' );
	},
	1
);
