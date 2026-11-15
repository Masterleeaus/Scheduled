/* Pass 120 source-to-widget recovery contract: executable regression check. */
'use strict';
const fs=require('fs'), assert=require('assert');
const s=fs.readFileSync(process.argv[2],'utf8');
let n=0;
function ok(v,label){assert.ok(v,label);n++;}
ok(s.includes("fetch('/titan-booking/checkout/status',options)"),'read-only status endpoint');
ok(s.includes('showRecovery(payload);'),'uncertain outcome offers recovery');
ok(s.includes('if(submissionLocked||checkoutValidationPending)return;'),'duplicate submission blocked');
ok(s.includes("result.resolved===true&&result.confirmed===true&&result.payment_settled===false&&result.status==='confirmed'"),'strict recovery success');
ok(s.includes("result.resolved===false&&result.confirmed===false&&result.payment_settled===false&&result.status==='unknown'"),'unknown is distinct');
ok(s.includes("/^[1-9][0-9]*$/.test(reference)"),'reference validated');
ok(s.includes('clearRecovery();'),'successful recovery removes action');
ok(s.includes('if(!recoveryPayload||recoveryPending)return;'),'duplicate status request blocked');
ok(s.includes('recoveryButton.disabled=true'),'status button disabled during request');
ok(s.includes('Do not submit again')||s.includes('do not submit again'),'no double-booking guidance');
ok(!s.includes('localStorage')&&!s.includes('sessionStorage'),'customer data never stored in browser storage');
ok(s.includes("var commitEnabled=root.dataset.bookingSubmitEnabled==='true';")&&s.includes('if(!commitEnabled){'),'deployment gate preserved');
console.log('Pass 120 recovery widget contract assertions: '+n+' passed');
