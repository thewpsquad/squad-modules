!function(){"use strict";var e={n:function(t){var r=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(r,{a:r}),r},d:function(t,r){for(var n in r)e.o(r,n)&&!e.o(t,n)&&Object.defineProperty(t,n,{enumerable:!0,get:r[n]})},o:function(e,t){return Object.prototype.hasOwnProperty.call(e,t)}},t=window.jQuery,r=e.n(t),n=window.lightGallery,o=e.n(n);function i(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}r()((()=>{var e,t;e=".disq_image_gallery",t=".gallery-images",r()(e).each(((e,n)=>{if(n.querySelector(t)){const e=n.querySelector(t),c={show_in_lightbox:"off",lightbox:{licenseKey:"open-source_wp-plugin_squad-modules-for-divi",speed:500,mode:"lg-slide",download:!1}};if(e?.dataset.setting&&e?.dataset.setting.length){const t=r()(e);t?.imagesLoaded().progress((function(){t?.isotope({layoutMode:"fitRows",itemSelector:".gallery-item",percentPosition:!0,masonry:{columnWidth:".gallery-item"}})})).done((function(){const t=Object.assign(c,JSON.parse(e.dataset.setting));"on"===t.show_in_lightbox&&o()(e,function(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?i(Object(r),!0).forEach((function(t){var n,o,i;n=e,o=t,i=r[t],(o=function(e){var t=function(e,t){if("object"!=typeof e||null===e)return e;var r=e[Symbol.toPrimitive];if(void 0!==r){var n=r.call(e,"string");if("object"!=typeof n)return n;throw new TypeError("@@toPrimitive must return a primitive value.")}return String(e)}(e);return"symbol"==typeof t?t:String(t)}(o))in n?Object.defineProperty(n,o,{value:i,enumerable:!0,configurable:!0,writable:!0}):n[o]=i})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):i(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}({},t.lightbox))}))}}}))}))}();