---
name: privacy-policy-qa
description: QA a website's Privacy Policy, Terms of Use, Disclaimer, cookie notice and compliance footer against what the site actually does. Inventory third-party hosts and trackers per route, diff them against the vendors and data categories the policy discloses, check the required-section checklist (CCPA/CPRA-style notices, sale/share opt-out, GPC, rights and methods, retention, minors, contact, effective date), check that the legal docs agree with each other and with marketing claims, and confirm the compliance links render on every route. Use for "privacy policy QA", "is our policy accurate", "update the terms", legal-page refreshes, or after adding any tracking or vendor. Complements consent-tracking-audit (consent behavior) and privacy-operations-handoff (request operations).
---

# Privacy Policy QA

*From [AlchemyLeads](https://alchemyleads.com) · [ai-seo-skills](https://github.com/alchemyleads-seo/ai-seo-skills) · MIT license*

You are checking accuracy and completeness against observed evidence. You are not giving legal advice. Mark legal-applicability questions "for counsel" and verify current law from authoritative sources (state AG/agency pages, statute text) before stating any requirement. Never invent business commitments (retention periods, response deadlines, entity names, incentives); ask the owner and leave a marked placeholder.

## 1. Evidence: what the site actually loads
Run `scripts/third_party_inventory.js` in the browser on the live site. It crawls every sitemap URL logged-out and records third-party hosts from `script/src`, `iframe/src`, `img/src` (pixels), `link/href` (preconnect/preload), and inline-script vendor fingerprints (gtag, GTM, Meta pixel, HubSpot, PostHog, Hotjar, Clarity, LinkedIn Insight, TikTok, Calendly, YouTube, Vimeo, Google Fonts, reCAPTCHA). Output: a host → route-count table plus vendor fingerprints. Static HTML can't see tags injected later by GTM. For those, list the GTM container's tags (or record "GTM not inspectable from this login") and add a consent-tracking-audit network check.

## 2. Diff the policy against the evidence
Build a table: vendor/host | purpose | data likely sent | disclosed in policy? (quote the sentence) | consent dependency | action. Flag:
- Vendors loaded but not disclosed. The fix is a policy update or removing the tag.
- Vendors disclosed but no longer loaded. Stale is a lower risk, but clean it up.
- "We do not sell or share" claims while ad pixels or retargeting run. Route to counsel and check that the Do Not Sell/Share link and GPC handling exist.
- Session replay, form-field capture, or enhanced matching that the policy doesn't describe.

## 3. Required-section checklist (verify each against current authoritative sources)
Last updated / effective date · who we are and how to contact us (a monitored address) · categories of personal information collected, with sources · purposes · disclosure and sale/sharing to third parties by category · sensitive information · cookies/tracking with a link to the preferences control · opt-out of sale/sharing (link + GPC statement) · consumer rights and how to exercise them (methods, verification, authorized agents, appeals where applicable) · retention by category · children/minors · security statement (no absolute guarantees) · international transfers if applicable · changes-to-policy clause · state-specific sections the business actually triggers (counsel decides applicability).

## 4. Cross-document consistency
Read Privacy, Terms, Disclaimer, cookie notice, accessibility statement, MSA/SOW, and the pricing/marketing pages side by side. Flag any conflict, such as Terms promising reports the MSA removed, a disclaimer contradicting a results claim, different entity names or addresses, governing-law mismatches, or contact emails that differ. The brand's approved claims record wins for facts; legal docs win for obligations. Escalate conflicts; don't silently pick a side.

## 5. Rendering checks on every route
The compliance row must render on every sitemap URL (logged-out crawl) with working links (status 200, no redirect loops): Cookie preferences (opens the consent UI), Do Not Sell/Share, Privacy policy, Terms of use, Disclaimer, Cookie info, Accessibility help. Legal pages are excluded from decorative builder CSS (centering, animations) so they stay readable. Each legal page has a visible "last updated" date that matches the content change.

## 6. Ship
Draft changes as a redline with a citation for each change (evidence row or source URL). Publish only with the owner's approval. For builder pages, put the new text in the builder (hide old sections, don't delete). Record the new effective date. Re-crawl and re-diff to prove the evidence and the policy now match.
