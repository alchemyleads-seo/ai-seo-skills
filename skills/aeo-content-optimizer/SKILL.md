---
name: aeo-content-optimizer
description: Audit and rewrite an existing page (live URL, draft or pasted copy) so ChatGPT and other answer engines are more likely to cite it — Key Takeaways up top, entity density, definitive language, question headings, self-contained chunks, HTML tables, verified statistics, freshness and FAQs — with every fix tied to a measured study in references/evidence.md. Picks only the checks that fit the page, verifies every statistic against its source sentence, stays additive-only on pages already ranking top 3, and flags non-content blockers (render, crawl, schema, entity) first. Built on Nectiv's MIT-licensed aeo-content-structure-optimizer (Nov 2026). Trigger on "optimize this page for AI search", "AEO pass", "make this citable", "why isn't ChatGPT citing this page", "content structure audit", or any page refresh aimed at AI citations.
---

# AEO content optimizer

*From [AlchemyLeads](https://alchemyleads.com) · [ai-seo-skills](https://github.com/alchemyleads-seo/ai-seo-skills) · MIT license*

Source: Chris Long, Nectiv, "New Claude Skill: AI Search Content Optimizer" (2026-11-02), skill at github.com/Nectiv-Digital/nectiv-claude-skills (MIT, see `LICENSE-nectiv`). Scripts and `references/evidence.md` are Nectiv's, unchanged. This file keeps Nectiv's method and adds guardrails suggested in the LinkedIn discussion of the release (Tim Lane, Rebecca Bridges, Muhammad Rizwan, Manuel Madeddu) plus a few of our own.

Evidence base (read `references/evidence.md` before diagnosing; cite factor IDs like `[POS-DOC]`):
- Kevin Indig, 1.2M ChatGPT citations: 44.2% of citations come from the first 30% of a page; cited text has 20.6% entity density vs 5–8% normal; definitive language 36.2% vs 20.2%; question marks 2x, 78.4% of them in headings.
- Nectiv: ChatGPT-cited pages contain a `<table>` 30% of the time vs 13% of Google results (2.3x), B2B SaaS only.
- Chris Green: controlled retrieval test, Q&A > headings+lists >> dense prose.
- AirOps: freshness windows by intent (vendor study, no control set; directional).
All correlational except Green's. Say so when a client asks.

`<skill-dir>` = wherever this skill is installed (e.g. `~/.claude/skills/aeo-content-optimizer`).

## 0. Before touching copy (the guardrails)

**a. Is this page a high ranker?** Check Google Search Console for the page's main query. If it sits in the top 3, the job is **additive only**: Key Takeaways, verified stats, FAQ, a table, a "Last updated" date. No rewording of existing body copy or headings. Don't risk a page that already wins.

**b. Is it a content problem at all?** Content is maybe half the job. Run these first and list failures in a "Not a content problem" section at the top of the deliverable:
- Rendering — does the body text exist in the raw HTML, without JavaScript? AI crawlers mostly don't run JS. If not, fix rendering before any rewrite.
- Crawler access — are GPTBot, OAI-SearchBot, PerplexityBot and ClaudeBot allowed in robots.txt and by the CDN/firewall?
- Schema — does FAQ/Article schema match the visible text?
- Entity and authority — is the brand a recognised entity with outside mentions? If AI answers cite third-party lists instead of the page, the fix is off-page (mentions, list inclusion), not a rewrite.
Also note page layout, CTA friction and missing proof (reviews, case studies) if they are obvious. Don't fix them in this skill; name them.

**c. Pick the checks that fit — write this list down before rewriting.** The model runs every check unless told not to. For each of the ten factors write `apply / skip / why` in one line:
- Skip TABLE on non-tabular informational pages (evidence is from comparison/roundup SaaS pages).
- Skip FAQ if the page already has a good one, or if it is a short service page whose FAQ lives elsewhere.
- Skip CHUNK bulletizing on opinion, story or newsletter pieces where voice matters more. Use bold lead-ins at most.
- Skip FRESH date checks on drafts.
- Only apply TAKEAWAYS to articles and guides, not homepages or landing pages.
Show the apply/skip list with the scorecard. If the owner says "just do it", apply only the `apply` rows.

## 1. Get the content
```
curl -sL -A "Mozilla/5.0" "<url>" -o page.html
python3 <skill-dir>/scripts/html_to_md.py page.html > page.md
```
Work in a scratch folder. Delete leftover chrome from `page.md`. If curl is blocked (many sites behind a CDN firewall return 403 to curl), read the page in a real browser, save its text, and skip `--html` checks; for drafts, use the local HTML. **Check `wc -l page.md` before analyzing:** a page built from divs instead of `<p>`/`<h2>` converts to 1–2 lines and the analyzer then reports 0 headings and 0% entities. If that happens, rebuild `page.md` from the browser's page text and add `##` to the headings by hand. Note target query, intent (commercial vs informational) and industry.

## 2. Measure
`python3 <skill-dir>/scripts/analyze.py page.md --html page.html` → JSON by factor ID. Numbers are heuristics (entity density is a proper-noun count). Use them for direction and before/after, never as exact study benchmarks.

## 3. Diagnose
Follow Nectiv's reading checks (in `evidence.md` and below): is the most valuable claim in the first 20–30%; is each section self-contained when cut out (~75 words, no pronoun openers); entity gaps; analyst voice; Key Takeaways (3–6 bullets, directly under the H1 — a Nectiv house rule, say so); page type for tables; freshness vs window.

## 4. Statistics: the verification protocol (strict)
Models hunting for stats return confident numbers that don't exist, and real URLs carrying the wrong number. For 2–3 key points only:
1. Search for the **primary** source (analyst firm, government data, the vendor's own study with a sample). Roundup pages ("100 SEO stats") are leads, never sources.
2. Open the source page itself (a fetch tool or a browser). Copy the **exact sentence** the number appears in, the publication date and the sample size.
3. Confirm the number in your draft matches the quoted sentence character for character ("most commonly 21–50%" stays that, not "most").
4. No source page that shows the number → leave a blank: `[STAT NEEDED: % of B2B buyers who use AI assistants in vendor research — no primary source found]`. Never fill it with a plausible number.
5. Every number gets a row in a **Sources** table: claim as written · exact source sentence (quoted) · URL · publisher · date · sample.
6. Existing numbers on the page: unverified is not false (`claims-context-humility`). Put untraceable ones on a "please confirm" list; correct only when the source clearly says something else, and say so. If the brand keeps a record of approved claims, check numbers against it.

## 5. Prioritize and rewrite
Rank by the factor priority in `evidence.md`, raised where this page misses badly. Seven sharp fixes beat twenty. Then follow Nectiv's rewrite rules:
- **Heading rule:** same headings, same order, same levels. Only allowed change is rewording an existing heading as a question, plus adding "Key Takeaways" if missing. Break long sections with bold lead-ins, not new headings.
- Opening answers the query directly with named entities and an "X is Y" definition.
- Definitive language pass from `DEFINITIVE.rewrite_candidates`. Never overclaim; keep true hedges and list them under "Left as is, for accuracy".
- Entity pass from `ENTITY.generic_references` and `zero_entity_sentences`, intro and first sentence under each heading first. Entities come from the page itself, well-known facts, the client's data, or a bracketed placeholder. **Never invent a brand, number or study.** Targets (directional): ≥12% overall, ≥15% in intro/takeaways, ≤25% sentences with no entity.
- Split paragraphs >120 words; answers within ~75 words of their heading.
- Tables as real HTML `<table>`, every cell from the page.
- Freshness: list the substantive updates; never recommend a date bump without content changes.
- Preserve the author's facts, examples, CTAs and voice. If the author has a voice or style guide, run the rewritten passages against it.

Write `page_final.md` (the full article), then:
`python3 <skill-dir>/scripts/check_rewrite.py page.md page_final.md` → must print `RESULT: PASS`. Every sentence marked rewritten or `DROPPED?` must map to a proposed change, otherwise put it back.

## 6. Re-measure
Rerun `analyze.py` on `page_final.md`. Report before → after honestly, including metrics that got worse and why.

## 7. Deliver
- **Client page:** Google Doc. Write `audit.json` (schema in `scripts/build_doc.py` docstring; add the Sources table and the "Not a content problem" items to the summary), `python3 <skill-dir>/scripts/build_doc.py audit.json > audit.html`, then upload the HTML to Google Drive as a Google Doc (Drive converts `text/html` uploads). Title: `AI Search Content Audit – <Client>: <page topic>`. Written for the client: no emoji, statuses Strong / Needs work / Weak, no description of how the audit ran.
- **Your own page:** skip the Doc. Review the scorecard, apply/skip list and changes, then publish through your normal process.
- Nothing goes live without the site owner's go-ahead.

## Adding studies
New study → new "Study N" section in `references/evidence.md` (source, dataset, findings with IDs, caveats), conflicts under "Conflicts & open questions", update the priority list. Candidates not yet added: Princeton GEO paper (Aggarwal et al., KDD 2024 — statistics and quotations raised visibility; read the paper before citing any figure), Mike Taylor's title test (see `agent-pass-titles`).
