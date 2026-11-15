(function(){'use strict';
const data=document.getElementById('site-image-block-data');if(!data)return;
let blocks=[];try{blocks=JSON.parse(data.textContent)}catch(e){return}
const widths={normal:'720px',wide:'1080px',full:'100%'};
for(const block of blocks){if(!block.url||!block.selector)continue;let target;try{target=document.querySelectorAll(block.selector)[block.index||0]}catch(e){continue}if(!target)continue;
 const figure=document.createElement('figure');figure.className='site-inserted-media';figure.dataset.imageId=block.id;
 figure.style.cssText='width:100%;max-width:'+(widths[block.width]||widths.normal)+';margin:1.5rem auto;box-sizing:border-box';
 const img=document.createElement('img');img.src=block.url;img.alt=block.alt||'';img.loading='lazy';img.style.cssText='width:100%;height:auto;display:block';figure.append(img);
 if(block.position==='before')target.before(figure);else target.after(figure);
}
if(window.sitePlacementPicker){
 const style=document.createElement('style');style.textContent='.site-placement-hover{outline:3px dashed #ff9c35!important;outline-offset:3px!important;cursor:crosshair!important}';document.head.append(style);
 const hint=document.createElement('div');hint.textContent='Click any page element to insert an image after it. Press Escape to cancel.';hint.style.cssText='position:fixed;z-index:2147483647;top:10px;left:10px;background:#122238;color:white;padding:12px;border-radius:8px';document.body.append(hint);
 let selected=null;document.addEventListener('mouseover',e=>{if(selected)selected.classList.remove('site-placement-hover');selected=e.target.closest('section,header,footer,article,div,p,h1,h2,h3,figure,main,nav')||e.target;selected.classList.add('site-placement-hover')});
 function path(element){const parts=[];while(element&&element.nodeType===1&&element!==document.documentElement){let part=element.tagName.toLowerCase();if(element.id&&/^[a-zA-Z][\w-]*$/.test(element.id)){part='#'+element.id;parts.unshift(part);break}let i=1;for(let sib=element.previousElementSibling;sib;sib=sib.previousElementSibling)if(sib.tagName===element.tagName)i++;part+=':nth-of-type('+i+')';parts.unshift(part);element=element.parentElement}return parts.join(' > ')}
 document.addEventListener('click',e=>{if(e.target===hint)return;e.preventDefault();e.stopPropagation();const selector=path(selected||e.target);window.opener?.postMessage({type:'site-image-target',selector},location.origin);hint.textContent='Placement selected. Return to the editor and save the draft.'},true);
 document.addEventListener('keydown',e=>{if(e.key==='Escape')window.close()});
}
})();
