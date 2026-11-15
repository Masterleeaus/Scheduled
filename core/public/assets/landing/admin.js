(function(){'use strict';var select=document.getElementById('landing-template');if(!select)return;var defaults=window.landingTemplateDefaults||{};select.addEventListener('change',function(){var t=defaults[select.value];if(!t)return;[['landing-headline','headline'],['landing-subheading','subheading'],['landing-cta','cta']].forEach(function(x){var node=document.getElementById(x[0]);if(node)node.value=t[x[1]]||'';});});})();

/* Pass 16: additive campaign image blocks, saved by existing CampaignInput pipeline. */
(function(){'use strict';
var root=document.getElementById('landing-image-blocks'),add=document.getElementById('landing-add-image'),status=document.getElementById('landing-image-message');if(!root||!add)return;
var counter=root.querySelectorAll('.landing-image-block').length;
function say(message){if(status)status.textContent=message;}
function escapeHtml(s){return String(s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
function field(i,name){return 'image_blocks['+i+']['+name+']';}
add.addEventListener('click',function(){if(root.querySelectorAll('.landing-image-block').length>=12){say('Maximum twelve additional images.');return;}var i=counter++;
var div=document.createElement('div');div.className='landing-image-block';div.style.cssText='padding:12px;border:1px solid #d5dee8;margin:12px 0;border-radius:8px';
div.innerHTML='<label>Image URL<input class="form-control landing-block-url" name="'+field(i,'url')+'" maxlength="500" required></label>'+
(window.landingUploadUrl?'<label>Upload image<input type="file" class="landing-block-upload" accept="image/jpeg,image/png,image/webp,image/gif"></label>':'')+
'<label>Alternative text<input class="form-control" name="'+field(i,'alt')+'" maxlength="180"></label>'+
'<label>Caption<input class="form-control" name="'+field(i,'caption')+'" maxlength="240"></label>'+
'<label>Placement<select class="form-control" name="'+field(i,'placement')+'"><option value="after-hero">After hero</option><option value="before-trust">Before trust section</option><option value="after-trust">After trust section</option><option value="before-form">Before enquiry form</option></select></label>'+
'<label>Width<select class="form-control" name="'+field(i,'width')+'"><option value="normal">Normal</option><option value="wide">Wide</option><option value="full">Full width</option></select></label>'+
'<button type="button" class="btn btn-outline-danger landing-remove-block">Remove image</button>';root.appendChild(div);say('Image block added. Upload or paste an image URL, then save.');});
root.addEventListener('click',function(event){if(event.target.matches('.landing-remove-block')){event.target.closest('.landing-image-block').remove();say('Image removed from draft form. Save to apply.');}});
function upload(input,target){if(!window.landingUploadUrl){say('Save the campaign first to upload images.');return;}var file=input.files&&input.files[0];if(!file)return;if(file.size>5*1024*1024){say('Image exceeds 5 MB.');input.value='';return;}var data=new FormData();data.append('image',file);input.disabled=true;say('Uploading image...');fetch(window.landingUploadUrl,{method:'POST',body:data,headers:{'X-CSRF-TOKEN':window.landingCsrfToken,'Accept':'application/json'},credentials:'same-origin'}).then(function(r){if(!r.ok)throw new Error('Image upload rejected ('+r.status+').');return r.json();}).then(function(result){if(!result.url)throw new Error('Missing image URL.');target.value=result.url;target.dispatchEvent(new Event('change',{bubbles:true}));say('Image uploaded. Save draft to apply.');}).catch(function(error){say(error.message);}).finally(function(){input.disabled=false;input.value='';});}
document.addEventListener('change',function(event){var input=event.target;if(input.matches('[data-landing-upload-target]')){var t=document.getElementById(input.dataset.landingUploadTarget);if(t)upload(input,t);}else if(input.matches('.landing-block-upload')){var t=input.closest('.landing-image-block').querySelector('.landing-block-url');if(t)upload(input,t);}});
})();
