'use strict';
const assert=require('assert');
const fs=require('fs');
const vm=require('vm');
const path=require('path');
const script=fs.readFileSync(path.resolve(__dirname,'../../../assets/front/js/titan-booking-widget.js'),'utf8');
const listeners=(obj)=>({ ...obj, handlers:{}, addEventListener(type,fn){this.handlers[type]=fn;} });
const status=()=>({textContent:'', recoveryButton:null, insertAdjacentElement(place,element){this.recoveryButton=element;}});
const button=()=>listeners({disabled:false,textContent:'10:00 AM',dataset:{},setAttribute(){},appendChild(){},remove(){this.removed=true;}});
const form=listeners({elements:{namedItem(k){return {value:k==='timezone'?'Australia/Melbourne':'2026-11-17',focus(){}};}},checkValidity(){return true;},reportValidity(){return true;}});
const submitButton=button();form.querySelector=()=>button();
const checkoutForm=listeners({hidden:true, reportValidity(){return true;},querySelector(){return submitButton;}});
const intentButton=button();
const slotButtons=[];
const list={replaceChildren(){slotButtons.length=0;},querySelectorAll(){return slotButtons;},appendChild(li){slotButtons.push(li.child);}};
const fields={
 '[data-booking-form]':form,'[data-booking-status]':status(),'[data-booking-slots]':list,
 '[data-booking-intent-panel]':{hidden:true},'[data-booking-intent-button]':intentButton,
 '[data-booking-intent-status]':status(),'[data-booking-selected-time]':status(),
 '[data-booking-checkout-form]':checkoutForm,'[data-booking-checkout-status]':status()
};
const root=listeners({dataset:{bookingSubmitEnabled:'true',catalogueReady:'true'},querySelector(k){return fields[k]||null;}});
const calls=[];
let finishCommit;
const fetch=(url,opts)=>{calls.push({url,opts});
 if(url.endsWith('/availability'))return Promise.resolve({slots:['2026-11-17T10:00:00+11:00']});
 if(url.endsWith('/intent'))return Promise.resolve({preview_only:true,booking_writes_enabled:false,confirmed:false,payment_settled:false,intent_token:'abc'});
 if(url.endsWith('/validate'))return Promise.resolve({checkout_valid:true,confirmed:false,payment_settled:false});
 if(url.endsWith('/submit'))return new Promise((resolve,reject)=>{finishCommit={resolve,reject};});
 if(url.endsWith('/status'))return Promise.resolve({resolved:true,confirmed:true,payment_settled:false,status:'confirmed',booking_reference:'42'});
 throw Error('unrecognized fetch');
};
class FormDataMock{constructor(f){this.f=f;}get(k){return ({date:'2026-11-17',duration_minutes:'60',staff_ids:'1',service_id:'1',method_id:'2',method_unit_id:'3',quantity:'1',timezone:'Australia/Melbourne',consent:'on'})[k]||'value';}}
const model={readableBookingMode(enabled){return enabled?'Online booking':'Availability only';},todayInZone(){return '2026-11-11';},dateWithinWindow(){return true;},slots(data){return data.slots;}};
const transport={options(root,payload,signal){return {payload,...(signal?{signal}:{})};},checkedResponse(data){return data;},confirmed(data){if(data.confirmed!==true)throw Error('unverified');return data.booking_reference;}};
const sandbox={window:{TitanBookingPreviewModel:model,TitanBookingTransport:transport},document:{readyState:'loading',addEventListener(){},querySelectorAll(){return [root];},createElement(tag){return tag==='li'?{appendChild(el){this.child=el;}}:button();}},FormData:FormDataMock,AbortController:class{constructor(){this.signal={aborted:false};}abort(){this.signal.aborted=true;}},fetch,Date,Number,Object,Array,String};
vm.createContext(sandbox);
vm.runInContext(script.replace('function boot(){document.querySelectorAll', 'function boot(){document.querySelectorAll').replace(/if\(document\.readyState==='loading'\)document\.addEventListener\('DOMContentLoaded',boot\);else boot\(\);/, 'boot();'),sandbox);
const tick=()=>new Promise(r=>setImmediate(r));
async function main(){
 const event={preventDefault(){}};
 form.handlers.submit(event);await tick();await tick();
 assert.equal(slotButtons.length,1);
 slotButtons[0].handlers.click();intentButton.handlers.click();await tick();await tick();
 assert.equal(checkoutForm.hidden,false);
 checkoutForm.handlers.submit(event);checkoutForm.handlers.submit(event);await tick();await tick();
 assert.equal(calls.filter(c=>c.url.endsWith('/validate')).length,1,'one validation');
 assert.equal(calls.filter(c=>c.url.endsWith('/submit')).length,1,'one commit request');
 assert.equal(calls.find(c=>c.url.endsWith('/submit')).opts.signal,undefined,'commit not abortable via UI');
 form.handlers.change();checkoutForm.handlers.submit(event);
 assert.equal(calls.filter(c=>c.url.endsWith('/submit')).length,1,'no duplicate after form changes');
 finishCommit.reject(new TypeError('Lost response'));
 await tick();await tick();
 assert.match(fields['[data-booking-checkout-status]'].textContent,/uncertain/i);
 assert.match(fields['[data-booking-checkout-status]'].textContent,/Do not submit again/i);
 assert.equal(submitButton.disabled,true,'locked until external resolution');
 checkoutForm.handlers.submit(event);
 assert.equal(calls.filter(c=>c.url.endsWith('/submit')).length,1,'still no duplicate');
 const recovery=fields['[data-booking-checkout-status]'].recoveryButton;
 assert.ok(recovery,'recovery action shown after uncertainty');
 recovery.handlers.click();await tick();await tick();
 assert.equal(calls.filter(c=>c.url.endsWith('/status')).length,1,'read-only recovery called once');
 assert.match(fields['[data-booking-checkout-status]'].textContent,/booking reference is 42/i,'confirmed only from persisted proof');
 assert.equal(calls.filter(c=>c.url.endsWith('/submit')).length,1,'no replay of write during recovery');
 console.log('Pass118/120 uncertain submission and recovery: 14 assertions passed');
}
main().catch(e=>{console.error(e);process.exitCode=1;});
