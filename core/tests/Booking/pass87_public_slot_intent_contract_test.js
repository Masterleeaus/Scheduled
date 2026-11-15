const assert=require('node:assert/strict');
const fs=require('node:fs');
const path=require('node:path');
const root=path.resolve(__dirname,'../../..');
const js=fs.readFileSync(path.join(root,'assets/front/js/titan-booking-widget.js'),'utf8');
const view=fs.readFileSync(path.join(root,'core/resources/views/front/partials/titan-booking-widget.blade.php'),'utf8');
const cases=[
 ['actual intent endpoint',js.includes("fetch('/titan-booking/intent',opts)")],
 ['validates preview scope',js.includes('data.preview_only!==true')],
 ['requires booking writes disabled',js.includes('data.booking_writes_enabled!==false')],
 ['rejects false confirmation',js.includes('data.confirmed!==false')],
 ['rejects false payment',js.includes('data.payment_settled!==false')],
 ['fetch credentials same origin',js.includes('transport.options(root,params')],
 ['abort on selection changes',js.includes('intentRequest.abort()')],
 ['supports pressed-state selection',js.includes("setAttribute('aria-pressed'")],
 ['does not persist preview token',!js.includes('localStorage')&&!js.includes('sessionStorage')],
 ['never sends personal data',!js.includes('customer_email')&&!js.includes('customer_name')],
 ['has semantic intent status',view.includes('data-booking-intent-status')&&view.includes('aria-live="polite"')],
 ['prevents treating preview as reservation',view.includes('Availability and checkout checks do not themselves reserve a slot.')&&view.includes('Only a server-issued booking reference confirms a reservation.')],
 ['sends selection identifiers',js.includes('unit_id:unit')&&js.includes('service_id:service')],
 ['guards stale slot',js.includes('start.getTime()<=Date.now()')],
];
for(const [label,ok] of cases){assert.ok(ok,label);console.log('PASS '+label)}
console.log(cases.length+' assertions passed');
