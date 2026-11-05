---
name: webflow-publish-isolation
description: Rules for publishing on Webflow without leaking unrelated changes to production — staging-only publishes, CMS draft/queue states, API pushes, and the post-publish staging-vs-production diff. Load before ANY Webflow edit, CMS change, API push or publish for any client.
---

# Webflow publish isolation

*From [AlchemyLeads](https://alchemyleads.com) · [ai-seo-skills](https://github.com/alchemyleads-seo/ai-seo-skills) · MIT license*

Webflow publishes are **site-wide**. One publish (Designer button, API `site publish`, or CMS "Publish now") can release *everything* that is pending: Designer edits, styles, page settings, and any queued CMS items. Isolation is your job, not Webflow's.

## Mental model (verified on a client site, 2026-10-05)
| Thing | What goes out on a site publish |
|---|---|
| Designer/page/style/custom-code edits | **All of them**, to every checked domain. You cannot pick pages on legacy plans. |
| CMS item status **Published** (clean) | Already out; republished as is |
| CMS item **Changes in draft** | **NOT included.** The published version stays. Edits wait. |
| CMS item **Queued to publish** | Included in the next site publish, to the checked domains |
| CMS item **Draft** (never published) | Not included |
| CMS item-level **Publish now** | Publishes that item immediately, normally to **all** domains, production included. Never use it for staging-only work. |
| API publish (`POST /sites/{id}/publish`) | Same as the Designer button: whole site, to the `customDomains` you list. Omitting the list or including the prod domain = production release. API item create/update with `isDraft:false` or the `/live` endpoints = live immediately. |

So "Changes in draft" items are a safety buffer. Do not queue them unless the user approved *that item*.

## Staging-only publish procedure
1. **Inventory first.** In the CMS, list every item that is Queued or has Changes in draft, and note any Designer edits you didn't make. Tell the user what will ride along before publishing.
2. Make the edit. Rich text: use a real selection + `document.execCommand('insertText')` (raw typing via the extension can scramble characters). Read the value back.
3. To ship a CMS edit, open the item → Publish-now **dropdown** → **Queue for next site publish**. Queue only the approved items.
4. Designer → Publish → **staging checked, production unchecked**. Zoom/screenshot the dialog as proof before clicking. After publishing, confirm production still says "Published N days ago".
5. Re-read the item and confirm the status changed from Queued to Published.

## Site-settings dashboard publish (verified 2026-10-12)
- Site settings → Custom code (head/footer) changes ship with the next site publish, like Designer edits.
- The dashboard **Publish** button (webflow.com/dashboard/sites/<slug>/...) opens with **production PRE-CHECKED**, unlike the Designer dialog. Uncheck it, then confirm in JS that the last two checkboxes are [true,false] before clicking.
- Custom-code editors are CodeMirror 6. Get the view from `.cm-content` own props (`o.view.state`), edit with `view.dispatch({changes})`, JSON.parse every ld+json block before saving, then reload and re-read. Reading the whole doc back can be blocked by the output filter; return slices/counts instead.
- The diff script strips <script>, so schema changes show as IDENTICAL. Also diff raw HTML of a few pages to confirm the schema delta is the only change.

## Post-publish verification (mandatory)
Run `scripts/staging_vs_prod_diff.py <prod-origin> <staging-origin>`. It crawls the sitemap on both hosts and reports:
- production `Last Published` stamp (must be unchanged, from before your work)
- HTTP mismatches
- visible-text, image and inline-style diffs **grouped by signature** (a footer/nav CMS list change shows up as one signature on ~every page)
- whether the shared site CSS is identical

Every diff group must be explained by approved pending work. Anything unexplained = stop and report. Also curl the edited page on staging with a cache-buster (`?v=$RANDOM`) to confirm the actual change.

## Pitfalls
- A handoff saying "X was published to staging" is a claim. Verify it in the rendered HTML (one client's hero colors were reported as staged but were still Changes in draft).
- CMS fields can exist without being bound in the template (e.g. a collection's Meta title/description fields). Editing them changes nothing on the page; don't report them as SEO changes.
- The "Leave site?" dialog after a CMS edit doesn't always mean data loss. Check the item status (Saved / Changes in draft) instead of force-navigating.
- Draft rich-text fill can look right and not persist. Reopen the item and read it back.
- Keep a notes file per site with its collections, staging domain and publish history, and read it before publishing that site.
