# Pass 24 — Industry Website Booking Requests

Four new site themes now show industry-specific appointment-request forms in the existing site lead workflow: pet care, fitness, yoga/Pilates, and vehicle detailing. Every form captures a site-selected service, preferred date, preferred time, optional business-type details, customer contact, consent and message. Requests are stored in `titan_site_leads` through the established site submission controller. They are not confirmed Cleanto reservations. Staff must follow up to confirm availability; no booking commits or charges are performed by this integration.

The new migration adds `request_kind`, `preferred_date`, `preferred_time`, `booking_details` to the site lead table. Deploy it before using new forms. Existing enquiry submissions stay compatible after migration. Leads admin lists the preference details. Existing published site records keep their content while their themes gain a Booking link and form. Four new presets now point their CTA at `#booking`.

Full live reservation integration remains blocked until Cleanto is configured for each business vertical and MariaDB concurrency tests pass. The underlying booking engine is untouched. Landing templates are unchanged. No live browser/database acceptance performed.
