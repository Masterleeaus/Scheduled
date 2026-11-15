#!/usr/bin/env python3
"""Adversarial contract cases exercised against the supplied source SQL."""
from pathlib import Path
import tempfile,sys
sys.path.insert(0,str(Path(__file__).resolve().parent))
from validate_transaction_schema import verify,ContractError
SOURCE=Path(__file__).with_name('native.sql')
base=SOURCE.read_text()
checks=0

def valid(sql):
    global checks
    result=verify(sql)
    assert result['transactional_tables_verified']==4
    assert result['release_authorized'] is False
    checks+=1

def invalid(sql):
    global checks
    try: verify(sql)
    except ContractError: checks+=1
    else:raise AssertionError('unsafe schema was accepted')

valid(base)
import re
invalid(re.sub(r'(CREATE TABLE IF NOT EXISTS `ct_bookings`.*?\) ENGINE=)InnoDB',r'\1MyISAM',base,count=1,flags=re.S))
invalid(base.replace('`client_phone` varchar(15) NOT NULL','`client_phone` varchar(255) NOT NULL'))
invalid(base.replace('`client_personal_info` text NOT NULL','`client_personal_info` varchar(20) NOT NULL'))
invalid(base.replace('`booking_date_time` datetime NOT NULL','`booking_date_time` varchar(50) NOT NULL'))
invalid(base.replace('`staff_ids` varchar(160) DEFAULT NULL','`staff_ids` varchar(3) DEFAULT NULL'))
invalid(base.replace('fingerprint CHAR(64)','fingerprint CHAR(32)'))
invalid(base.replace('request_key VARCHAR(96) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY','request_key VARCHAR(96) CHARACTER SET ascii COLLATE ascii_bin NOT NULL'))
invalid(base.replace('CREATE TABLE IF NOT EXISTS titan_booking_requests','CREATE TABLE IF NOT EXISTS titan_booking_requests') + '\nCREATE TABLE IF NOT EXISTS titan_booking_requests (id INT) ENGINE=InnoDB;')
invalid(base.replace('CREATE TABLE IF NOT EXISTS titan_booking_orders','CREATE TABLE IF NOT EXISTS titan_booking_orders') + '\nCREATE TABLE IF NOT EXISTS titan_booking_orders (id INT) ENGINE=InnoDB;')
invalid(base.replace('`ct_bookings`','`ct_missing_bookings`',1))
print('PASS',checks,'schema contract cases')
# Ensure the parser never steals a required column definition from a later table.
invalid(base.replace('`booking_date_time` datetime NOT NULL,', '`booking_date_time` varchar(50) NOT NULL,', 1)
        + '\nCREATE TABLE dummy (`booking_date_time` datetime NOT NULL) ENGINE=InnoDB;')
# No false confidence from composite primary keys.
invalid(base.replace('`staff_ids` varchar(160) DEFAULT NULL,', '`staff_ids` varchar(160) DEFAULT NULL,',1).replace('`booking_date_time` datetime NOT NULL,', '`booking_date_time` datetime NOT NULL,',1).replace('`staff_ids` varchar(160) DEFAULT NULL,\n    `gc_event_id`', '`staff_ids` varchar(160) DEFAULT NULL,\n    `gc_event_id`',1).replace('PRIMARY KEY (`id`)\n    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;\n\n-- ct_booking_addons', 'PRIMARY KEY (`id`, `order_id`)\n    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;\n\n-- ct_booking_addons',1))
# No false confidence from a composite request key.
invalid(base.replace('NOT NULL PRIMARY KEY, fingerprint', 'NOT NULL, PRIMARY KEY (request_key, fingerprint), fingerprint',1))
# A declared column cannot be repeated under the same name.
invalid(base.replace('`client_phone` varchar(15) NOT NULL,',
                     '`client_phone` varchar(15) NOT NULL,\n    `client_phone` varchar(15) NOT NULL,',1))
# The storage engine belongs to this table, not a subsequent CREATE.
invalid(base.replace(') ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;',
                     ') DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;',1))
# Confirm nontransactional sidecar can't be certified.
invalid(base.replace('titan_booking_orders (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB',
                     'titan_booking_orders (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=MyISAM'))
# The state column must retain a bounded non-null contract.
invalid(base.replace('state VARCHAR(16) NOT NULL', 'state TEXT NOT NULL'))
print('PASS',checks,'total including adversarial cases')
