'use strict';
const assert=require('assert');
const fs=require('fs');
const path=require('path');
const root=path.resolve(__dirname,'../../..');
const transport=require(path.join(root,'assets/front/js/titan-booking-transport.js'));
let checks=0;
function check(ok,message){assert(ok,message);checks++;}
const scope={querySelector(sel){return sel==='input[name="_token"]'?{value:'a'.repeat(40)}:null;}};
let req=transport.options(scope,{intent_token:'example',customer:{consent:true}});
check(req.method==='POST','POST');
check(req.credentials==='same-origin','same origin');
check(req.cache==='no-store','not cached');
check(req.headers['X-CSRF-TOKEN']==='a'.repeat(40),'CSRF token bound');
check(JSON.parse(req.body).customer.consent===true,'JSON body');
const signal={aborted:false};
check(transport.options(scope,{},signal).signal===signal,'abort signal forwarded');
for(const bad of ['', 'unsafe\nheader', 'short']){
 check((()=>{try{transport.options({querySelector:()=>({value:bad})},{});return false;}catch(e){return true;}})(),'bad CSRF rejected');
}
check(transport.confirmed({confirmed:true,payment_settled:false,booking_reference:'12345'})==='12345','persisted booking result accepted');
for(const value of [
 {confirmed:false,payment_settled:false,booking_reference:'123'},
 {confirmed:true,payment_settled:true,booking_reference:'123'},
 {confirmed:true,payment_settled:false,booking_reference:'abc'},
 {confirmed:true,payment_settled:false,booking_reference:''},
 {confirmed:true,payment_settled:false,booking_reference:123},
]){check((()=>{try{transport.confirmed(value);return false;}catch(e){return true;}})(),'false booking proof rejected');}
const widget=fs.readFileSync(path.join(root,'assets/front/js/titan-booking-widget.js'),'utf8');
const assets=fs.readFileSync(path.join(root,'core/resources/views/front/partials/titan-booking-assets.blade.php'),'utf8');
const view=fs.readFileSync(path.join(root,'core/resources/views/front/partials/titan-booking-widget.blade.php'),'utf8');
check(widget.includes("fetch('/titan-booking/checkout/submit'"),'submit route used');
check(widget.includes("var commitEnabled=root.dataset.bookingSubmitEnabled==='true';")&&widget.includes('if(!commitEnabled){'),'submit gated');
check(widget.includes('transport.confirmed(result)'),'confirmation checked');
check(widget.includes('checkoutRequest.abort()'),'stale checkout canceled');
check(assets.indexOf('titan-booking-transport.js')<assets.indexOf('titan-booking-widget.js'),'transport loaded before widget');
check(view.includes('@csrf'),'csrf field rendered');
check(view.includes('app()->bound('),'feature enabled only with registered executor');
console.log('Pass95 booking browser transport: '+checks+' assertions passed');
