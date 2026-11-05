---
name: claims-context-humility
description: How to treat claims, stats and numbers you can't verify in a brand's existing copy. Unverified is NOT false. Load whenever auditing, rewriting, de-duplicating or QA-ing existing copy, or writing reports that flag claims (site audits, refreshes, de-generic passes, pitch QA, subagent prompts).
---

# Claims: unverified is not the same as false

*From [AlchemyLeads](https://alchemyleads.com) · [ai-seo-skills](https://github.com/alchemyleads-seo/ai-seo-skills) · MIT license*

**Why:** an assistant auditing copy will treat every number it can't find in its own sources as "invented" or "unsupported", then remove or flag true claims: conversion stats, client results, industry figures. The business owner has years of client context, call notes, dashboards and conversations the assistant never sees. Any written record of approved claims is a **partial** record of what's been written down, not a complete list of what's true.

## Rules
1. **Existing copy the business published is presumed accurate.** Missing from the claims record means "not yet recorded", nothing more.
2. **Never delete or rewrite an existing claim just because you can't verify it.** Leave it in place and put it in a short "please confirm" list. Remove only when:
   - (a) it contradicts the claims record (a different figure for the same metric, an outdated target-customer range), or
   - (b) the owner says to remove it.
3. **Neutral wording when flagging.** Say "not on record yet: confirm?". Never say "invented", "made-up", "fake" or "unsupported". Don't assume two similar numbers are the same claim (they can be different clients).
4. **Record-only numbers apply to NEW claims you write.** They don't apply to claims already on the site. When you write new copy, only use numbers from the claims record or published case studies, or ask.
5. **De-duplication is not fact-checking.** When removing repeated text, keep the claim on at least one page and relocate it. Never drop it everywhere.
6. **When the owner confirms a claim, add it to the claims record right away** (with the source if given), so it's never flagged again.
7. **Subagent prompts must carry this rule.** Paste rules 1 to 5 into any agent prompt that audits or rewrites copy.
8. **Keep the "confirm" list short, and don't nag.** Batch the items once and drop the ones the owner waves off.

## Still hard rules
- Contradictions with the claims record get fixed, not flagged.
- Regulated categories: healthcare and medical stays FDA/FTC-safe, and finance makes no guaranteed returns. Even then, soften the wording rather than deleting the substance, and flag it.
- Client anonymization rules always apply.
