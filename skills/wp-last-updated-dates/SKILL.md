---
name: wp-last-updated-dates
description: Add and maintain a visible "Last updated" date on WordPress posts and AI SEO pages (bottom-of-funnel, glossary, platform guides), matched to schema dateModified. Use whenever publishing, refreshing or rebuilding a WordPress page for AI search, when asked to "add last updated", "show updated dates", "freshness signal", or when rolling the pattern out to a WordPress site.
---

# WordPress "Last updated" dates for AI SEO

AI engines and searchers both lean on freshness. Kevin Indig shared a case (Growth Memo, Oct 2026) where adding a visible "last updated" date lined up with roughly 3x AI citations. Treat that as a practitioner observation, not a promise. The date is cheap, honest, and it matches what schema already says.

## Rules

1. **The visible date equals schema `dateModified`.** AIOSEO, Yoast and Rank Math all output `dateModified` from WordPress `post_modified`, so the visible date uses `post_modified` too. Never show a date the schema contradicts.
2. **Only real edits move the date.** `post_modified` changes on every save, so don't re-save a page just to bump it, and batch trivial fixes (typos, tracking, link swaps) into real refreshes when you can. Google's guidance is to show a date only when the content meaningfully changed.
3. **Posts:** `By <author> · Published <date> · Last updated <date>`. Show "Last updated" only when it is more than a day after publishing.
4. **Pages:** a single `Last updated <date>` line, placed under the hero subtitle (`p.lede`) when the page has one, otherwise at the end of the content. Never at the very top of the content area, where it hides under a fixed header.
5. **Scope on AI SEO sites:** blog posts; bottom-of-funnel pages (cost, timeline, switching, "us vs" comparisons, pricing, why-us); glossary and "What is" pages; AI-search hubs and platform guides. Leave out legal pages, home, about, contact and thank-you pages.
6. Readers see dates in the site's audience time zone; stored dates stay UTC.

## Implementation (WPCode or Code Snippets PHP snippet)

Reference: `references/post-byline-example.php`.

- Hooks `the_content` (priority 20, once per request, only for the queried post), plus a `wp_head` style block.
- Page scope = an explicit bottom-of-funnel path list plus path prefixes (e.g. `/glossary/`). Add new page types to the list rather than turning it on for every page.
- **Staging gate:** `LU_LIVE` false shows the new output only to logged-in editors who add `?lu_preview=1`; visitors keep the old output. Flip to true to go live.
- On page-builder sites where the H1 and `p.lede` are inside `the_content`, the lede placement works. Pages built purely from builder elements fall back to the end of the content.

When editing a snippet in the WPCode editor through browser automation, set the CodeMirror value, sync the underlying textarea, click Update, then re-read the saved code server-side to confirm it saved.

## Rollout and verification checklist

1. **Before:** screenshot a post byline, a bottom-of-funnel page and a glossary page.
2. Save the old snippet code (a local copy).
3. Deploy with the gate off (`LU_LIVE` false); check `?lu_preview=1` as an editor on a post edited more than a day after publishing, a lede page, and a page without a lede.
4. Confirm a logged-out, cache-busted fetch still shows the old output.
5. Get the site owner's go-ahead, flip `LU_LIVE` to true, and clear the page cache (host and plugin).
6. **After:** a logged-out, cache-busted fetch on each page type shows the line; an out-of-scope page (e.g. /about/) doesn't; the visible date matches `dateModified` in the JSON-LD. Take after screenshots.

## Add this step to every WordPress AI SEO workflow

When a workflow publishes, refreshes or rebuilds a page (blog publish, bottom-of-funnel pages, content refresh, answer-first rewrites, schema work):

- [ ] The page shows "Last updated" (posts: next to Published) and it matches schema `dateModified`.
- [ ] If it's a new page type that should carry the date, add its path or prefix to the snippet's page list.
- [ ] Don't re-save a page only to bump its date.

## Before rolling out on a new site

- whether the theme already prints a date (hide or replace it, don't show two);
- which SEO plugin writes `dateModified` (AIOSEO, Yoast and Rank Math all use `post_modified`);
- the builder (Elementor or Divi pages may not route content through `the_content` the same way; test placement);
- caching (WP Rocket, LiteSpeed or the host) needs a purge after going live.
