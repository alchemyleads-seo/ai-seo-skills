---
name: marketing-compliance-gate
description: Pre-publish compliance gate for marketing pages, case studies, testimonials, review widgets, pricing pages, schema and video metadata. Checks claim substantiation against the brand's claims record, testimonial/endorsement and review-display rules, client privacy (anonymization), results and typicality disclosures, pricing accuracy, regulated-vertical copy (health, finance), deliverable promises vs the contract, and that the accessibility and privacy gates ran. Use before publishing or approving any public marketing content (yours or a client's), and when asked to "check compliance" on copy.
---

# Marketing Compliance Gate

*From [AlchemyLeads](https://alchemyleads.com) · [ai-seo-skills](https://github.com/alchemyleads-seo/ai-seo-skills) · MIT license*

Run it on every page before asking for publish approval. Output a short PASS/FLAG table with evidence. You flag; the owner or counsel decides. Verify rules against current official sources (FTC endorsement guides and consumer-review rule, relevant state law) before citing them. Don't quote rule numbers from memory.

## Checks
1. **Claims substantiation:** every number, superlative ("best", "#1", "only"), and outcome claim maps to an approved claim on record or a published case study/review, quoted with its exact wording (pipeline is not revenue; 95% retention is not 96%). Unsupported claims are FLAGGED and get rewritten or removed. **Client pages are different:** a client's statements about their own company, experience, products and service are presumed true (they know their business better than we do, and a PR check runs later). Keep them. Flag them only when the client's own live data contradicts them, or when they fall in a regulated area: health or medical efficacy, Made in USA, certifications without a source, guarantees or warranties, or competitor comparisons. Flag inline; don't delete (see `claims-context-humility`).
2. **Results and typicality:** standout case-study results next to sales copy need context (timeframe, what was measured, "results vary"). Don't imply that every client gets the best result.
3. **Testimonials and reviews:** they must be real and traceable to a source (Google review, Clutch, a signed video release). Edits only trim (use an ellipsis) and never change meaning. Disclose any material connection (discount, free work, affiliate). Never invent, merge, or upgrade reviews. Review schema only marks up reviews that are visible on the page. Never imply a third-party rating you didn't receive.
4. **Client privacy:** names are first name + last initial and companies are described, unless written permission exists. No client revenue or ad-spend dollars unless anonymized and approved. Check alt text, file captions, JSON-LD, and video titles/descriptions too.
5. **Pricing accuracy:** prices, tier inclusions, and "starting at" figures match the live pricing source. No hidden terms contradicted by the MSA/Terms. Don't mention contract length if the owner excluded it.
6. **Deliverable promises vs the contract:** nothing in the copy promises what the MSA/SOW doesn't (check deliverable words like "reports", "dedicated", "unlimited" against the contract).
7. **Regulated verticals:** health and medical device copy makes no treatment, efficacy, or off-label claims and stays inside the client's regulatory review. Finance copy makes no guaranteed returns. Legal or medical-adjacent pages carry the appropriate disclaimer link.
8. **AI/automation honesty:** don't claim guaranteed AI-assistant citations or rankings. Describe the process and evidence, not guarantees.
9. **Brand rules:** the house spelling of the brand name, and no impersonation of other brands. Competitor comparisons use verifiable public facts with sources.
10. **Gates ran:** `wave-accessibility-qa` pre-publish gate (0 errors and 0 contrast errors at desktop and 390px), `privacy-policy-qa` if the page adds a vendor, embed, or form, and FAQ/Service schema mirroring the visible text.

## Output
| # | Check | PASS/FLAG | Evidence (quote + source/URL) | Fix |
End with: "Ready for approval" only if there are no FLAGs, otherwise the numbered decisions needed from the owner.
