(()=>{"use strict";var e={n:t=>{var r=t&&t.__esModule?()=>t.default:()=>t;return e.d(r,{a:r}),r},d:(t,r)=>{for(var o in r)e.o(r,o)&&!e.o(t,o)&&Object.defineProperty(t,o,{enumerable:!0,get:r[o]})},o:(e,t)=>Object.prototype.hasOwnProperty.call(e,t)};const t=window.jQuery;var r=e.n(t);const o=window.lottie;var n=e.n(o);const i=(e,t,r)=>e&&t&&r.indexOf(t)&&e[`${t}${r}`]?e[`${t}${r}`]:e&&e[r]?e[r]:null,l=(e,t)=>{r()(t).on("click",(()=>{e.pause()}))};r()((()=>{var e,t;e=".disq_divider",t=".squad-lottie-player.lottie-player-container",r()(e).each(((e,o)=>{if(r()(o).find(t).length){const e=r()(o).find(t),a=r()(e).attr("data-src"),d=r()(e).attr("data-options"),c=(e=>{try{return JSON.parse(e),!0}catch(e){return!1}})(d)?JSON.parse(d):{},s={autoplay:!1,loop:0,hover:!1,renderer:"svg",speed:1,direction:1,playCount:0,background:"transparent",delay:0,mode:"normal",interaction:"hover",rendererSettings:{hideOnTransparent:!0,progressiveLoad:!0}},p=i(c.moduleReference,c.fieldPrefix,"lottie_direction"),u=i(c.moduleReference,c.fieldPrefix,"lottie_speed"),f=i(c.moduleReference,c.fieldPrefix,"lottie_delay"),m=i(c.moduleReference,c.fieldPrefix,"lottie_renderer"),y=i(c.moduleReference,c.fieldPrefix,"lottie_mode"),g=i(c.moduleReference,c.fieldPrefix,"lottie_loop"),v=i(c.moduleReference,c.fieldPrefix,"lottie_loop_no_times"),_=i(c.moduleReference,c.fieldPrefix,"lottie_trigger_method"),b=i(c.moduleReference,c.fieldPrefix,"lottie_scroll"),w={autoplay:["none","freeze-click"].includes(_),direction:p?Number.parseInt(p):1,speed:u?Number.parseFloat(u):1,delay:f?Number.parseFloat(f):0,renderer:m||"svg",mode:y||"normal"},h=Object.assign(s,w);if(g&&"on"===g){const e=v?Number.parseInt(v):0;h.loop=v&&e>0?Number.parseInt(v):-1}const P=(e=>{if("object"==typeof e)return e;try{return JSON.parse(e)}catch(t){return new URL(e,window.location.href).toString()}})(a),R="string"==typeof P?"path":"animationData";setTimeout((()=>{const e=n().loadAnimation({container:o.querySelector(t),autoplay:h.autoplay,renderer:h.renderer,loop:h.loop,[R]:P,rendererSettings:h.rendererSettings});if(1!==h.speed&&e.setSpeed(Number.parseFloat(String(h.speed))),-1===h.direction?e.setDirection(-1):e.setDirection(1),"hover"===_&&(((e,t)=>{r()(t).on("mouseover",(()=>{e.play()}))})(e,o),"reverse"===i(c.moduleReference,c.fieldPrefix,"lottie_mouseout_action")&&o&&o.addEventListener("mouseout",(()=>{((e,t)=>{t.loop,e.stop(),e.setDirection(-1*e.playDirection),e.play()})(e,h)})),"lock"===i(c.moduleReference,c.fieldPrefix,"lottie_click_action")&&l(e,o)),"freeze-click"===_&&l(e,o),"click"===_&&((e,t)=>{r()(t).on("click",(()=>{e.play()}))})(e,o),"play-on-show"===_&&new window.IntersectionObserver((function([t]){t.isIntersecting&&(t.target.classList.add("intersecting"),e.play())}),{threshold:Number.parseFloat(String(h.delay))}).observe(o.querySelector(t)),"scroll"===_){let t,r=0;const n=window.getComputedStyle(o);let i="";window.addEventListener("scroll",(function(){document.body.getBoundingClientRect().top,-1===h.direction?e.setDirection(-1):e.setDirection(1),r=document.body.getBoundingClientRect().top,window.clearTimeout(t),"page"===b?n.getPropertyValue("transform")!==i&&e.play():e.play(),t=setTimeout((function(){e.pause(),i=n.getPropertyValue("transform")}),30)}),!1)}"bounce"===h.mode&&["svg","canvas"].includes(h.renderer)&&((e,t)=>{let r=0;t.loop?e.addEventListener("loopComplete",(()=>{e.stop(),e.setDirection(-1*e.playDirection),e.play()})):e.addEventListener("complete",(()=>{r++,r<2&&(e.setDirection(-1*e.playDirection),e.play())}))})(e,h)}),["none","scroll"].includes(_)?0:h.delay)}}))}))})();