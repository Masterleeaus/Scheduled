(function () {
 'use strict';
 function init(root) {
  if(root.dataset.bookingInitialized==='true')return;
  root.dataset.bookingInitialized='true';
  var form=root.querySelector('[data-booking-form]'),status=root.querySelector('[data-booking-status]'),list=root.querySelector('[data-booking-slots]');
  var model=window.TitanBookingPreviewModel,transport=window.TitanBookingTransport;
  if(!form||!status||!list||!model||!transport)return;
  var button=form.querySelector('[type="submit"]');
  var commitEnabled=root.dataset.bookingSubmitEnabled==='true';
  var readinessLabel=root.querySelector('[data-booking-readiness-label]');
  var readinessMessage=root.querySelector('[data-booking-readiness-message]');
  var resetButton=root.querySelector('[data-booking-reset]');
  if(readinessLabel)readinessLabel.textContent=model.readableBookingMode(commitEnabled);
  if(readinessMessage)readinessMessage.textContent=commitEnabled
    ?'Select a time and complete your booking details. A reservation is confirmed only when a booking reference is issued. Payments are not taken in this widget.'
    :'Availability can be checked, but online reservation submission is currently unavailable. Contact the business to arrange an appointment.';
  var date=form.elements.namedItem('date');
  var intentPanel=root.querySelector('[data-booking-intent-panel]');
  var intentButton=root.querySelector('[data-booking-intent-button]');
  var intentStatus=root.querySelector('[data-booking-intent-status]');
  var selectedTime=root.querySelector('[data-booking-selected-time]');
  var checkoutForm=root.querySelector('[data-booking-checkout-form]'),checkoutStatus=root.querySelector('[data-booking-checkout-status]');
  var selectedSlot=null,slotPayload=null,intentRequest=null,checkoutRequest=null,intentSeq=0,validatedToken=null;
  // Once a reservation request has been sent its outcome is uncertain until a
  // valid server confirmation arrives. Never abort/replay it from this widget.
  var submissionLocked=false,checkoutValidationPending=false;
  // Status recovery is read-only. Retain only in-memory input for a single
  // uncertainty episode; never persist customer details or automatically replay a write.
  var recoveryPayload=null,recoveryPending=false,recoveryButton=null,recoveryRequest=null;
  function clearRecovery(){
   recoveryPayload=null;recoveryPending=false;
   if(recoveryRequest){recoveryRequest.abort();recoveryRequest=null;}
   if(recoveryButton){recoveryButton.remove();recoveryButton=null;}
  }
  function showRecovery(payload){
   recoveryPayload=payload;
   if(!checkoutStatus||recoveryButton)return;
   recoveryButton=document.createElement('button');
   recoveryButton.type='button';
   recoveryButton.textContent='Check reservation status';
   recoveryButton.setAttribute('aria-label','Check whether your reservation was received');
   checkoutStatus.insertAdjacentElement('afterend',recoveryButton);
   recoveryButton.addEventListener('click',function(){
    if(!recoveryPayload||recoveryPending)return;
    recoveryPending=true;recoveryButton.disabled=true;
    checkoutStatus.textContent='Checking the reservation record…';
    recoveryRequest=typeof AbortController==='function'?new AbortController():null;
    var active=recoveryRequest;
    var options;
    try{options=transport.options(root,recoveryPayload,active&&active.signal);}
    catch(e){checkoutStatus.textContent='Status check unavailable. Contact the business with your booking details.';recoveryPending=false;recoveryButton.disabled=false;return;}
    fetch('/titan-booking/checkout/status',options).then(transport.checkedResponse).then(function(result){
     if(result&&result.resolved===true&&result.confirmed===true&&result.payment_settled===false&&result.status==='confirmed'){
      var reference=String(result.booking_reference||'');
      if(!/^[1-9][0-9]*$/.test(reference))throw new Error('Invalid persisted booking reference');
      checkoutStatus.textContent='Reservation confirmed. Your booking reference is '+reference+'.';
      validatedToken=null;clearRecovery();
     }else if(result&&result.resolved===true&&result.confirmed===false&&result.payment_settled===false&&
       ['completed','rescheduled','cancelled','rejected','no_show'].indexOf(result.status)!==-1){
      var historicalReference=String(result.booking_reference||'');
      if(!/^[1-9][0-9]*$/.test(historicalReference))throw new Error('Invalid persisted booking reference');
      checkoutStatus.textContent='Reservation '+result.status.replace('_',' ')+'. Booking reference '+historicalReference+'. Contact the business for details.';
      validatedToken=null;clearRecovery();
     }else if(result&&result.resolved===false&&result.confirmed===false&&result.payment_settled===false&&result.status==='unknown'){
      checkoutStatus.textContent='The reservation has not been verified yet. It may still have been received. Do not submit again; contact the business to confirm.';
     }else{throw new Error('Unexpected recovery evidence');}
    }).catch(function(){
     checkoutStatus.textContent='Reservation status is unavailable. Your booking may have been received. Do not submit again; contact the business to confirm.';
    }).finally(function(){recoveryPending=false;recoveryRequest=null;if(recoveryButton)recoveryButton.disabled=false;});
   });
  }
  if(resetButton)resetButton.addEventListener('click',function(){
    if(submissionLocked){status.textContent='A reservation request was already sent. Check its status before starting again.';return;}
    clearRecovery();form.reset();cancel();if(checkoutForm)checkoutForm.reset();
    status.textContent='Choose a service, '+String(root.dataset.staffNoun||'professional')+' and date to check new availability.';
  });
  function clearIntent(){if(submissionLocked)return;intentSeq++;selectedSlot=null;slotPayload=null;validatedToken=null;if(checkoutForm){checkoutForm.hidden=true;var checkoutButton=checkoutForm.querySelector('[type="submit"]');if(checkoutButton)checkoutButton.disabled=false;}if(checkoutStatus)checkoutStatus.textContent='';if(intentRequest){intentRequest.abort();intentRequest=null;}if(checkoutRequest){checkoutRequest.abort();checkoutRequest=null;}if(intentPanel)intentPanel.hidden=true;if(intentButton)intentButton.disabled=true;if(intentStatus)intentStatus.textContent='Choose an available time to continue.';}
  function selectSlot(iso,payload,buttonElement){
   clearIntent();selectedSlot=iso;slotPayload=Object.assign({},payload);
   Array.prototype.forEach.call(list.querySelectorAll('button[data-booking-slot]'),function(b){b.setAttribute('aria-pressed',String(b===buttonElement));});
   if(intentPanel)intentPanel.hidden=false;
   if(selectedTime)selectedTime.textContent=buttonElement.textContent+' — not yet reserved';
   if(intentButton)intentButton.disabled=false;
   if(intentStatus)intentStatus.textContent='Recheck this time before entering your booking details.';
  }
  if(intentButton)intentButton.addEventListener('click',function(){
   if(!selectedSlot||!slotPayload||intentRequest)return;
   var start=new Date(selectedSlot);if(!Number.isFinite(start.getTime())||start.getTime()<=Date.now()){clearIntent();status.textContent='The selected time is no longer in the future.';return;}
   var seq=++intentSeq,params=Object.assign({},slotPayload,{date_time:slotPayload.localDateTime});delete params.localDateTime;
   intentRequest=typeof AbortController==='function'?new AbortController():null;
   var active=intentRequest,opts;try{opts=transport.options(root,params,active&&active.signal);}catch(e){intentRequest=null;intentStatus.textContent='Booking session expired. Reload the page and try again.';return;}
   intentButton.disabled=true;intentStatus.textContent='Rechecking your selected time…';
   fetch('/titan-booking/intent',opts).then(transport.checkedResponse)
   .then(function(data){if(seq!==intentSeq)return;
    if(data.preview_only!==true||data.booking_writes_enabled!==false||data.confirmed!==false||data.payment_settled!==false||typeof data.intent_token!=='string'||!data.intent_token){throw new Error('Unsafe booking response');}
    validatedToken=data.intent_token;if(checkoutForm)checkoutForm.hidden=false;
    intentStatus.textContent='Time validated. Enter your contact details to continue.';
   }).catch(function(e){if(seq===intentSeq&&(!e||e.name!=='AbortError'))intentStatus.textContent='This time could not be validated. Refresh availability or contact the business.';})
   .finally(function(){if(seq===intentSeq){intentRequest=null;intentButton.disabled=false;}});
  });
  if(checkoutForm)checkoutForm.addEventListener('submit',function(event){
   event.preventDefault();
   if(submissionLocked||checkoutValidationPending)return;
   if(!validatedToken||!selectedSlot){checkoutStatus.textContent='Select and validate a time again.';return;}
   if(!checkoutForm.reportValidity())return;
   var values=new FormData(checkoutForm),customer={};
   ['first_name','last_name','email','phone','address','city','state','zip','notes'].forEach(function(key){customer[key]=String(values.get(key)||'');});
   customer.consent=values.get('consent')==='on';
   var seq=intentSeq,submitButton=checkoutForm.querySelector('[type="submit"]'),payload={intent_token:validatedToken,customer:customer};
   checkoutRequest=typeof AbortController==='function'?new AbortController():null;
   var active=checkoutRequest,opts;
   try{opts=transport.options(root,payload,active&&active.signal);}catch(e){checkoutStatus.textContent='Booking session expired. Reload the page and try again.';checkoutRequest=null;return;}
   checkoutValidationPending=true;
   submitButton.disabled=true;
   checkoutStatus.textContent='Checking your booking details…';
   fetch('/titan-booking/checkout/validate',opts).then(transport.checkedResponse)
   .then(function(data){
    if(seq!==intentSeq)return null;
    if(data.checkout_valid!==true||data.confirmed!==false||data.payment_settled!==false)throw new Error('Invalid checkout validation');
    if(!commitEnabled){
     checkoutStatus.textContent='Details checked. Please contact the business to finalize your reservation.';
     return null;
    }
    if(seq!==intentSeq) return null;
    // Do not pass an AbortSignal to the commit request. Once transmitted,
    // aborting the browser fetch cannot undo a committed database transaction.
    submissionLocked=true;
    checkoutStatus.textContent='Submitting your reservation…';
    return fetch('/titan-booking/checkout/submit',transport.options(root,payload))
      .then(transport.checkedResponse).then(function(result){
       if(seq!==intentSeq)return;
       var reference=transport.confirmed(result);
       validatedToken=null;clearRecovery();
       checkoutStatus.textContent='Reservation confirmed. Your booking reference is '+reference+'.';
       submitButton.disabled=true;
      });
   })
   .catch(function(e){if(seq===intentSeq&&(!e||e.name!=='AbortError')){
     if(submissionLocked){
      checkoutStatus.textContent='The outcome of your reservation is uncertain. Check its status or contact the business; do not submit again.';
      showRecovery(payload);
     }else{
      checkoutStatus.textContent='Your details could not be validated. Review them and try again.';
     }
   }})
   .finally(function(){checkoutValidationPending=false;if(seq===intentSeq){checkoutRequest=null;if(validatedToken&&!submissionLocked)submitButton.disabled=false;}});
  });
  var request=null,sequence=0;
  function clear(){clearIntent();list.replaceChildren();root.dataset.previewHasSlots='false';}
  function cancel(){if(submissionLocked)return;sequence++;if(request){request.abort();request=null;}if(button)button.disabled=false;clear();}
  root.addEventListener('titan-booking-input-changed',function(){cancel();status.textContent='Selection changed. Check availability again to refresh the times.';});
  form.addEventListener('change',function(){cancel();status.textContent='Check availability to see available times. Your selection is not yet reserved.';});
  root.addEventListener('titan-booking-destroy',cancel,{once:true});
  form.addEventListener('submit',function(event){
    event.preventDefault();cancel();
    if(root.dataset.catalogueReady!=='true'){status.textContent='Services are not ready. Please try again later.';return;}
    if(!form.checkValidity()){form.reportValidity();return;}
    var local;try{local=model.todayInZone(String(form.elements.namedItem('timezone').value));}catch(e){status.textContent='Business timezone unavailable. Contact the business.';return;}
    if(!model.dateWithinWindow(date.value,local,180)){status.textContent='Choose a valid date within the next 180 days.';date.focus();return;}
    var values=new FormData(form);
    var duration=Number(values.get('duration_minutes')),staff=Number(values.get('staff_ids'));
    if(!Number.isSafeInteger(duration)||duration<15||duration>720||duration%15!==0||!Number.isSafeInteger(staff)||staff<1){status.textContent='Please select a valid professional and duration.';return;}
    var payload={staff_id:staff,duration_minutes:duration,date:date.value,timezone:String(values.get('timezone')||'Australia/Melbourne')};
    var current=++sequence;
    request=typeof AbortController==='function'?new AbortController():null;
    var opts;try{opts=transport.options(root,payload,request&&request.signal);}catch(e){status.textContent='Booking session expired. Reload the page and try again.';if(button)button.disabled=false;return;}
    status.textContent='Checking available times. No reservation has been created.';if(button)button.disabled=true;
    fetch('/titan-booking/availability',opts).then(transport.checkedResponse)
    .then(function(data){
      if(current!==sequence)return;
      var available=model.slots(data);clear();
      if(!available.length){status.textContent='No available times were returned for this '+String(root.dataset.staffNoun||'professional')+' and date. Try another selection.';return;}
      available.slice(0,24).forEach(function(value){
       var item=document.createElement('li'),time=document.createElement('time'),parsed=new Date(value),choose=document.createElement('button');
       time.dateTime=value;
       time.textContent=parsed.toLocaleTimeString(undefined,{hour:'numeric',minute:'2-digit',timeZone:payload.timezone});
       choose.type='button';choose.dataset.bookingSlot='true';choose.setAttribute('aria-pressed','false');choose.setAttribute('aria-label','Select '+time.textContent+' to continue booking');choose.appendChild(time);item.appendChild(choose);list.appendChild(item);
       choose.addEventListener('click',function(){
         var values=new FormData(form),localDateTime=parsed.toLocaleString('sv-SE',{timeZone:payload.timezone,hour12:false}).replace(' ','T');
         var service=Number(values.get('service_id')),method=Number(values.get('method_id')),unit=Number(values.get('method_unit_id')),quantity=Number(values.get('quantity'));
         if([service,method,unit,quantity].some(function(n){return !Number.isSafeInteger(n)||n<1;})){status.textContent='Please reselect a valid service, pricing method and quantity.';return;}
         selectSlot(value,{service_id:service,method_id:method,unit_id:unit,staff_id:staff,quantity:quantity,duration_minutes:duration,timezone:payload.timezone,localDateTime:localDateTime},choose);
       });
      });
      root.dataset.previewHasSlots='true';
      status.textContent=available.length+' available time'+(available.length===1?'':'s')+' found. Choose a time to continue. No appointment has been created.';
    }).catch(function(error){if(current===sequence&&(!error||error.name!=='AbortError')){clear();status.textContent='Availability could not be verified. Please contact the business.';}})
    .finally(function(){if(current===sequence){if(button)button.disabled=false;request=null;}});
  });
 }
 function boot(){document.querySelectorAll('[data-titan-booking-widget]').forEach(init);}
 if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',boot);else boot();
})();
