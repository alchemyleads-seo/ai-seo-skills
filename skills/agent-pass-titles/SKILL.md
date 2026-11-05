---
name: agent-pass-titles
description: Rules and a test method for writing titles, meta descriptions, excerpts and H1s that AI agents pick and cite when answering a user's question. Answer-shaped headlines beat clever, problem-first, credential-first and year-stamped ones, and agents back one page per topic per site. Use whenever writing or refreshing a title or meta description, auditing a site's titles for AI search, or picking which of several overlapping posts should win a topic. Trigger on "title and meta", "rewrite the headline", "agent pass", "why isn't this post cited", "which post should own this topic".
---

# Agent pass: titles and descriptions that agents cite

*From [AlchemyLeads](https://alchemyleads.com) · [ai-seo-skills](https://github.com/alchemyleads-seo/ai-seo-skills) · MIT license*

Source: Mike Taylor (head of evals, Every), Oct 2026 newsletter. He ran Claude agents on real user questions with his 38 posts as candidates and counted how often each was cited. One author, one site and an agent-picks-from-a-list setup, so treat the numbers as directional. The pattern was consistent across every question he tested.

Agents choose from the **title + description** (meta description, excerpt, subtitle). The body only matters after it's picked.

## The rules
1. **The description reads like the answer to the question.** When two posts were equally relevant, the answer-shaped one won 94% vs 1%, 100% vs 8%, 71% vs 0%. Taglines and opinions ("Evals are often all you need") lose to "Choose the best model for the work you do".
2. **Lead with what the reader gets.** "Prompt Engineering Guide: Five Principles With Examples" scored 100%. The same five principles led by the problem ("Why your AI answers are bland, and…") scored 54%, and led by credentials ("…From the co-author of O'Reilly's…") scored 50%. Put the deliverable in the first words: guide, checklist, cost, steps, comparison, number of items.
3. **No year stamps or "Ultimate".** "The Ultimate Guide to Prompt Engineering" went from 2% to 0% with "(Updated 2026)" added. Keep freshness in the body and the byline (`wp-last-updated-dates`), never in the title.
4. **One winner per topic per site.** Agents backed one post per question and mostly ignored siblings (92% vs 12% / 8% / 0%). Making one post more answer-shaped cut a sibling from 65% to 19%. Before rewriting, pick which URL should own the question. Make that one the obvious answer, angle the others at different questions, and point them at the winner with internal links.
5. **Write for a specific reader.** Agents cited a site more often when they knew who they were helping and that person matched its audience (42% with no user, 50% with a detailed persona). Name the ICP in the description when it fits ("for manufacturers", "for B2B teams").
6. **Two agent audiences.** An agent answering for a user wants the clearest answer (how-tos, costs, comparisons). An agent browsing for itself prefers original experiments and data on AI behavior and reliability. Reader traffic and agent picks had zero correlation. Original test results (your own citation data, experiments) do well with both.

## Rewrite pattern
`<What they get>: <specific scope or number> <for whom, if it fits>` + description: one sentence that answers the question, a second with the concrete payoff.
- Before: "Why We Work With Industrial Hard Tech Companies" (a "why us" opinion)
- After: "AI Search Marketing for Industrial and Hard Tech Companies: What Works" + "How manufacturers get cited by ChatGPT and Google AI Overviews, with the steps and results from industrial clients."

Then run your house gates. Ours: approved numbers only, no contrast framing ("not X, but Y"), no em dashes, ≤60-char meta title for posts (≤70 for pages), ≤155–160-char description. Rewrites must be truthful to the body. Don't promise a checklist the post doesn't contain.

## Checklist per title + description
- [ ] Would an agent with the target question read this as the answer?
- [ ] Do the first 5 words say what the reader gets?
- [ ] No problem-first or credentials-first opening, no tagline, no rhetorical question
- [ ] No year, "ultimate", "complete" or "definitive"
- [ ] It's the one URL we want to win this question, and siblings are angled elsewhere
- [ ] Excerpt, OG description and SEO-plugin meta description are aligned (agents may read any of them)

## Testing variants (optional, for pillar pages)
Replicate Taylor's setup before shipping a big rewrite:
1. Write 3–5 real buyer questions (from long Search Console queries or an AI-visibility tool's tracked prompts) and 1–2 personas from the client's ICP.
2. Build a candidate list: the variant titles + descriptions mixed with 10–20 competitor results for the same question (title + description as they appear in search), shuffled per run.
3. Ask a Claude model (default the latest Sonnet) with web tools off: "Your user asks: <question>. <persona>. Which of these would you read and cite? Return the IDs." Run 30–50 sessions per variant and count citation share.
4. Ship the winner and log the before/after share in the site's notes. Taylor packaged his version as a Claude Code skill called `agent-pass`. Check its terms before borrowing from it.
