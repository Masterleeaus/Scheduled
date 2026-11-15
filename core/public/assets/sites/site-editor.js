(function(){'use strict';const root=document.querySelector('[data-site-editor]');if(!root)return;const raw=root.querySelector('[name="document"]');const controls=root.querySelector('[data-site-fields]');let state;try{state=JSON.parse(raw.value)}catch(e){return}
const themeFields=(()=>{try{return JSON.parse(root.dataset.themeFields||'{}')}catch(e){return {}}})();
const mediaUrl=root.dataset.mediaUrl;const pickerUrl=root.dataset.placementUrl;const csrf=root.querySelector('input[name="_token"]')?.value;
const history=[];const future=[];let applying=false;
function snapshot(){if(applying)return;history.push(JSON.stringify(state));if(history.length>50)history.shift();future.length=0}
function restore(value){applying=true;state=JSON.parse(value);sync();controls.replaceChildren();renderAll();applying=false}
function toolbar(){const target=root.querySelector('[data-site-toolbar]');if(!target)return;for(const [name,fn] of [['Undo',()=>{if(!history.length)return;future.push(JSON.stringify(state));restore(history.pop())}],['Redo',()=>{if(!future.length)return;history.push(JSON.stringify(state));restore(future.pop())}]]){const b=document.createElement('button');b.type='button';b.textContent=name;b.addEventListener('click',fn);target.append(b)}}
const plain=['headline','tagline','about','cta_label','cta_url','contact_email','phone','seo_title','seo_description','seo_image'];const labels={cta_label:'Button label',cta_url:'Button destination',seo_title:'Search title',seo_description:'Search description',contact_email:'Contact email'};
function sync(){raw.value=JSON.stringify(state,null,2);raw.dispatchEvent(new Event('change',{bubbles:true}))}
function field(label,value,onchange,large){const wrap=document.createElement('label');wrap.className='site-editor-field';wrap.append(document.createTextNode(label));const el=document.createElement(large?'textarea':'input');if(large)el.rows=3;else el.type='text';el.value=value||'';el.addEventListener('focus',snapshot);el.addEventListener('input',()=>{onchange(el.value);sync()});wrap.append(el);return wrap}

function uploadButton(done,label){
 const wrap=document.createElement('div');const file=document.createElement('input');file.type='file';file.accept='image/png,image/jpeg,image/webp,image/gif';file.setAttribute('aria-label',label);
 const status=document.createElement('span');status.setAttribute('role','status');
 file.addEventListener('change',async()=>{const picked=file.files?.[0];if(!picked)return;if(picked.size>5*1024*1024){status.textContent='Maximum 5 MB';return}
  const body=new FormData();body.append('language_id',root.querySelector('[name=language_id]').value);body.append('image',picked);
  status.textContent='Uploading…';file.disabled=true;
  try{const response=await fetch(mediaUrl,{method:'POST',headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'},body,credentials:'same-origin'});
   if(!response.ok)throw new Error('Upload failed ('+response.status+').');const result=await response.json();if(!result.url)throw new Error('No image URL returned');done(result.url);status.textContent='Uploaded';}
  catch(error){status.textContent=error.message;status.className='site-editor-error'}finally{file.disabled=false}
 });wrap.append(file,status);return wrap;
}
function renderImageBlocks(){
 if(!Array.isArray(state.image_blocks))state.image_blocks=[];
 const box=document.createElement('fieldset');const heading=document.createElement('legend');heading.textContent='Images inserted anywhere on the page';box.append(heading);
 const note=document.createElement('p');note.textContent='Upload or paste an image, then use Pick placement to click an element in the draft preview. Image blocks are saved with the site draft.';box.append(note);
 const add=document.createElement('button');add.type='button';add.textContent='Insert an image';add.addEventListener('click',()=>{snapshot();state.image_blocks.push({id:'image-'+Math.random().toString(36).slice(2,12),url:'',alt:'',selector:'main',index:0,position:'after',width:'normal'});sync();controls.replaceChildren();renderAll()});box.append(add);
 state.image_blocks.forEach((block,index)=>{
  const row=document.createElement('div');row.className='site-editor-image-row';
  row.append(field('Image URL',block.url,v=>block.url=v));row.append(uploadButton(url=>{snapshot();block.url=url;sync();controls.replaceChildren();renderAll()},'Upload image '+(index+1)));
  row.append(field('Alternative text',block.alt,v=>block.alt=v));
  const preview=document.createElement('img');preview.src=block.url||'';preview.alt=block.alt||'Image preview';preview.loading='lazy';row.append(preview);
  row.append(field('Target page element selector',block.selector,v=>block.selector=v));
  const position=document.createElement('select');for(const option of ['before','after']){const el=document.createElement('option');el.value=option;el.textContent=option;position.append(el)}position.value=block.position;position.addEventListener('change',()=>{snapshot();block.position=position.value;sync()});row.append(position);
  const width=document.createElement('select');for(const option of ['normal','wide','full']){const el=document.createElement('option');el.value=option;el.textContent=option;width.append(el)}width.value=block.width;width.addEventListener('change',()=>{snapshot();block.width=width.value;sync()});row.append(width);
  const picker=document.createElement('button');picker.type='button';picker.textContent='Pick placement in preview';picker.addEventListener('click',()=>{pickerTarget=block.id;window.open(pickerUrl,'site-placement-picker','noopener=false')});row.append(picker);
  const remove=document.createElement('button');remove.type='button';remove.textContent='Remove image block';remove.addEventListener('click',()=>{snapshot();state.image_blocks.splice(index,1);sync();controls.replaceChildren();renderAll()});row.append(remove);box.append(row)
 });controls.append(box);
}
let pickerTarget=null;
window.addEventListener('message',event=>{if(event.origin!==location.origin||!event.data||event.data.type!=='site-image-target'||!pickerTarget)return;
 const block=(state.image_blocks||[]).find(i=>i.id===pickerTarget);if(!block)return;
 if(typeof event.data.selector!=='string'||event.data.selector.length>350)return;
 snapshot();block.selector=event.data.selector;block.index=0;block.position='after';pickerTarget=null;sync();controls.replaceChildren();renderAll();
});

function renderBrand(){
 const defaults={primary:state.accent||'#23466f',secondary:'#226a62',surface:'#ffffff',background:'#f8fafc',ink:'#24313d',font:'system',radius:'rounded',density:'comfortable',width:'standard'};
 if(!state.brand||typeof state.brand!=='object')state.brand={...defaults};
 const box=document.createElement('fieldset');const legend=document.createElement('legend');legend.textContent='Global branding and design system';box.append(legend);
 const colourNames={primary:'Primary colour',secondary:'Secondary colour',surface:'Surface colour',background:'Page background',ink:'Text colour'};
 for(const [key,label] of Object.entries(colourNames)){
 const wrap=document.createElement('label');wrap.className='site-editor-field';wrap.append(document.createTextNode(label));
 const color=document.createElement('input');color.type='color';color.value=/^#[0-9a-f]{6}$/i.test(state.brand[key])?state.brand[key]:defaults[key];
 color.addEventListener('focus',snapshot);color.addEventListener('input',()=>{state.brand[key]=color.value;if(key==='primary')state.accent=color.value;sync()});wrap.append(color);box.append(wrap);
 }
 const options={font:['system','humanist','geometric','serif'],radius:['square','subtle','rounded','pill'],density:['compact','comfortable','spacious'],width:['narrow','standard','wide']};
 for(const [key,choices] of Object.entries(options)){const wrap=document.createElement('label');wrap.className='site-editor-field';wrap.append(document.createTextNode(key));const select=document.createElement('select');
 choices.forEach(value=>{const option=document.createElement('option');option.value=value;option.textContent=value;select.append(option)});select.value=choices.includes(state.brand[key])?state.brand[key]:defaults[key];
 select.addEventListener('change',()=>{snapshot();state.brand[key]=select.value;sync()});wrap.append(select);box.append(wrap)}
 const reset=document.createElement('button');reset.type='button';reset.textContent='Reset brand tokens to defaults';reset.addEventListener('click',()=>{snapshot();state.brand={...defaults};sync();controls.replaceChildren();renderAll()});box.append(reset);controls.append(box);
}
function renderAll(){renderBrand();for(const key of plain){controls.append(field(labels[key]||key.replace(/_/g,' '),state[key],v=>state[key]=v,key==='about'||key==='tagline'||key==='seo_description'));if(key==='seo_image')controls.append(uploadButton(url=>{snapshot();state.seo_image=url;sync();controls.replaceChildren();renderAll()},'Upload social preview image'))}
const collections={services:['name','description'],faqs:['question','answer'],testimonials:['name','quote'],team:['name','role'],gallery:['url','alt'],service_areas:['name','description'],promotions:['code','description','expires'],blog_posts:['title','summary','body','slug']};
for(const [key,props] of Object.entries(collections)){if(!Array.isArray(state[key]))state[key]=[];const box=document.createElement('fieldset');const title=document.createElement('legend');title.textContent=key;box.append(title);const items=document.createElement('div');box.append(items);const add=document.createElement('button');add.type='button';add.textContent='Add '+key.slice(0,-1);add.addEventListener('click',()=>{if(state[key].length>=20)return;snapshot();const obj={};props.forEach(p=>obj[p]='');state[key].push(obj);sync();render()});box.append(add);controls.append(box);
function render(){items.replaceChildren();state[key].forEach((item,index)=>{const row=document.createElement('div');row.className='site-editor-row';for(const p of props){row.append(field(p,item[p],v=>item[p]=v,p==='description'||p==='answer'||p==='quote'||p==='body'||p==='summary'));if(key==='gallery'&&p==='url')row.append(uploadButton(url=>{snapshot();item.url=url;sync();render()},'Upload gallery image'))}const up=document.createElement('button');up.type='button';up.textContent='↑';up.setAttribute('aria-label','Move up');up.disabled=index===0;up.addEventListener('click',()=>{snapshot();[state[key][index-1],state[key][index]]=[state[key][index],state[key][index-1]];sync();render()});row.append(up);const down=document.createElement('button');down.type='button';down.textContent='↓';down.setAttribute('aria-label','Move down');down.disabled=index===state[key].length-1;down.addEventListener('click',()=>{snapshot();[state[key][index+1],state[key][index]]=[state[key][index],state[key][index+1]];sync();render()});row.append(down);const remove=document.createElement('button');remove.type='button';remove.textContent='Remove';remove.addEventListener('click',()=>{snapshot();state[key].splice(index,1);sync();render()});row.append(remove);items.append(row)})}render()}
for(const [store,label,definitions] of [['theme_copy','Template text',themeFields.copy||{}],['theme_images','Template images',themeFields.images||{}]]){
 if(!Object.keys(definitions).length)continue;
 if(!state[store] || typeof state[store]!=='object')state[store]={};
 const section=document.createElement('fieldset');const heading=document.createElement('legend');heading.textContent=label;section.append(heading);
 for(const [key,defaultValue] of Object.entries(definitions)){
  const text=store==='theme_copy'?defaultValue:key+' — '+defaultValue;
  const current=Object.prototype.hasOwnProperty.call(state[store],key)?state[store][key]:defaultValue;
  section.append(field(text,current,v=>state[store][key]=v,store==='theme_copy'&&current.length>80));
  if(store==='theme_images'){section.append(uploadButton(url=>{snapshot();state[store][key]=url;sync();controls.replaceChildren();renderAll()},'Upload '+key));const preview=document.createElement('img');preview.src=current;preview.alt='Image '+key;preview.loading='lazy';preview.style.cssText='display:block;width:180px;max-height:120px;object-fit:cover';section.append(preview)}
 }
 controls.append(section);
}
renderImageBlocks();
const analytics=document.createElement('label');analytics.className='site-editor-field';const flag=document.createElement('input');flag.type='checkbox';flag.checked=state.analytics_consent===true;flag.addEventListener('change',()=>{state.analytics_consent=flag.checked;sync()});analytics.append(flag,document.createTextNode(' Enable optional, consent-gated analytics'));controls.append(analytics)}toolbar();renderAll();sync();raw.hidden=true;raw.setAttribute('aria-hidden','true');const toggle=document.createElement('button');toggle.type='button';toggle.textContent='Show JSON advanced editor';toggle.addEventListener('click',()=>{raw.hidden=!raw.hidden;raw.setAttribute('aria-hidden',String(raw.hidden));toggle.textContent=raw.hidden?'Show JSON advanced editor':'Hide JSON editor';if(!raw.hidden)raw.focus()});controls.after(toggle);raw.addEventListener('input',()=>{try{state=JSON.parse(raw.value)}catch(e){return}})
})();
