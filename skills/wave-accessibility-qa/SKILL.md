---
name: wave-accessibility-qa
description: Run WebAIM WAVE accessibility checks across a site's route inventory (WAVE API batch or WAVE browser-extension snapshots), triage errors, contrast errors and alerts by shared template, apply the proven WordPress/Cornerstone fix patterns, and re-scan to prove the counts dropped. Use for "WAVE compliance", "run WAVE", "fix accessibility errors", ADA/WCAG cleanup passes, or as the pre-publish accessibility gate on new pages. Complements shared-template-accessibility (remediation method) and site-privacy-accessibility (portfolio rollout).
---

# WAVE Accessibility QA

WAVE counts are evidence, not a conformance verdict. Never say a site is "ADA compliant" or "WCAG conformant" because WAVE shows 0 errors. Manual keyboard, screen-reader, and zoom checks remain in the backlog.

## 1. Route inventory (one URL per template and state)
From the sitemap, pick one URL per template: home, page-builder pages, each custom kit (`.alv`, `.alp`), blog post, case study, industry CPT, service CPT, legal pages, forms (/call/), 404, author, search. Add states that need interaction (open nav and mega menu, mobile drawer, cookie banner open, FAQ `<details>` open). Templates repeat, so scan representatives and fix once.

## 2. Scan
- **API (preferred for batches):** `scripts/wave_api_scan.py urls.txt --out wave.csv`, with the key in `WAVE_API_KEY` (WebAIM API credits, owned by the client or agency; never hard-code it). It records errors, contrast, alerts, features, structure, ARIA, plus per-item IDs and counts. The script fails loudly on API errors and never writes zeros for a failed scan.
- **Extension/manual:** WAVE browser extension on each URL and state; save the DOM/summary snapshot and use `shared-template-accessibility/scripts/summarize-wave.py` for the saved format.
- Logged-out only (the admin bar adds false findings). Clear caches first. A Cloudflare challenge, redirect, or raw image URL is not a scanned page: exclude it and note why.

## 3. Triage (per template, not per page)
| WAVE item | Usual cause in WordPress/Cornerstone | Fix pattern |
| --- | --- | --- |
| `alt_missing`, `alt_link_missing` | Builder images inserted without alt | Set the Media Library alt, plus a render-time alt map keyed by upload path for builder images (a small PHP snippet that fills empty alts on output). Decorative images get `alt=""` only after a visual check. |
| `link_empty`, `button_empty` | Icon-only links (social, carousel arrows, logo) | `aria-label` naming the destination or action |
| `contrast` | Muted text on dark, orange text on white | Use token pairs that pass 4.5:1 (or 3:1 for large text); recheck hover/focus states |
| `heading_skipped`, `heading_empty` | Builder headings chosen for size | Fix the level in the builder; style with classes |
| `label_missing` | Embedded forms (HubSpot, CF7) | Visible `<label for>` or `aria-label`; never submit a live form to test |
| `language_missing` | Theme head | `<html lang>` |
| `region_missing` / alerts | No `<main>`/landmarks in builder output | Landmark wrappers in the header/footer templates |
| `aria_reference_broken` | Copied components with duplicated IDs | Unique IDs per instance |
Also check things WAVE can't see: visible focus (`:focus-visible` outline in brand orange), skip link, keyboard access to the mega menu, `<details>` FAQs, carousels you can pause, and 200% zoom with no horizontal scroll.

## 4. Fix narrowly, then re-scan
Prefer source fixes (builder, Media Library). Use a render-time snippet only for theme-file output (`DISALLOW_FILE_EDIT`). Re-scan the same URLs/states and keep before/after CSVs. Report per template: before → after counts, the remaining items with their reasons (intentional or contextual alert), and the manual backlog.

## 5. Pre-publish gate for a new page
Before anyone approves publishing: WAVE errors = 0 and contrast errors = 0 on desktop and at 390px; every image has a meaningful or empty alt; the heading order has no skips; FAQ items are keyboard-operable.
