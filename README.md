# AI SEO skills for Claude

Claude skills from [AlchemyLeads](https://alchemyleads.com) for AI search optimization, website QA and marketing compliance. Each folder under `skills/` is one skill: a `SKILL.md` with instructions, plus any scripts or references it uses.

## Install

Copy a skill folder into `~/.claude/skills/` (Claude Code) or upload it as a custom skill in Claude. Claude loads a skill when a request matches its description.

```bash
git clone https://github.com/alchemyleads-seo/ai-seo-skills.git
cp -R ai-seo-skills/skills/<skill-name> ~/.claude/skills/
```

## Skills

### AI search content
| Skill | What it does |
|---|---|
| [aeo-content-optimizer](skills/aeo-content-optimizer) | Audit and rewrite a page so ChatGPT and other answer engines are more likely to cite it, with every fix tied to a published citation study. Built on Nectiv's MIT-licensed optimizer. |
| [agent-pass-titles](skills/agent-pass-titles) | Write titles and meta descriptions that AI agents pick when answering a question, plus a method to test variants. |
| [entity-match-check](skills/entity-match-check) | Prove a directory or social profile belongs to the right company before linking it in schema or quoting its numbers. |
| [schema-parent-child](skills/schema-parent-child) | Link hub and child pages both ways in JSON-LD, with the right property for each page family. |

### Website QA
| Skill | What it does |
|---|---|
| [site-speed-js-cleanup](skills/site-speed-js-cleanup) | Measure, inventory every script, and remove what each page doesn't use, behind a test flag. Includes a WordPress snippet template. |
| [wave-accessibility-qa](skills/wave-accessibility-qa) | Run WebAIM WAVE across a site, triage the errors and fix them. |
| [privacy-policy-qa](skills/privacy-policy-qa) | Check the privacy policy, terms and cookie notice against what the site actually loads and collects. |
| [webflow-publish-isolation](skills/webflow-publish-isolation) | Publish on Webflow without pushing unrelated changes to production. |
| [wp-sitewide-text-sweep](skills/wp-sitewide-text-sweep) | Change a phrase on every page of a WordPress site, including page-builder content. |
| [wp-last-updated-dates](skills/wp-last-updated-dates) | Add a visible "Last updated" date that matches schema `dateModified`. Includes a WordPress snippet. |

### Claims and compliance
| Skill | What it does |
|---|---|
| [marketing-compliance-gate](skills/marketing-compliance-gate) | Pre-publish check for claims, testimonials, reviews, pricing, privacy and regulated copy. |
| [claims-context-humility](skills/claims-context-humility) | How to treat claims you can't verify in existing copy: flag and confirm, don't delete. |
| [made-in-usa-claims-page](skills/made-in-usa-claims-page) | Plan and word a "Made in USA" / Buy American page so its claims hold up. |
| [client-question-rounds](skills/client-question-rounds) | Get what you need from a client in one short round, then turn it into an approvable deliverable. |

## Credits

- `aeo-content-optimizer` is adapted from [Nectiv's aeo-content-structure-optimizer](https://github.com/Nectiv-Digital/nectiv-claude-skills) (MIT, © 2026 Nectiv Digital). Its scripts and `references/evidence.md` are Nectiv's, unchanged; their license is in `skills/aeo-content-optimizer/LICENSE-nectiv`.
- `agent-pass-titles` summarizes findings from Mike Taylor's agent citation tests (Every, Oct 2026).
- Studies cited in the skills belong to their authors: Kevin Indig (Growth Memo), Chris Green, AirOps, Nectiv.

These skills are working methods, not legal advice. Check compliance rules against current official sources before relying on them.

## License

MIT. See [LICENSE](LICENSE).
