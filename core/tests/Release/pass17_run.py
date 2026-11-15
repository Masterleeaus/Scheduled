#!/usr/bin/env python3
"""Repeatable offline source-level release gate. Does NOT certify browser, MariaDB, or providers."""
import json, subprocess, sys, shutil, os, datetime
from pathlib import Path
ROOT=Path(__file__).resolve().parents[3]  # application archive root
CORE=ROOT/'core'
OUT=Path(sys.argv[1]).resolve() if len(sys.argv)>1 else ROOT/'PASS17_RELEASE_GATE.json'
TESTS=[
 ('templates_and_assets','php','core/tests/Release/pass17_template_integrity.php'),
 ('booking_fail_closed_contract','php','core/tests/Release/pass17_release_security.php'),
 ('booking_readiness','php','core/tests/Booking/pass10_readiness_contract_test.php'),
 ('booking_schema','php','core/tests/Booking/pass10_schema_snapshot_test.php'),
 ('booking_response','php','core/tests/Booking/pass100_response_contract_test.php'),
 ('booking_validation','php','core/tests/Booking/pass88_checkout_validation_test.php'),
 ('native_customer_handoff','php','core/tests/Booking/pass92_checkout_native_handoff_test.php'),
 ('native_checkout_compatibility','php','core/tests/Booking/pass89_checkout_commit_compatibility_test.php'),
 ('native_checkout_executor','php','core/tests/Booking/pass94_native_executor_test.php'),
 ('booking_source_handoff','php','core/tests/Booking/pass92_source_connection_test.php'),
 ('booking_submission_gate','php','core/tests/Booking/pass93_submission_gate_test.php'),
 ('booking_tenant_readiness','php','core/tests/Booking/pass96_tenant_checkout_readiness_test.php'),
 ('booking_replay','php','core/tests/Booking/pass98_submission_replay_test.php'),
 ('booking_phone_boundary','php','core/tests/Booking/pass99_phone_native_boundary_test.php'),
 ('payment_reconciliation','php','core/tests/Booking/pass12_reconciliation_decision_test.php'),
 ('stripe_inbox','php','core/tests/Booking/pass12_stripe_payment_inbox_test.php'),
 ('booking_widget','node','core/tests/Booking/pass11_public_widget_test.js'),
 ('booking_transport','node','core/tests/Booking/pass88_checkout_contract_test.js'),
 ('booking_widget_uncertain_outcome','node','core/tests/Booking/pass118_checkout_uncertain_outcome_test.js'),
 ('booking_recovery_widget','node','core/tests/Booking/pass120_recovery_widget_test.js','assets/front/js/titan-booking-widget.js'),
 ('legacy_booking_intent_contract','node','core/tests/Booking/pass87_public_slot_intent_contract_test.js'),
 ('legacy_booking_transport_contract','node','core/tests/Booking/pass95_public_checkout_transport_test.js'),
]
result={'scope':'OFFLINE_SOURCE_REGRESSION_ONLY','timestamp_utc':datetime.datetime.now(datetime.timezone.utc).isoformat(),'environment':{'php':shutil.which('php'),'node':shutil.which('node'),'pdo_mysql':False,'mariadb':False},'tests':[],'missing_acceptance':['Laravel/PHPUnit with DOM/XML extensions','real MariaDB booking transactions and migrations','authenticated browser editing of 25 templates','live AI image and copy provider','Stripe official test-mode checkout and webhook settlement','cross-tenant live database security']}
if shutil.which('php'):
  p=subprocess.run(['php','-r','echo extension_loaded("pdo_mysql")?"yes":"no";'],capture_output=True,text=True)
  result['environment']['pdo_mysql']=p.stdout=='yes'
result['environment']['mariadb']=bool(shutil.which('mariadb') or shutil.which('mysql'))
for name,tool,script,*args in TESTS:
 if not shutil.which(tool):
  result['tests'].append({'name':name,'status':'SKIP','reason':tool+' missing'});continue
 try:
  proc=subprocess.run([tool,script,*args],cwd=ROOT,capture_output=True,text=True,timeout=20)
  result['tests'].append({'name':name,'status':'PASS' if proc.returncode==0 else 'FAIL','exit_code':proc.returncode,'output':(proc.stdout+'\n'+proc.stderr).strip()[-1200:]})
 except subprocess.TimeoutExpired:result['tests'].append({'name':name,'status':'FAIL','reason':'timed out (20s)'})
result['summary']={status:sum(1 for t in result['tests'] if t['status']==status) for status in ['PASS','FAIL','SKIP']}
result['release_accepted']=False # never infer live acceptance from offline tests
OUT.parent.mkdir(parents=True,exist_ok=True)
OUT.write_text(json.dumps(result,indent=2)+'\n')
print(json.dumps({'summary':result['summary'],'failed':[t['name'] for t in result['tests'] if t['status']=='FAIL'],'report':str(OUT),'release_accepted':False},indent=2))
sys.exit(0 if result['summary']['FAIL']==0 else 2)
