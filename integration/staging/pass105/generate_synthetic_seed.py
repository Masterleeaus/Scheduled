#!/usr/bin/env python3
"""Generate deterministic synthetic rows for disposable schema review ONLY.
Never creates a booking, fake paid transaction or release evidence.
"""
import argparse,json
from pathlib import Path
ROWS={
 'ct_services':{'id':'1','title':"'QA Residential Clean'",'description':"'Synthetic testing service'",'color':"'#336699'",'image':"''",'status':"'E'",'position':'1'},
 'ct_services_method':{'id':'1','service_id':'1','method_title':"'Hourly'",'status':"'E'",'position':'1'},
 'ct_service_methods_units':{'id':'1','services_id':'1','methods_id':'1','units_title':"'Hours'",'base_price':'50.00','minlimit':'1','maxlimit':'8','status':"'E'",'position':'1','limit_title':"'Hours'",'unit_symbol':"'hr'",'half_section':"'D'",'uduration':'60','predefine_image':"''",'predefine_image_title':"''",'image':"''"},
 'ct_services_methods_units_rate':{'id':'1','units_id':'1','units':"'1'",'rules':"'E'",'rates':'50.00'},
 'ct_admin_info':{'id':'1','password':"'DISABLED-TEST-ACCOUNT'",'email':"'staff@example.invalid'",'fullname':"'QA Staff'",'phone':"'0000000000'",'address':"'QA Address'",'city':"'Melbourne'",'state':"'VIC'",'zip':"'3000'",'country':"'Australia'",'role':"'staff'",'description':"'Synthetic test staff'",'enable_booking':"'Y'",'service_commission':"'F'",'commision_value':'0','schedule_type':"'W'",'image':"''",'service_ids':"'1'"},
}
def generate(schema):
    inventory=json.loads(Path(schema).read_text());available={t['name']:set(t['columns']) for t in inventory['tables']}
    statements=['-- SYNTHETIC NON-PRODUCTION seed candidate; NOT validated against MariaDB.','-- No bookings, payments, credentials or real customer records.','-- Review staff schedule and tenant configuration separately before using in acceptance.']
    for table,row in ROWS.items():
        missing=set(row)-available.get(table,set())
        if missing:raise ValueError(f'{table} columns absent from original installer: {sorted(missing)}')
        statements.append('INSERT INTO `'+table+'` ('+', '.join('`'+c+'`' for c in row)+') VALUES ('+', '.join(row.values())+');')
    return '\n'.join(statements)+'\n'
if __name__=='__main__':
    p=argparse.ArgumentParser();p.add_argument('schema_inventory');p.add_argument('output_sql');a=p.parse_args()
    Path(a.output_sql).write_text(generate(a.schema_inventory));print('Generated '+str(len(ROWS))+' synthetic catalog/staff rows; NOT complete acceptance fixtures')
