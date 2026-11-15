# Pass 21 — 50 Business Vertical Integration

## Scope
Implemented **50 distinct business vertical configurations in 10 groups** in the existing site onboarding, site-document validation, template selection UI, and AI creation prompt. Six verticals are marked as new course subjects, but **no Academy courses were added**.

## Behaviour
- Operator chooses a vertical from the grouped select; frontend recommends an existing template while allowing another choice.
- Site onboarding validates the vertical and stores `vertical_key` in the site document.
- Non-cleaning verticals replace the default cleaning-service entries with a conservative industry-specific inquiry service; industry-specific legal/regulatory capabilities are **not** silently activated.
- AI prompt receives vertical and instructed to avoid unverified certifications and personal health advice.
- Existing saved sites without `vertical_key` remain valid.

## Limitations
- 50 verticals != 50 separate website designs or 50 courses. Existing 15 website templates remain the available design catalogue.
- Cleanto database booking operations have not been adapted or certified for trades, beauty, pet and other vertical workflows.
- Live browser, database and AI-provider testing pending.
- The catalog is now shared through a single JSON file and PHP service; broader cross-module filtering is later work.
