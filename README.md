# WhatsMyPhonePrice.com — Used Phone Valuation & P2P Marketplace (Pakistan)

## What This Is
A website where people in Pakistan get an instant, free estimated resale value for their used phone, then optionally list it for sale directly to other buyers (peer-to-peer) — with trust/verification features that neither existing competitor offers.

## Target User
Someone in Pakistan with a used phone, wanting to know what it's worth before selling — and optionally wanting to list it for a real buyer to find directly.

## Hosting / Tech Constraint (ASSUMPTION — confirm if wrong)
Assuming the same setup as our other project (shared hosting, e.g. Namecheap):
- Backend: **PHP + MySQL**
- Frontend: plain **HTML/CSS/JavaScript** — no Node build pipeline
- Deployable via simple file upload (FTP/cPanel)

## Domains
- Primary: `whatsmyphoneprice.com`
- Secondary: `whatsmymobileprice.com` — redirects to primary (captures type-in variants)
- **Do NOT** brand, style, or market this site in any way similar to "WhatMobile" (an unrelated, much larger competitor) — no similar logo colors, no comparisons, no references. Keep branding fully independent to avoid trademark/confusion risk.

## Business Model (core — do not deviate)
1. **Valuation Engine** — always free, no obligation, no forced sale to us. This is NOT a trade-in service (unlike competitor "mobilelyft" which forces users to sell to them).
2. **Optional: Post for Sale** — after getting a valuation, user can optionally list the phone (with the estimated price attached) for real individual buyers to see and contact directly. Peer-to-peer — we are never the buyer.

## Valuation Form Fields
- Brand + Model + Storage/RAM variant (e.g. "OnePlus 9 Pro 256GB")
- Screen condition (no scratches / minor scratches / cracked or lined)
- Touch/display fully working (yes/no — note any dead pixels or touch issues)
- Front + back camera working (yes/no)
- Battery health % — **optional field** (many Android brands don't expose this easily; see fallback below)
- Purchase year / age of phone — **required, used as fallback depreciation input** when battery health isn't available
- Battery symptom questions (fallback signal): lasts a full day of normal use? drains fast? randomly shuts off early?
- Water damage history (yes/no)
- Repair history (opened before? original vs. replaced screen/battery — non-original parts reduce value)
- Box included (yes/no) — treat as a small deduction only, not a big one (many phones are gifted without box)
- Charger included (yes/no)
- Headphones included (yes/no, if applicable)
- PTA approval status: PTA-approved / Non-PTA (patched) / Blocked — **major value factor, weight heavily**
- Network lock status (unlocked / carrier-locked)
- IMEI number (for PTA status verification feature)
- Original bill/invoice available (yes/no — trust signal)
- Color/variant
- **Real photos of the actual device required** (not stock images) — for human/buyer review only, see AI section below

## Valuation Algorithm
1. **Base price**: current new-phone market price for that exact model + storage variant, sourced by aggregating from PriceOye.pk, WhatMobile.com.pk, Mega.pk (average across sources, not just one)
2. **Age-based depreciation**: brand-specific curve — iPhones depreciate slower (~27–33%/year) than Android (~58–70%/year); use purchase year as primary age input
3. **Condition deductions**: screen/body damage, battery health if available (or battery symptom answers if not), non-original repair parts, water damage, missing box/charger (small deduction)
4. **PTA status adjustment**: this is a large deduction, not a small one — Non-PTA/patched phones carry real ongoing block risk and are priced significantly lower; Blocked phones are near-zero value
5. Label the result clearly as **"Estimated Price"** everywhere — never "Guaranteed Price" or "Final Price"

## Trust & Verification Features (build these — this is the core differentiator vs. OLX/mobilelyft)
1. **IMEI/PTA status checker**: seller uploads a screenshot of their SMS-8484 or DVS-app result, OR we deep-link to dirbs.pta.gov.pk for the buyer to verify themselves. No official PTA API exists — do not attempt automated scraping of PTA data.
2. **iCloud/FRP lock warning checklist**: for iPhones, a checklist reminding buyers to verify the seller can factory-reset to a clean home screen in person before paying (iCloud-locked phones are a common Pakistani scam). Same concept for Android Factory Reset Protection (FRP).
3. **Condition-locked listings**: the condition answers from the valuation form are locked onto the listing as a visible "condition card" — buyers see exactly what was declared, so misrepresentation is caught at the in-person meetup, not after payment.
4. **Verified profiles**: phone-number OTP verification earns a "Verified Seller" badge.
5. **Ratings & reviews**: buyers and sellers rate each other after a completed deal — this is what discourages dishonest listings over time (reputation cost).
6. **Bilingual scam-education content** (English + Urdu): how to factory-reset before selling, how to verify PTA status, how to spot advance-payment scams, a "safe deal checklist" shown before buyer/seller connect.
7. **City/area filtering** for listings, plus a curated directory of suggested safe public meetup spots per city (malls, well-known mobile markets).

## AI Usage — Explicit Scope (read carefully)
- **DO use AI (e.g. Groq's free/cheap text models) for**: aggregating and normalizing base prices scraped from PriceOye/WhatMobile/Mega.pk into clean structured data; generating plain-language explanations of the price breakdown for users.
- **DO NOT build AI-based photo condition grading or auto-pricing from photos.** This is a deliberate, final decision — not something to revisit without explicit instruction. Photos are for human/buyer review only. Do not add any "AI analyzes your photo" feature, even as an experiment.
- If later asked to explore a photo/AI inconsistency-flag feature, it must only ever flag for human review — never auto-set price, auto-publish, or auto-reject a listing.

## Explicit Non-Goals (do not build these)
- No forced-buyer/trade-in model (we are not mobilelyft — never require the user to sell to us)
- No AI photo-based condition grading or auto-pricing (see above)
- No automated scraping that bypasses logins/CAPTCHAs or violates robots.txt
- No escrow/payment holding, courier/COD integration, or warranty products yet (future phase, needs licensed partners — do not build custom payment-holding logic)
- No branding/naming/visual similarity to "WhatMobile" or any existing competitor

## Language / Market
- Bilingual: **English + Urdu**, mobile-first (most visitors on phones)
- Currency: PKR only

## SEO
- Every phone model should have its own indexable page (e.g. `/phone/oneplus-9-pro`) — not just a search box
- FAQ page answering real search queries (e.g. "how much is my phone worth in Pakistan", "PTA approved vs non-PTA price difference")
- Proper per-page `<title>` and `<meta description>`, unique per page
- FAQPage and Product schema.org structured data
- Server-rendered PHP (not JS-rendered content) — keeps content crawlable and AI-answer-engine-citable

## Build Order (suggested)
1. MySQL schema: phones (brand/model/variant + base price), valuations, listings, users, ratings
2. Valuation form (frontend) + PHP calculation endpoint using the algorithm above
3. Base-price aggregation script (AI-assisted normalization from the 3 source sites)
4. Listing creation (from a completed valuation) + listing detail page with condition card
5. IMEI/PTA checklist + iCloud/FRP warning content
6. User verification (OTP) + ratings system
7. Bilingual scam-education / safety pages
8. SEO: per-model pages, FAQ, schema markup, sitemap

## Another build order (suggestion)
1. MySQL schema: phones (brand/model/variant + base price), valuations, listings, users, ratings etc
2. see this https://github.com/rohail275/shadcn-ui for design and style or can use this approach (all beautifull ui in it)
3. use this https://github.com/rohail275/shadcn-ui but 1 thing you have to ahve in mind is i have shared base hosting to upload my site
4. 
