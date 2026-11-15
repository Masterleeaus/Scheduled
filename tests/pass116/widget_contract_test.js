'use strict';
const fs=require('fs'),assert=require('assert'),path=require('path');
const root=path.resolve(__dirname,'../..');
const widget=fs.readFileSync(path.join(root,'assets/front/js/titan-booking-widget.js'),'utf8');
const blade=fs.readFileSync(path.join(root,'core/resources/views/front/partials/titan-booking-widget.blade.php'),'utf8');
let n=0;
for(const field of ['city','state','zip']){
 assert(widget.includes("'"+field+"'"),'checkout serialization missing '+field);n++;
 assert(new RegExp('name="'+field+'"').test(blade),'input missing '+field);n++;
}
assert(/name="email"[^>]*maxlength="200"/.test(blade),'email must match Cleanto 200 chars');n++;
for(const field of ['city','state','zip']){assert(new RegExp('name="'+field+'"[^>]*required').test(blade),'address field required: '+field);n++;}
assert(widget.includes("fetch('/titan-booking/checkout/submit'"),'submission wiring preserved');n++;
console.log('Pass116 public widget contracts: '+n+' assertions passed');
