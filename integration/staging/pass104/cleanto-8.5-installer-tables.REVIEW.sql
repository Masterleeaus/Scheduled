-- REVIEW CANDIDATE: Cleanto installer literal CREATE TABLE declarations only.
-- NOT equivalent to an installed/upgraded Cleanto 8.5 schema.
-- Missing installer ALTERs, seed data and version migrations must be reviewed.
-- SOURCE SHA256 ec69004d6f364c3037bb771a86f4e3396fd10b07f6b679e1ff56232c78266ce9

-- ct_addon_service_rate
CREATE TABLE IF NOT EXISTS `ct_addon_service_rate` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `addon_service_id` int(11) NOT NULL,
    `unit` varchar(20) NOT NULL,
    `rules` enum('E','G') NOT NULL,
    `rate` double NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ct_admin_info
CREATE TABLE IF NOT EXISTS `ct_admin_info` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `password` varchar(50) NOT NULL,
    `email` varchar(250) NOT NULL,
    `fullname` varchar(70) NOT NULL,

    `phone` varchar(15) NOT NULL,
    `address` varchar(250) NOT NULL,
    `city` varchar(48) NOT NULL,
    `state` varchar(48) NOT NULL,
    `zip` varchar(10) NOT NULL,
    `country` varchar(48) NOT NULL,
    `role` varchar(20) NOT NULL,
    `description` varchar(320) NOT NULL,
    `enable_booking` enum('Y','N') NOT NULL,
    `service_commission` enum('F','P') NOT NULL,
    `commision_value` double NOT NULL,
    `schedule_type` enum('M','W') NOT NULL,
    `image` varchar(250) NOT NULL,
    `service_ids` varchar(255) NOT NULL,
    `staff_wallet_amount` int(100) DEFAULT 0,
    `paypal_api_username` varchar(100) DEFAULT NULL,
    `paypal_api_password` varchar(100) DEFAULT NULL,
    `paypal_api_signature` varchar(100) DEFAULT NULL,
    `paypal_test_mode_status` varchar(10) DEFAULT NULL,
    `revenue_percentage` int(11) DEFAULT 0,
    `latitude` varchar(100) DEFAULT NULL,
    `longitude` varchar(100) DEFAULT NULL,
     PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ct_bookings
CREATE TABLE IF NOT EXISTS `ct_bookings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` bigint(20) NOT NULL,
    `client_id` bigint(20) NOT NULL,
    `order_date` date NOT NULL,
    `booking_date_time` datetime NOT NULL,
    `service_id` int(11) NOT NULL,
    `method_id` int(11) NOT NULL,
    `method_unit_id` int(11) NOT NULL,
    `method_unit_qty` double NOT NULL,
    `method_unit_qty_rate` double NOT NULL,
    `booking_status` enum('A','C','R','CC','CS','CO','MN','RS') NOT NULL COMMENT 'A=active, C=confirm, R=Reject, CC=Cancel by Client, CS=Cancel by service provider,CO=Completed,MN=MARK AS NOSHOW',
    `reject_reason` varchar(200) NOT NULL,
    `reminder_status` enum('0','1') DEFAULT '0' COMMENT '0=Email Not Sent,1=Email Sent',
    `lastmodify` datetime NOT NULL,
    `read_status` enum('R','U') DEFAULT 'U',
    `staff_ids` varchar(160) DEFAULT NULL,
    `gc_event_id` varchar(255) DEFAULT NULL,
    `gc_staff_event_id` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ct_booking_addons
CREATE TABLE IF NOT EXISTS `ct_booking_addons` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` bigint(20) NOT NULL,
    `service_id` int(11) NOT NULL,
    `addons_service_id` int(11) NOT NULL,
    `addons_service_qty` int(11) NOT NULL,
    `addons_service_rate` double NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- ct_coupons
CREATE TABLE IF NOT EXISTS `ct_coupons` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `coupon_code` varchar(250) NOT NULL,
    `coupon_type` enum('P','F') NOT NULL,
    `coupon_limit` int(11) NOT NULL,
    `coupon_used` int(11) NOT NULL,
    `coupon_value` double NOT NULL,
    `coupon_expiry` date NOT NULL,
    `status` enum('on','off') NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ct_frequently_discount
CREATE TABLE IF NOT EXISTS `ct_frequently_discount` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `discount_typename` varchar(20) NOT NULL,
    `days` int(5) DEFAULT '0',
    `d_type` enum('F','P') NOT NULL,
    `rates` double NOT NULL,
    `labels` varchar(50) NOT NULL,
    `booking_count` INT(5) DEFAULT '0',
    `stripe_plan_id` VARCHAR(30) NULL,
    `status` enum('E','D') NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ct_off_days
CREATE TABLE IF NOT EXISTS `ct_off_days` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL,
    `off_date` date NOT NULL,
    `lastmodify` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
    `status` int(1) DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- ct_order_client_info
CREATE TABLE IF NOT EXISTS `ct_order_client_info` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL,
    `client_name` varchar(200) NOT NULL,
    `client_email` varchar(200) NOT NULL,
    `client_phone` varchar(15) NOT NULL,
    `client_personal_info` text NOT NULL,
    `order_duration` bigint(10) DEFAULT '0',
    `recurring_id` bigint(11) DEFAULT '0',
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ct_payments
CREATE TABLE IF NOT EXISTS `ct_payments` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL,
    `payment_method` varchar(200) NOT NULL,
    `transaction_id` varchar(300) NOT NULL,
    `amount` double NOT NULL,
    `discount` double NOT NULL,
    `taxes` double NOT NULL,
    `partial_amount` double NOT NULL,
    `payment_date` date NOT NULL,
    `net_amount` double NOT NULL,
    `lastmodify` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `frequently_discount` BIGINT(11) DEFAULT '0',
    `frequently_discount_amount` double NOT NULL,
    `recurrence_status` enum('Y', 'N') NOT NULL,
    `tip` double NOT NULL,
    `payment_status` varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ct_schedule_breaks
CREATE TABLE IF NOT EXISTS `ct_schedule_breaks` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `provider_id` int(11) NOT NULL,
    `week_id` int(11) NOT NULL,
    `weekday_id` int(11) NOT NULL,
    `break_start` time NOT NULL,
    `break_end` time NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ct_schedule_offtimes
CREATE TABLE IF NOT EXISTS `ct_schedule_offtimes` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `provider_id` int(11) NOT NULL,
    `start_date_time` datetime NOT NULL,
    `end_date_time` datetime NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ct_services
CREATE TABLE IF NOT EXISTS `ct_services` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(50) NOT NULL,
    `description` text NOT NULL,
    `color` varchar(8) NOT NULL,
    `image` varchar(250) NOT NULL,
    `status` enum('E','D') DEFAULT 'D',
    `position` int(11) DEFAULT '0',
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ct_services_addon
CREATE TABLE IF NOT EXISTS `ct_services_addon` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `service_id` int(11) NOT NULL,
    `addon_service_name` varchar(50) NOT NULL,
    `base_price` double NOT NULL,
    `maxqty` int(11) NOT NULL,
    `image` varchar(250) NOT NULL,
    `multipleqty` enum('Y','N') NOT NULL,
    `status` enum('E','D') DEFAULT 'E',
    `position` int(11) NOT NULL,
    `predefine_image` text NOT NULL,
    `predefine_image_title` text NOT NULL,
    `addon_service_description` varchar(255) DEFAULT NULL,
    `aduration` int(5) DEFAULT '0',
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ct_services_method
CREATE TABLE IF NOT EXISTS `ct_services_method` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `service_id` int(11) NOT NULL,
    `method_title` varchar(250) NOT NULL,
    `status` enum('E','D') DEFAULT 'E',
    `position` int(11) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ct_services_methods_units_rate
CREATE TABLE IF NOT EXISTS `ct_services_methods_units_rate` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `units_id` int(11) NOT NULL,
    `units` varchar(30) NOT NULL,
    `rules` enum('G','E') NOT NULL,
    `rates` float NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ct_service_methods_design
CREATE TABLE IF NOT EXISTS `ct_service_methods_design` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `service_methods_id` int(11) NOT NULL,
    `design` int(11) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ct_service_methods_units
CREATE TABLE IF NOT EXISTS `ct_service_methods_units` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `services_id` int(11) NOT NULL,
    `methods_id` int(11) NOT NULL,
    `units_title` varchar(256) NOT NULL,
    `base_price` double NOT NULL,
    `minlimit` int(11) DEFAULT '1',
    `maxlimit` int(11) NOT NULL,
    `status` enum('E','D') NOT NULL,
    `position` int(11) NOT NULL,
    `limit_title` varchar(256) NOT NULL,
    `unit_symbol` varchar(50) NOT NULL,
    `half_section` enum('E','D') DEFAULT 'D',
    `uduration` int(5) DEFAULT '0',
    `predefine_image` text NOT NULL,
    `predefine_image_title` text NOT NULL,
    `image` varchar(250) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ct_referral_coupon
CREATE TABLE IF NOT EXISTS `ct_referral_coupon` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `client_id` int(11) DEFAULT 0,
      `referral_coupon` text DEFAULT NULL,
      `friend_referral_id` int(11) DEFAULT 0,
      `coupon_limit` int(11) DEFAULT 0,
      `coupon_used` int(11) DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ct_register_otp
CREATE TABLE IF NOT EXISTS `ct_register_otp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` varchar(15) DEFAULT NULL,
  `otp` bigint(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ct_staff_gc
CREATE TABLE IF NOT EXISTS `ct_staff_gc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gc_id` varchar(70) DEFAULT NULL,
  `gc_status` enum('Y','N') DEFAULT 'N',
  `gc_token` text DEFAULT NULL,
  `gc_status_configure` enum('Y','N') DEFAULT 'N',
  `gc_status_sync_configure` enum('Y','N') DEFAULT 'N',
  `gc_client_id` varchar(200) DEFAULT NULL,
  `gc_client_secret` varchar(200) DEFAULT NULL,
  `gc_frontend_url` varchar(200) DEFAULT NULL,
  `gc_admin_url` varchar(200) DEFAULT NULL,
  `staff_id` int(11) DEFAULT 0,
  `gc_api_key` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ct_transfer_request
CREATE TABLE IF NOT EXISTS `ct_transfer_request` (
  `request_id` int(100) NOT NULL AUTO_INCREMENT,
  `staff_id` int(100) DEFAULT 0,
  `email_id` varchar(100) DEFAULT NULL,
  `request_amount` int(100) DEFAULT 0,
  `status` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ct_wallet_history
CREATE TABLE IF NOT EXISTS `ct_wallet_history` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `client_id` int(10) DEFAULT 0,
  `wallet_amount` int(100) DEFAULT 0,
  `wallet_amount_status` enum('C','D') DEFAULT NULL,
  `wallet_trans_id` varchar(100) DEFAULT NULL,
  `wallet_method` varchar(10) DEFAULT '0',
  `lastmodify` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

-- ct_settings
CREATE TABLE IF NOT EXISTS `ct_settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `option_name` varchar(500) NOT NULL,
    `option_value` text NOT NULL,
    `postalcode` mediumtext NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ct_users
CREATE TABLE IF NOT EXISTS `ct_users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_email` varchar(96) DEFAULT NULL,
    `user_pwd` varchar(40) DEFAULT NULL,
    `first_name` varchar(32) DEFAULT NULL,
    `last_name` varchar(32) DEFAULT NULL,
    `phone` varchar(15) DEFAULT NULL,
    `zip` varchar(10) DEFAULT NULL,
    `address` varchar(250) DEFAULT NULL,
    `city` varchar(48) DEFAULT NULL,
    `state` varchar(48) DEFAULT NULL,
    `notes` varchar(800) DEFAULT NULL,
    `vc_status` enum('Y','N','-') DEFAULT 'N',
    `p_status` enum('Y','N','-') DEFAULT 'N',
    `contact_status` varchar(500) DEFAULT NULL,
    `status` enum('E','D') DEFAULT 'D',
    `usertype` varchar(50) DEFAULT NULL,  
    `cus_dt` TIMESTAMP,
    `stripe_id` VARCHAR(30) NULL,
    `referal_code` text DEFAULT NULL,
    `wallet_amount` int(100) DEFAULT 0,
     PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ct_week_days_available
CREATE TABLE IF NOT EXISTS `ct_week_days_available` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `provider_id` int(11) DEFAULT '0',
    `week_id` int(11) NOT NULL,
    `weekday_id` int(11) NOT NULL,
    `day_start_time` time NOT NULL,
    `day_end_time` time NOT NULL,
    `off_day` enum('Y','N') NOT NULL,
    `provider_schedule_type` varchar(30) DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ct_languages
CREATE TABLE IF NOT EXISTS `ct_languages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `label_data` longtext NOT NULL,
    `language` varchar(20) NOT NULL,
    `admin_labels` longtext NOT NULL,
    `error_labels` longtext NOT NULL,
    `extra_labels` longtext NOT NULL,
    `front_error_labels` longtext NOT NULL,
    `language_status` enum('Y','N') DEFAULT 'Y',
    `app_labels` longtext NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ct_email_user
CREATE TABLE IF NOT EXISTS `ct_email_user` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `cus_ids` text NOT NULL,
    `cus_sub` varchar(100) NOT NULL,
    `cus_msg` text NOT NULL,
    `cus_img` text NOT NULL,
    `cus_dt` TIMESTAMP,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ct_sms_user
CREATE TABLE IF NOT EXISTS `ct_sms_user` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `cus_ids` text NOT NULL,
    `cus_msg` text NOT NULL,
    `cus_dt` TIMESTAMP,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ct_staff_commission
CREATE TABLE `ct_staff_commission` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL,
    `staff_id` int(11) NOT NULL,
    `amt_payable` double NOT NULL,
    `advance_paid` double NOT NULL,
    `net_total` double NOT NULL,
    `payment_method` varchar(50) NOT NULL,
    `transaction_id` varchar(100) NOT NULL,
    `payment_date` date NOT NULL,
    PRIMARY KEY (`id`)
    )ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ct_sms_templates
CREATE TABLE IF NOT EXISTS `ct_sms_templates` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `sms_subject` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
    `sms_message` text COLLATE utf8_unicode_ci NOT NULL,
    `default_message` text COLLATE utf8_unicode_ci NOT NULL,
    `sms_template_status` enum('E','D') COLLATE utf8_unicode_ci NOT NULL,
    `sms_template_type` enum('A','C','R','CC','RS','RM','CS','CO') COLLATE utf8_unicode_ci NOT NULL COMMENT 'A=active, C=confirm, R=Reject, CC=Cancel by Client,CS=Cancel by Staff, RS=Reschedule, RM=Reminder, CO=Completed',
    `user_type` enum('A','C','S') COLLATE utf8_unicode_ci NOT NULL COMMENT 'A=Admin,C=client,S=Staff',
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=30 ;

-- ct_email_templates
CREATE TABLE IF NOT EXISTS `ct_email_templates` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email_subject` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
    `email_message` text COLLATE utf8_unicode_ci NOT NULL,
    `default_message` text COLLATE utf8_unicode_ci NOT NULL,
    `email_template_status` enum('E','D') COLLATE utf8_unicode_ci NOT NULL,
    `email_template_type` enum('A','C','R','CC','RS','RM','CO') COLLATE utf8_unicode_ci NOT NULL COMMENT 'A=active, C=confirm, R=Reject, CC=Cancel by Client, RS=Reschedule, RM=Reminder, CO=Completed',
    `user_type` enum('A','C','S') COLLATE utf8_unicode_ci NOT NULL COMMENT 'A=Admin,C=client,S=Staff',
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=30 ;

-- ct_staff_status
CREATE TABLE IF NOT EXISTS `ct_staff_status` (
    `id` int(50) NOT NULL AUTO_INCREMENT,
    `staff_id` int(50) NOT NULL,
    `order_id` int(50) NOT NULL,
    `status` enum('A','D') DEFAULT 'D',
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- ct_rating_review
CREATE TABLE IF NOT EXISTS `ct_rating_review` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `staff_id` int(11) NOT NULL,
    `order_id` bigint(11) NOT NULL,
    `rating` double NOT NULL,
    `review` text NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- ct_recurring_status
CREATE TABLE IF NOT EXISTS `ct_recurring_status` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `recurring_id` int(11) NOT NULL,
    `status` enum('P','A','D') NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- ct_otp
CREATE TABLE IF NOT EXISTS `ct_otp` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(50) NOT NULL,
    `otp` bigint(11) NOT NULL,
    `status` enum('NV','V') COLLATE utf8_unicode_ci NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- ct_special_offer
CREATE TABLE IF NOT EXISTS `ct_special_offer` (`id` int(11) NOT NULL AUTO_INCREMENT,  
    `coupon_value` int(11) NOT NULL,
    `coupon_date` date NOT NULL,
    `coupon_type` enum('P','F') NOT NULL,
    `special_text` text NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
