---
name: client-question-rounds
description: "How to ask a client for information and turn their answers into a finished, approvable deliverable with the fewest possible rounds. Research the client's own materials first, ask 1-4 short questions with no homework, apply every answer to every surface (design + doc + notes), then ship a copy-for-approval doc. Use when drafting an intake/info request, questionnaire, 'what do we need from them', a reply to a client's answers, 'are these the last questions', or 'finalize it so I can send for approval'."
---

# Client question rounds

The rule: **do the work yourself before asking clients.** Clients (especially busy owners and execs at established companies) get tired of homework and of piecemeal rounds. Every question you send costs goodwill, so each one has to be something only they can answer.

## 1. Research pass before writing any question
Go through the client's own materials and answer everything you can yourself:
- Site pages on the topic (about, services, repair, contact, quote forms). Fetch them into a scratch folder.
- The downloads or resources library: catalogs, spec sheets, capabilities brochures, operation manuals, FAQs. List the PDF URLs (`grep -oE 'https?://<domain>/wp-content/uploads/[^"]+\.pdf'`), download them, and pull the text with PyMuPDF (`fitz`). Grep for the facts you need (testing, history, founding year, certifications, in-house processes, locations).
- Photos: get them out of the same PDFs (see Gotchas) and use them in the design rather than asking for photos.
- Your own client notes, chat history and earlier email threads. Something you think is missing (call tracking, routing, an owner's decision) may already be set up.
- Whatever you find from an old document goes in as a fact with its date ("per the 2019 brochure"), or becomes a yes/no confirm question.

## 2. Write the ask
- **1-4 questions maximum**, written plainly. Leave out legal explanations, internal process, claim-checking methods and outlines; those stay internal (an appendix or a separate doc).
- **Turn findings into confirm questions**: "Your 2019 brochure mentions three test cells. Is that still the setup?" beats "What testing do you do?"
- **One consolidated round.** Before sending, sweep every section of the deliverable for anything still unconfirmed (scope claims you wrote yourself, ownership, routing, sitewide changes, follow-up questions hidden inside an answer). Ask it all now or decide it internally. If someone asks "are these really the last questions?", the honest answer comes from that sweep.
- **Ask the account lead before asking the client** anything they can decide (routing, ownership, keep/change sitewide copy).
- Offer a fallback that removes pressure: "If you'd rather wait, we'll leave that section off at launch."
- **No "happy to hop on a call" line** unless the account lead adds it. No question about who approves if the contact is already clear.
- A small reference table (for example, what each claim level requires) is fine to include when it explains *why* you're asking. Keep it to one.
- Keep emails short when the sender and the contact know each other well, and send them as a reply in the original thread.

## 3. Apply each answer everywhere
When the client replies:
1. Restate what changed and any claim it now breaks. For example, a supplier list showing outsourced machining means "machined in X" is no longer safe, until the client says in-house work happens too.
2. Update **every** surface in the same pass: the design, the doc, the client facts notes, and any sitewide copy notes. Read the doc back afterward, because browser Find & Replace can fail without saying so.
3. Use the client's wording **word for word** when they give a line ("U.S. manufacturer", not "USA-based").
4. Keep sensitive specifics internal: supplier or vendor names, unit costs, contacts. The page shows service + city only unless the client explicitly approves names. Keep the full list in internal notes.
5. Record each decision in the client notes with a "don't raise again" flag when a topic is closed (for example, "footer stays, they have heard the risk").
6. Give the account lead a short report: what changed, open items (numbered), and the reply draft.

## 4. Finalize for approval
When the open questions no longer matter, or it's time to send:
- Remove sections that depend on unanswered questions. Don't ship placeholders.
- Fill every placeholder with real assets: the client's logo, certification badges, real photos, a real map. Remove internal notes, sticky notes and section numbers.
- Turn the doc into **"<Client> <Page> – Copy for Approval"**: the design link at the top, page details (URL, title tag, meta description, where it's linked from), then the copy section by section **matching the design word for word** (export the text from the design file so they match), then sitewide changes and behind-the-scenes notes (schema, internal links, photo sources, tracking). Keep the same doc link; the old content stays in version history.
- Send note: a few lines with both links, ending with "Reply with any edits, or just 'approved' and we'll build it."
- **Share the design file with view access** before sending, or the client can't open it.

## Gotchas
- **PDF photos:** in PyMuPDF, loop over `page.get_images(full=True)`, build a `fitz.Pixmap`, skip anything under about 500x300, convert CMYK to RGB, dedupe with an md5 of the samples, then save. Build a contact sheet (PIL) to choose from. Captions should say when photos predate a move or rebrand.
- **Real maps:** use `us-atlas` from jsdelivr (states TopoJSON), project and simplify it to an SVG, then place it. Don't use a drawn placeholder.
- **Before you say "X isn't tracked":** check for Google Ads call conversions (AW- IDs) as well as GA4 events. Phone clicks may already be tracked by a call conversion on the number, in which case a new page only needs `tel:+1...` links. Old UA events in GTM are legacy.
- Don't re-argue a risk the decision-maker has already heard and dismissed. Note it and move on.

## Reference example
A century-old industrial manufacturer, for a "Made in USA" page. The first intake was a long compliance questionnaire, which was rejected as "way too much homework." It was cut to 2-3 questions, and over several client rounds it became a final copy-for-approval doc plus a design. The company's own old brochure answered testing, history and repair scope and supplied the photos, which brought the last ask down from 7 questions to 0. Claim specifics are in `made-in-usa-claims-page`.
