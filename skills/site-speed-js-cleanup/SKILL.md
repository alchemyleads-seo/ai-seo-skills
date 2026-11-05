---
name: site-speed-js-cleanup
description: Page-speed quality pass plus JavaScript/asset cleanup for WordPress (and other CMS) sites that have accumulated years of scripts, plugins and pasted content. Baselines with full PageSpeed Insights, inventories every script/style and where it's injected (WPCode, plugins, theme, GTM, CDN), scans every sitemap URL for bloat and per-page feature use, then ships content-aware fixes behind a test flag, verifies logged-out, and re-measures. Trigger on "site is slow", "PageSpeed", "Core Web Vitals", "clean up JS", "what scripts are on the site", "remove <vendor> script", "page weight", "TBT/LCP", or a low PageSpeed Insights score.
---

# Site speed + JS cleanup

**Why:** a 10-year-old site had a 31/100 mobile score, 14 MB of page weight and 2.8 s of blocking time. None of it came from the theme alone. It came from things nobody remembered adding: Figma clipboard data pasted into the builder (580 KB on one page), photos hidden inside SVG files, a testimonial video preloading 7.5 MB, a form embed pulling reCAPTCHA in 8 times, and form plugins loading on every page for one hidden form. Generic "optimize images, defer JS" advice misses all of these. Measure, inventory, then fix what is actually there.

This skill is the inventory, cleanup and rollout workflow; pair it with any general web-performance guide for metric definitions and trace analysis. Keep per-site findings, IDs and history in a notes file for that site; read it first and update it at the end.

## 0. Ground rules
- **Never delete. Deactivate.** Snippets get turned off, media gets added beside the original, and plugin removals are a recommendation list for the owner unless they explicitly approved each one.
- **Content-aware removals only.** Drop an asset from pages that don't use it; never blanket-dequeue something a page needs. Decide per page from the final HTML (is there a form, a video, a comment form?).
- **Logged-out is the truth.** Logged-in editors bypass page cache and get extra assets. Measure and verify as an anonymous visitor (a separate browser profile or the built-in browser, never the admin tab).
- **Firewall challenges lie.** A CDN challenge page served to your fetcher can look like `noindex`, empty robots.txt, or 403 assets. If the baseline request is challenged, mark those results inconclusive. Use a real browser for scans.
- Auto-mode classifier: even with a broad "do all", live edits can be blocked. Get the owner to approve numbered items explicitly. Turning off a CAPTCHA counts as security-weakening; the owner does that.
- Ask before: Cloudflare zone settings (Rocket Loader, minify, cache rules), theme file edits, plugin deactivation, legal page edits (privacy/cookie policy). Site-wide go-live of a new output filter needs the owner's go as well.

## 1. Baseline (keep the JSON)
- Full Lighthouse JSON from the PageSpeed Insights API (free; an API key raises the rate limit):
  `curl -s "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=<URL>&strategy=mobile&key=$PSI_API_KEY" -o before.json`
  For field data, check the CrUX history (needs real Chrome traffic).
- Pull from the JSON: score; LCP/TBT/CLS/FCP/SI; `lcp-breakdown-insight` (LCP element + render delay); `network-requests` sorted by transferSize; `bootup-time`; `unused-javascript`; `render-blocking-insight`; `network-dependency-tree-insight` (CSS @import and font chains); `cache-insight` (third parties you didn't know were there).
- Run 2+ times. One lab run swings a lot (we saw 31 then 42 on the same page). Report a range.
- Check server time (`server-response-time`). If it's under ~300 ms the host isn't the problem; don't go digging in host logs yet.

## 2. Inventory every script and style
Logged-out fetch of the page in the browser (`fetch(url, {credentials:'omit'})`, then DOMParser). For each `<script>`/`<link>`/`<style>`: id, src/href, type, async/defer, size, head vs body. For inline scripts, list the hostnames they mention. Never dump full inline code (consent, pixel and token strings).
- `type="<hash>-text/javascript"` everywhere means Cloudflare Rocket Loader is rewriting scripts at the edge.
- `type="text/plain" data-*-consent=` means a consent manager holds it until opt-in. It still counts if the default is opt-in for some regions (US default-on loads it in Lighthouse).

Then find **where each one is injected**:
- WPCode: list all snippets, fetch each edit page, grep the code (`[name=wpcode_snippet_code]`) for the vendor host. WPCode's list search only matches titles.
- Plugins: `/wp-json/wp/v2/plugins`. The asset path (`/plugins/<slug>/`) names the plugin.
- Theme / child theme: `style.css` `@import`s, `functions.php` enqueues, builder "custom JS" fields.
- GTM: tags that inject more scripts (check `cache-insight` for hosts you can't find in the HTML).
- Page builder content: raw HTML or code elements inside the page (embeds such as HubSpot `hbspt.forms.create`).

Hand the owner a table: script, what it is, where it's injected, pages that need it, verdict (keep / load on demand / page-scope / remove), and expected saving.

## 3. Scan every sitemap URL (in the browser, background job)
Read the sitemap index. AIOSEO wraps URLs in CDATA: `<loc>\s*(?:<!\[CDATA\[)?\s*([^<\]\s]+)`. Fetch each page logged-out with 6 workers. Push results to `window.__scan` as you go (calls over 45 s time out, so start it and poll). Per page, record:
- HTML KB, and bytes in `data-buffer` / `data-metadata` attributes (Figma/Canva paste junk)
- Feature use: CF7 forms (`class="wpcf7-form`), HubSpot embeds, `<video>`/`<audio>`, sliders, comment forms, iframes
- Every image under `/uploads/` that ends in `.svg`. Then fetch each SVG and flag the ones with `data:image/(png|jpeg|webp)` inside (raster-in-SVG).

## 4. Known traps (check every site)
| Trap | Signal | Fix |
|---|---|---|
| Figma/Canva clipboard junk | `data-buffer="<!--(figma)..."` / `data-metadata="<!--(figmeta)..."` on spans | Strip the attributes in output; clean the source when the page is next edited |
| Raster-in-SVG images | `.svg` over 20 KB with an embedded PNG | Render in the browser to a canvas at 2× display size, export WebP (q ~0.86), upload as `<name>-opt.webp`, swap the src, and add the SVG's intrinsic width/height if the tag has none |
| Video preloading | `<video preload="metadata/auto">`, multi-MB mp4 in network-requests | `preload="none"`, plus a poster frame captured from the video (canvas → WebP) |
| Form embed + reCAPTCHA | hsforms `v2.js`, recaptcha repeated in network-requests, seconds of bootup | Load the embed on first scroll/tap/keypress, or right away if the form target is within ~400 px of the viewport |
| Form plugins on every page | CF7 + add-ons on pages with no CF7 form (or only a form hidden at every breakpoint) | Drop their CSS/JS where no form renders; list hidden dead forms for removal |
| Player/comment/emoji leftovers | mediaelement without video, comment-reply without comments, wp-emoji | Content-aware removal |
| Child theme `@import` chains | `network-dependency-tree-insight` shows style.css → old theme CSS copy → Font Awesome 4 webfont / Google Fonts | Confirm visually that nothing depends on them, then enqueue a cleaned copy (owner OK; theme files) |
| Lazy-loaded logo/hero | `loading="lazy"` on the first logo or the LCP image | Remove lazy, add `fetchpriority="high"` |
| Rocket Loader | rewritten script types; rocket-loader in bootup-time | A/B with the owner's OK, 3+ runs per side, comparing medians. On a Cornerstone site with jQuery in the head it was a net WIN (off: lower TBT but slow FCP in half the runs). Only drop it after head scripts are deferred |
| Deferring all scripts | Rocket Loader replacement | Defer externals and turn inline scripts into deferred data: URIs (order and globals preserved). Run it in the OUTERMOST output buffer (start it before any consent/gating filter) or vendor code gets base64'd past the consent gate. Keep DOM-inserting decorative scripts inline (fire their DOMContentLoaded handlers immediately) or CLS jumps. A/B against Rocket Loader before switching it off |
| Migration leftovers | Cache/security plugins from a previous host (LiteSpeed Cache or SiteGround Security on Kinsta) | Recommend deactivation; the owner decides |
| Forgotten vendors | trackers you can't find in the HTML (visitor ID, identity graphs) | Trace them to a snippet or GTM tag; when removed, flag privacy/cookie policy mentions for the owner |

## 5. Ship behind a flag, then verify logged-out
- Template: `assets/speed-cleanup-snippet.php`. It is a WPCode PHP snippet (run everywhere) that output-buffers on `template_redirect` for visitors without `edit_posts`. Each section is content-aware and the page gets a `<!-- … KB removed -->` marker. Fill in the image map and poster map for the site.
- **Test locally first** on real HTML. PHP via `npx -y @php-wasm/cli harness.php`. Get pages into the sandbox by POSTing them from the browser to a tiny local CORS receiver (Cloudflare challenges curl). Diff the script/link/style ids before and after per page.
- **Deploy in test mode**: the snippet only runs with `?<site>_speed_on=1`. Check that a plain fetch has no marker and a flagged fetch does.
- **Verify logged-out** in the built-in browser on the flagged URL. Check:
  - Console errors
  - Hero and logo screenshots
  - Swapped images render at the same size as the originals (compare against `?…_speed_off=1`)
  - Video shows its poster and plays
  - Form loads after a scroll
  - Network: no mp4 or form embed before interaction
- **Go live** only with the owner's OK: remove the test-mode line, clear the page cache (host and plugin).

## 6. Re-measure and report
- PSI full JSON again for the same URLs (2+ runs). Compare before and after: score range, LCP, TBT, CLS, transfer size, request count, HTML KB.
- Re-run the sitemap scan; Figma bytes and raster-SVG counts should be zero on every page.
- Report to the owner: a before/after table; what was removed and where; what's left (decisions needed: Rocket Loader, child theme CSS, plugin list, dead hidden forms, policy wording).
- Update the client notes file: snippet IDs, media IDs, baseline numbers and dates, open items.

## Quality bar
- Score alone isn't the goal. TBT under 300 ms, LCP under 2.5 s (lab mobile) and no layout shift from swapped images matter more.
- Every change is reversible in one step (deactivate the snippet + clear cache), and the notes file says how.
- No visual change unless agreed.
