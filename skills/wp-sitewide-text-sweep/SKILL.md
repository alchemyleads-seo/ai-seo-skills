---
name: wp-sitewide-text-sweep
description: Find and change a phrase, claim, number, or name on EVERY page of a WordPress site, including page-builder content (Cornerstone/Elementor), ACF fields, schema JSON-LD, and image alts, then prove zero residuals with a full logged-out sitemap crawl. Use for "remove all X references site-wide", "update this number everywhere", "anonymize client names", or claim corrections.
---

# WP Site-wide Text Sweep

*From [AlchemyLeads](https://alchemyleads.com) · [ai-seo-skills](https://github.com/alchemyleads-seo/ai-seo-skills) · MIT license*

## 1. Inventory (before touching anything)
Crawl every sitemap URL logged-out in the browser (`scripts/sweep.js`, 6 workers, results in `window.__res`) and search the raw HTML, not just the visible text: names hide in alt attributes, JSON-LD, video titles, and `@id` fragments. Group the hits by exact phrase and context. List pattern families (first + last name, "Owner, <Brand>", "client, <Brand>, we", alt text) and exclude staff and other false positives.

## 2. Choose where to fix it
| Source | Fix at |
| --- | --- |
| Plain WP page/post content | REST edit of `content.raw` |
| ACF fields | Edit screen: set value, dispatch `change`, Update |
| Page-builder data (Cornerstone etc.) | The builder UI, or a render-time rewrite |
| Many pages across the builder and plugins | One render-time `strtr` snippet (`template_redirect` + `ob_start`, base64 JSON map, frontend only: skip admin/AJAX/REST/feeds) |
| Alt text injected by another snippet | That snippet's map; downstream buffers can't see it |

Render-time rewrites are reversible and cover everything, but the source text stays stale. Note that in the site's notes, and fix at source when a page is rebuilt.

## 3. Build the map safely
- Longest phrase first (PHP `strtr` with an array already does this). Add context-specific phrases before generic fallbacks so the grammar works ("client, X, we" → "client, a cleanroom manufacturer, we").
- Protect strings that must survive by mapping them to themselves (e.g. `alt="Brand"` → `alt="Brand"` for a logo strip you're keeping).
- Never touch URLs or filenames: hyphenated slugs won't match spaced names, but check.
- Simulate the map in JS against the captured HTML first (same longest-first regex) and print residuals. Ship only when the residuals are the expected exceptions.
- In JS, use `split/join` or a function replacement, never `.replace(str, str)`: `$` in prices ("$5M") gets interpreted.

## 4. Verify
Clear every cache layer, rerun the full crawl, and assert 0 residuals (outside the documented exceptions) and that every JSON-LD block still parses. If something survives, find which code injects it after your buffer (search every snippet's decoded source) and fix it there.

## Lessons
- A localStorage hand-off was written as the literal string "undefined" because the window var died on navigation. Recompute inside the job instead of relying on state from a previous page.
- `do_shortcode` inside an `ob_start` callback blanked a page. Precompute in `template_redirect`.
- Output-buffer layering: callbacks started later run first; an a11y alt-map snippet ran after the anonymizer.
