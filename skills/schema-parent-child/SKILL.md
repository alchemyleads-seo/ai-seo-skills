---
name: schema-parent-child
description: Rule and recipe for linking parent and child pages in structured data (JSON-LD). Use whenever a site has hub pages with sub pages (service hubs and sub-services, glossary and term explainers, platform or industry hubs, newsletter or series archives, tool hubs) or when publishing a new child page under an existing hub. Adds two-way links so search engines and AI see the hierarchy, using the type-appropriate property for each family.
---

# Parent/child schema rule

*From [AlchemyLeads](https://alchemyleads.com) · [ai-seo-skills](https://github.com/alchemyleads-seo/ai-seo-skills) · MIT license*

Every parent page and its child pages must be linked both ways in schema, using the property that fits the type. Never leave a child page whose markup doesn't name its parent, or a parent that doesn't list its children. Apply it when publishing any new child page, and when auditing a site.

## Patterns (pick by family)

| Family | Parent gets | Each child gets |
|---|---|---|
| Glossary / term explainers | DefinedTermSet (`#termset`) holding the terms | `Article` (`#article`, `about` → the term) + the `DefinedTerm` (hub `@id`, `inDefinedTermSet` → set, `url` = child page) |
| Service hub → sub-services | `Service.hasOfferCatalog` (OfferCatalog of `Offer.itemOffered` → child `#service`) + `CollectionPage.hasPart` → child `#webpage` | `Service.isRelatedTo` → parent `#service` + `WebPage.isPartOf` → parent `#collectionpage` |
| Content hub → topic pages (platforms, industries, tools) | `WebPage.hasPart` → child `#webpage` + an `ItemList` (`#<name>`) of children with position, name, url | `WebPage.isPartOf` → parent `#webpage` |
| Series (newsletter, podcast, course) | `CreativeWorkSeries` (`#series`, publisher org, `hasPart` → issues) | `WebPage.isPartOf` → `#series` |
| Case studies | `ItemList` directory of case studies on the hub page | `Article.about` → related Service `@id`s |

Rules:
- Reuse the `@id`s the site already outputs (crawl the page first; AIOSEO uses `<url>#webpage`, `#collectionpage`, `#service`, `#article`, `#faq`). Add properties by emitting a node with the same `@id`; never invent a second id for the same thing.
- Only state facts the page shows (names = page titles or H1s, definitions = existing visible text). No `sameAs` unless verified.
- Child-only parents that aren't real pages (e.g. a path segment with no page) are skipped; link the child to the nearest real parent.

## Recipe
- Emit the extra nodes through whatever page-level schema mechanism the site has: SEO-plugin custom schema, a theme hook, or one site-wide snippet holding a `nodes[path]` map that is appended to the plugin's graph. With a map, append (never replace) node arrays, check that existing nodes (e.g. FAQ counts) survive the round-trip, and back up the old config before saving.
- Crawl first: list the schema types and `@id`s per URL, then group pages into families with their ids and titles. Generate the nodes from that list rather than by hand.
- Verify live with a cache-busting query string: every family page has valid JSON-LD and the expected link properties.
