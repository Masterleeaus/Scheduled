# Pass 09 — Integrated business site creation

- An administrator selects one of the existing 15 site presets and completes a business profile.
- **Manual** mode builds validated site documents using local template content, no AI API key required.
- **Assisted** mode uses the configured server-side AI provider to propose constrained website text, FAQs and services. It does not fabricate testimonials, prices, credentials or staff.
- Preview uses the same public theme renderer but has no publication action and `no-store` / `noindex` headers.
- Draft creation uses existing `titan_sites` records, editor, revisions and publishing, not a new website engine.
- AI provider failure rejects assisted creation instead of silently claiming successful generation. Manual mode is always available.
- Generated drafts remain fully editable through the existing supported editor fields; universal element editing is still an unresolved acceptance gate.
- No payments or booking provider changes in this pass.

Security: existing admin routes, CSRF checks, request validation and rate limits apply. Keep AI credentials server-side. Generated content is passed through `SiteDocument::decode` and escaped by the Blade templates.
