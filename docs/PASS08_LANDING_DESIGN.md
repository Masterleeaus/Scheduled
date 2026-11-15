# Pass 08 campaign design implementation

The campaign builder now stores a validated JSON design document in `titan_landing_campaigns.design_json`. Apply migration `2026_11_11_080001_add_design_to_titan_landing_campaigns.php` before editing campaigns. Legacy campaigns use defaults at render time without a destructive migration.

New fields: eyebrow, image URL/alt, up to 8 benefits, trust heading/text, layout variant, button shape and secondary colour. Existing lead capture and UTM tracking remain unchanged. Values are escaped in Blade and restricted on input. Image paths must be accepted by `ThemeFields::safeImage`.

**Not delivered:** Upload controls, shared site editor parity, tenant isolation, production AI editing, live DB/browser acceptance, conversion analytics. Those remain blockers, not silent successes.
