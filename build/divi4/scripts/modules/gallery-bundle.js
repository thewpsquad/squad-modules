(()=>{"use strict";var e={n:o=>{var t=o&&o.__esModule?()=>o.default:()=>o;return e.d(t,{a:t}),t},d:(o,t)=>{for(var i in t)e.o(t,i)&&!e.o(o,i)&&Object.defineProperty(o,i,{enumerable:!0,get:t[i]})},o:(e,o)=>Object.prototype.hasOwnProperty.call(e,o)};const o=window.jQuery;var t=e.n(o);t()((()=>{var e;e=".gallery-images",t()(".disq_image_gallery").each(((o,i)=>{if(i.querySelector(e)){const o=i.querySelector(e),n={show_in_lightbox:"off",lightbox:{licenseKey:"open-source_wp-plugin_squad-modules-for-divi",speed:500,mode:"lg-slide",download:!1}};if(window.console.log(n),window.console.log(o?.dataset.setting),o?.dataset.setting&&o?.dataset.setting.length){const e=t()(o);e?.imagesLoaded().progress((function(){window.console.log("image loading"),e?.isotope({layoutMode:"fitRows",itemSelector:".gallery-item",percentPosition:!0,masonry:{columnWidth:".gallery-item"}})})).done((function(){const e=Object.assign(n,JSON.parse(o.dataset.setting));"on"===e.show_in_lightbox&&window.lightGallery&&window.lightGallery(o,{...e.lightbox})}))}}}))})),window.console.log("test")})();