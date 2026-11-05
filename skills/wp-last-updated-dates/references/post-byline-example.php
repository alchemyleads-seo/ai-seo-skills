/* post-byline-example: visible byline and "Last updated" date (WPCode / Code Snippets, PHP, run everywhere).
   Posts: "By <author> · Published Dec 10, 2024 · Last updated Oct 16, 2026" (Last updated only when it is
   at least a day after publishing).
   Pages: "Last updated Oct 3, 2026" on the pages listed in lu_page_match(), placed under the hero's p.lede
   (or at the end of the content).
   Last updated = post_modified, the same date AIOSEO / Yoast / Rank Math put in dateModified schema.
   LU_LIVE true = live for everyone. Set false to stage changes behind ?lu_preview=1 for editors. */
if (!defined('LU_LIVE')) define('LU_LIVE', false);
if (!defined('LU_TIMEZONE')) define('LU_TIMEZONE', 'America/New_York'); // the audience's time zone; stored dates stay UTC

function lu_new() {
	return LU_LIVE || (isset($_GET['lu_preview']) && current_user_can('edit_posts'));
}
function lu_page_match($id) {
	$path = trailingslashit((string) wp_parse_url(get_permalink($id), PHP_URL_PATH));
	// Bottom-of-funnel pages: edit this list for the site.
	$bofu = array('/pricing/', '/cost/', '/how-long-does-it-take/', '/switching-agencies/', '/vs-in-house/');
	if (in_array($path, $bofu, true)) return true;
	// Whole sections by prefix.
	return (bool) preg_match('#^/(glossary|guides)/#', $path);
}
function lu_applies() {
	if (is_singular('post')) return lu_new();
	return is_page() && lu_new() && lu_page_match(get_queried_object_id());
}
add_filter('the_content', function ($content) {
	static $done = false;
	if ($done || is_admin() || is_feed() || !lu_applies()) return $content;
	if (doing_action('wp_head') || doing_filter('get_the_excerpt')) return $content;
	$id = get_the_ID();
	if (!$id || (int) $id !== (int) get_queried_object_id()) return $content;
	$done = true;
	$tz = new DateTimeZone(LU_TIMEZONE);
	$pub = (int) get_post_time('U', true, $id);
	$mod = (int) get_post_modified_time('U', true, $id);
	$time = function ($ts) use ($tz) {
		return '<time datetime="' . esc_attr(wp_date('Y-m-d', $ts, $tz)) . '">' . esc_html(wp_date('M j, Y', $ts, $tz)) . '</time>';
	};
	$dot = ' <span aria-hidden="true">&middot;</span> ';
	if (!is_singular('post')) {
		// Under the hero lede when the page has one, otherwise at the end of the content
		$line = '<p class="lu-byline lu-updated">Last updated ' . $time(max($mod, $pub)) . '</p>';
		$out = preg_replace('#(</h1>\s*<p class="lede">.*?</p>)#s', '$1' . $line, $content, 1, $n);
		return $n ? $out : $content . $line;
	}
	$author_id = (int) get_post_field('post_author', $id);
	$html = '<p class="lu-byline">By <a href="' . esc_url(get_author_posts_url($author_id)) . '" rel="author">'
		. esc_html(get_the_author_meta('display_name', $author_id)) . '</a>' . $dot;
	$html .= 'Published ' . $time($pub);
	if ($mod > $pub + DAY_IN_SECONDS) $html .= $dot . 'Last updated ' . $time($mod);
	return $html . '</p>' . $content;
}, 20);
add_action('wp_head', function () {
	if (!lu_applies()) return;
	echo '<style id="lu-byline-css">body .entry-content .lu-byline,.lu-byline{max-width:768px;margin:0 auto 28px!important;padding:0 16px;text-align:center;font-size:14px;line-height:1.5;opacity:.75;letter-spacing:.02em}.lu-byline a{text-decoration:none}.lu-byline a:hover{text-decoration:underline}body .entry-content .lu-updated{margin:18px auto 24px!important}</style>';
});
