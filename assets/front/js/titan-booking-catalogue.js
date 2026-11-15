(function () {
 'use strict';
 function renderOptions(select, rows, prompt) {
   select.replaceChildren();
   var placeholder=document.createElement('option');placeholder.value='';placeholder.textContent=prompt;select.appendChild(placeholder);
   rows.forEach(function (row) { var option=document.createElement('option');option.value=String(row.id);option.textContent=row.label;select.appendChild(option); });
   select.disabled=rows.length===0;
   select.value='';
 }
 function init(root) {
   if(root.dataset.catalogueInitialized==='true')return;
   root.dataset.catalogueInitialized='true';
   var endpoint=root.getAttribute('data-catalogue-endpoint');
   var form=root.querySelector('[data-booking-form]');
   var status=root.querySelector('[data-booking-status]');
   var model=window.TitanBookingPreviewModel;
   if(!form || !model || !endpoint || !/^\/(?!\/)[a-zA-Z0-9_/?=&.%-]+$/.test(endpoint))return;
   var service=form.elements.namedItem('service_id'), method=form.elements.namedItem('method_id'), unit=form.elements.namedItem('method_unit_id'), staff=form.elements.namedItem('staff_ids');
   if(!service||!method||!unit||!staff)return;
   [service,method,unit,staff].forEach(function(s){s.disabled=true;});
   var controller=typeof AbortController==='function'?new AbortController():null;
   var options={credentials:'same-origin',headers:{'Accept':'application/json'},cache:'no-store'};
   if(controller)options.signal=controller.signal;
   root.addEventListener('titan-booking-destroy',function(){if(controller)controller.abort();},{once:true});
   fetch(endpoint,options).then(function(response){if(!response.ok)throw new Error('Catalogue unavailable');return response.json();})
   .then(function(response){
     var data=model.catalogue(response);
     var mapping=root.getAttribute('data-mapped-service-ids')||'';
     // Mapping only filters the public UI; Cleanto remains authoritative for permissions, prices and availability.
     if(mapping){
       var allowed=Object.create(null);
       mapping.split(',').forEach(function(id){if(/^[1-9][0-9]*$/.test(id))allowed[id]=true;});
       data.services=data.services.filter(function(row){return allowed[String(row.id)]===true;});
     }
     renderOptions(service,data.services,root.getAttribute('data-service-prompt') || 'Select service');
     renderOptions(staff,data.staff,root.getAttribute('data-staff-prompt') || 'Select a cleaner');
     renderOptions(method,[],'Choose service first');
     renderOptions(unit,[],'Choose method first');
     service.addEventListener('change',function(){
       renderOptions(method,data.methods.filter(function(row){return String(row.service_id)===service.value;}),'Select pricing method');
       renderOptions(unit,[],'Choose method first');
       root.dispatchEvent(new Event('titan-booking-input-changed'));
     });
     method.addEventListener('change',function(){
       renderOptions(unit,data.units.filter(function(row){return String(row.method_id)===method.value;}),'Select service size');
       root.dispatchEvent(new Event('titan-booking-input-changed'));
     });
     [unit,staff].forEach(function(field){field.addEventListener('change',function(){root.dispatchEvent(new Event('titan-booking-input-changed'));});});
     root.dataset.catalogueReady='true';
     if(status)status.textContent='Select your service and date to check available times. No booking will be made.';
   }).catch(function(error){
     if(error.name==='AbortError')return;
     root.dataset.catalogueReady='false';
     if(status)status.textContent='We cannot load services at the moment. Please contact the business.';
   });
 }
 function boot(){document.querySelectorAll('[data-titan-booking-widget]').forEach(init);}
 if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',boot);else boot();
})();
