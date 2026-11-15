# PlusAgency + Cleanto — Pass 75: Five public-site design systems

## Change summary
Built five distinctly composed and mobile-responsive Blade template layouts for residential, commercial, end-of-lease, carpet/upholstery, and window cleaning, plus individual CSS themes and full domain-specific starter JSON documents. Public dispatch now chooses a trusted template partial by the stored `template_key`; the other five Pass 74 template keys retain the original layout. The existing site draft, revision and publish controllers are unchanged.

## Scope
- 5 public Blade templates with distinctive section sequences, typography, hero illustrations made using CSS, service presentation and CTA placement.
- 5 custom responsive CSS files, shared focus/reduced-motion/mobile styles and source-supplied starter document JSON.
- SiteTemplates::make reads starter JSON for the first five categories while preserving the original remaining five.
- CTA defaults to `#contact`, not a misleading live booking form; displayed customer data is Blade-escaped.
- Contact section displays administrator-configured email/phone when provided; there is not yet an actual lead-capture form in this website starter surface.

## Release blockers
Pass 58 still missing. MariaDB schema, real publishing, multi-tenant isolation, GrapesJS round-trip, and cross-device browser checks are not verified. This is an enriched template rendering pass, not a certified ten-site product. Avoid claiming that full page visual editing or booking payment works.
