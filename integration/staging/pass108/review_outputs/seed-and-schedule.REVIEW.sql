-- SYNTHETIC NON-PRODUCTION seed candidate; NOT validated against MariaDB.
-- No bookings, payments, credentials or real customer records.
-- Review staff schedule and tenant configuration separately before using in acceptance.
INSERT INTO `ct_services` (`id`, `title`, `description`, `color`, `image`, `status`, `position`) VALUES (1, 'QA Residential Clean', 'Synthetic testing service', '#336699', '', 'E', 1);
INSERT INTO `ct_services_method` (`id`, `service_id`, `method_title`, `status`, `position`) VALUES (1, 1, 'Hourly', 'E', 1);
INSERT INTO `ct_service_methods_units` (`id`, `services_id`, `methods_id`, `units_title`, `base_price`, `minlimit`, `maxlimit`, `status`, `position`, `limit_title`, `unit_symbol`, `half_section`, `uduration`, `predefine_image`, `predefine_image_title`, `image`) VALUES (1, 1, 1, 'Hours', 50.00, 1, 8, 'E', 1, 'Hours', 'hr', 'D', 60, '', '', '');
INSERT INTO `ct_services_methods_units_rate` (`id`, `units_id`, `units`, `rules`, `rates`) VALUES (1, 1, '1', 'E', 50.00);
INSERT INTO `ct_admin_info` (`id`, `password`, `email`, `fullname`, `phone`, `address`, `city`, `state`, `zip`, `country`, `role`, `description`, `enable_booking`, `service_commission`, `commision_value`, `schedule_type`, `image`, `service_ids`) VALUES (1, 'DISABLED-TEST-ACCOUNT', 'staff@example.invalid', 'QA Staff', '0000000000', 'QA Address', 'Melbourne', 'VIC', '3000', 'Australia', 'staff', 'Synthetic test staff', 'Y', 'F', 0, 'W', '', '1');

-- DISPOSABLE REVIEW ONLY; weekly schedule assumptions require review.
-- weekday_id convention (1=Monday), week_id=1, schedule type W are NOT certified.
-- Verify against Cleanto availability engine before importing.
INSERT INTO `ct_week_days_available` (`provider_id`,`week_id`,`weekday_id`,`day_start_time`,`day_end_time`,`off_day`,`provider_schedule_type`) VALUES
(1,1,1,'08:00:00','18:00:00','N','W'),
(1,1,2,'08:00:00','18:00:00','N','W'),
(1,1,3,'08:00:00','18:00:00','N','W'),
(1,1,4,'08:00:00','18:00:00','N','W'),
(1,1,5,'08:00:00','18:00:00','N','W');
