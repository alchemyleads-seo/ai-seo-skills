---
name: entity-match-check
description: Prove a third-party profile or record belongs to the right company or person before using it anywhere: schema sameAs, Wikidata statements, pitches, review counts, proof lines, directory fixes, audits. Use whenever you are about to cite, link, count or edit a Clutch, G2, DesignRush, GoodFirms, Trustpilot, Crunchbase, LinkedIn, Google Business Profile, Inc., Yelp, BBB, social or Wikidata page for a brand, and whenever a number from such a page differs from what the brand has on record. Trigger on "check the Clutch profile", "review count", "sameAs", "entity", "is this our profile", "fix the listing".
---

# Entity match check

**Why:** in a real case, an agency's homepage Organization `sameAs` linked a Clutch profile with almost the same name. It belonged to a **different agency in another state**. The mistake spread:
- Google AI Mode cited the wrong profile when asked whether the brand was a legitimate company.
- A "dropped" review count was reported twice, because the count came from the other company.
- Outreach drafts went out quoting the wrong number before anyone caught it.

A name that looks right is not proof. Lookalike brands exist on every directory.

## The rule
Before you **link, count, quote or edit** any profile, it must pass **at least two hard anchors**. A matching name is never one of them.

| Anchor | How to check | Counts as hard? |
|---|---|---|
| Profile links to the official domain | The "Visit website" href (after following the redirect) or the JSON-LD `url` resolves to the brand's domain | yes |
| Logged-in owner view | The vendor dashboard's own "Preview profile" or "View public profile" link | yes, strongest |
| Address or HQ | Location matches a known office | yes |
| Phone or email | Matches the canonical contact | yes |
| Name | Exact canonical name or a registered alias | **no**, supporting only |
| Search-result position / "looks like us" | n/a | **never** |

If a page fails to show two hard anchors, stop. Don't use it; ask the brand owner or open the owner dashboard.

## When something looks off, suspect the entity first
- A review count, rating, founding year or location differs from the brand's records → **re-verify you have the right profile before calling it drift.**
- The profile name differs from the brand → treat it as a different entity until two anchors prove otherwise. Don't plan a "rename".
- More than one profile on the same directory → list all of them with their anchors. Only the one that passes is the brand's. Report the others as lookalikes; they're an entity-confusion risk, not the brand's to fix.

## Keep a canonical profile table
For every brand you work on, keep a table in its notes and re-verify entries older than 90 days:

| Platform | URL | Anchors | Verified |
|---|---|---|---|
| Clutch | … | owner dashboard preview; domain link | YYYY-MM-DD |
| NOT ours | … (lookalike) | different city, different domain | YYYY-MM-DD |
| Google Business Profile | share link | kgmid | YYYY-MM-DD |
| Wikidata | Q-id, or "none live" with deletion dates | `wbgetentities` | YYYY-MM-DD |

## Before writing schema `sameAs` (or Wikidata identifier statements)
1. Every URL passes the anchor table above. Record the anchors next to the URL in your notes.
2. The URL is live (200) and not deleted. For Wikidata, check `wbgetentities` for `"missing"`.
3. After saving, fetch the live page logged out and diff the `sameAs` list against the canonical table. Nothing extra, nothing missing.

## Audit mode (run when asked "check our entity" or quarterly)
- Decode every schema emitter on the homepage (theme, SEO plugin social profiles, custom snippets) and check each `sameAs` against the canonical table.
- Search each major directory for lookalike names and note any that AI engines cite for branded prompts (an AI-visibility tool's cited-sources report, or ask the engines directly). A lookalike cited for the brand's own prompts gets a counter-signal: the correct profile in `sameAs`, plus a complete profile on that directory.
