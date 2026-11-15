#!/usr/bin/env python3
"""Produce disposable-only Cleanto acceptance inputs, never a release certificate.

Accepts the provenance-controlled installer schema inventory and Pass 105 seed.
Prepares two distinct future appointment windows and proposed weekly staff rows.
The operator must approve the resulting SQL/JSON before any database import.
"""
import argparse
import datetime as dt
import hashlib
import json
from pathlib import Path
from zoneinfo import ZoneInfo

DATABASE = 'titan_booking_test_acceptance'
REQUIRED = {
 'ct_services': {'id','title','status'},
 'ct_services_method': {'id','service_id','status'},
 'ct_service_methods_units': {'id','services_id','methods_id','base_price','uduration'},
 'ct_services_methods_units_rate': {'id','units_id','rates'},
 'ct_admin_info': {'id','service_ids','enable_booking'},
 'ct_week_days_available': {'id','provider_id','weekday_id','week_id','day_start_time','day_end_time','off_day','provider_schedule_type'},
 'ct_bookings': {'id','order_id','client_id','booking_date_time','service_id','staff_ids'},
 'ct_order_client_info': {'id','order_id','client_name','client_email'},
}

def build(inventory, seed, now=None):
    tables = {t['name']:set(t['columns']) for t in inventory['tables']}
    missing={table:sorted(cols-tables.get(table,set())) for table,cols in REQUIRED.items() if cols-tables.get(table,set())}
    if missing: raise ValueError('Unsupported native schema: '+json.dumps(missing,sort_keys=True))
    if inventory.get('table_count')!=len(inventory['tables']) or len(tables)!=len(inventory['tables']):
        raise ValueError('Native table count or uniqueness mismatch')
    if 'ct_week_days_available' in seed or 'ct_bookings' in seed or 'ct_payments' in seed:
        raise ValueError('Seed input already contains scheduling or transaction rows; manual reconciliation needed')
    if seed.count('INSERT INTO')!=5: raise ValueError('Expected exactly five Pass 105 catalogue/staff seed statements')
    timezone=ZoneInfo('Australia/Melbourne')
    now = now or dt.datetime.now(timezone)
    if now.tzinfo is None: raise ValueError('now must be timezone-aware')
    local=now.astimezone(timezone)
    # Use weekdays with stable 10:00 and 14:00 windows, several days in the future.
    day=(local+dt.timedelta(days=7)).date()
    while day.weekday()>4:day+=dt.timedelta(days=1)
    create_a=dt.datetime.combine(day,dt.time(10,0),timezone)
    create_b=dt.datetime.combine(day,dt.time(14,0),timezone)
    # Native Cleanto weekday IDs are 1=Monday through 7=Sunday.
    # Use the next business day for reschedules, including Friday -> Monday.
    reschedule_day=day+dt.timedelta(days=1)
    while reschedule_day.weekday()>4:
        reschedule_day+=dt.timedelta(days=1)
    reschedule_a=dt.datetime.combine(reschedule_day,dt.time(10,0),timezone)
    reschedule_b=dt.datetime.combine(reschedule_day,dt.time(14,0),timezone)
    def fixture(start,move,email):
        return {'database':DATABASE,'environment':'disposable',
          'tenant':{'authenticated_owner_id':1,'verified_pricing_settings':{
             'tax_enabled':False,'discount_enabled':False,'coupon_enabled':False,
             'addons_enabled':False,'travel_fee_enabled':False,'deposit_enabled':False}},
          'create':{'customer':{'name':'Synthetic Booking QA','email':email,'phone':'0000000000'},
              'service':{'service_id':1,'method_id':1,'method_unit_id':1,'quantity':1},
              'staff_ids':[1],'duration_minutes':60,'start_at':start.strftime('%Y-%m-%d %H:%M:%S'),'timezone':'Australia/Melbourne'},
          'reschedule':{'staff_ids':[1],'duration_minutes':60,'start_at':move.strftime('%Y-%m-%d %H:%M:%S'),'timezone':'Australia/Melbourne'}}
    lifecycle=fixture(create_a,reschedule_a,'lifecycle@example.invalid')
    concurrency=fixture(create_b,reschedule_b,'concurrency@example.invalid')
    # Cleanto native class_dayweek_avail.php::get_daynamebyid confirms Monday=1...Sunday=7.
    # insert_schedule_weekly() persists week_id, weekday_id and provider_id; week_id=1
    # is used in weekly mode by set_schedule_type(). Runtime availability still needs DB testing.
    schedule=['-- DISPOSABLE REVIEW ONLY; weekly schedule assumptions require review.',
              '-- Native source confirms weekday_id 1=Monday through 7=Sunday and weekly week_id=1.',
              '-- Verify against Cleanto availability engine before importing.',
              "INSERT INTO `ct_week_days_available` (`provider_id`,`week_id`,`weekday_id`,`day_start_time`,`day_end_time`,`off_day`,`provider_schedule_type`) VALUES"]
    schedule += ["(1,1,%d,'08:00:00','18:00:00','N','W')%s"%(i,',' if i<5 else ';') for i in range(1,6)]
    sql=seed.rstrip()+'\n\n'+'\n'.join(schedule)+'\n'
    assert create_a.timestamp()+3600<create_b.timestamp()
    return lifecycle,concurrency,sql,{'native_tables':len(tables),'seed_rows':5,'schedule_rows':5,
          'table_inventory_sha256':hashlib.sha256(json.dumps(inventory,sort_keys=True).encode()).hexdigest(),
          'synthetic_only':True,'mariadb_import_executed':False,'booking_commit_verified':False,
          'schedule_semantics_approved':False,'schema_upgrade_reconciliation_complete':False,
          'blocking_note':'DO NOT use as release evidence or auto-import without reviewing installer upgrades and schedule conventions.'}

def main():
    p=argparse.ArgumentParser();p.add_argument('inventory');p.add_argument('seed');p.add_argument('output_directory');a=p.parse_args()
    inv=json.loads(Path(a.inventory).read_text());seed=Path(a.seed).read_text()
    lifecycle,concurrency,sql,meta=build(inv,seed)
    out=Path(a.output_directory);out.mkdir(parents=True,exist_ok=True)
    for name,contents in {'lifecycle.json':json.dumps(lifecycle,indent=2)+'\n',
       'concurrency.json':json.dumps(concurrency,indent=2)+'\n',
       'seed-and-schedule.REVIEW.sql':sql,'REVIEW_STATUS.json':json.dumps(meta,indent=2)+'\n'}.items():
        (out/name).write_text(contents)
    print('Generated review-only schema-compatible fixture inputs, not executed booking evidence')
if __name__=='__main__': main()
