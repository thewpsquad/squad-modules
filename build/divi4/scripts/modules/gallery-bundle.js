!function(){"use strict";var e={n:function(t){var r=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(r,{a:r}),r},d:function(t,r){for(var o in r)e.o(r,o)&&!e.o(t,o)&&Object.defineProperty(t,o,{enumerable:!0,get:r[o]})},o:function(e,t){return Object.prototype.hasOwnProperty.call(e,t)}},t=window.jQuery,r=e.n(t),o=window.lightGallery,n=e.n(o);function i(e){return i="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},i(e)}function u(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);t&&(o=o.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,o)}return r}r()((function(){var e,t;e=".disq_image_gallery",t=".gallery-images",r()(e).each((function(e,o){if(o.querySelector(t)){var c=o.querySelector(t),l={show_in_lightbox:"off",lightbox:{licenseKey:"open-source_wp-plugin_squad-modules-for-divi",speed:500,mode:"lg-slide",download:!1}};if(c.dataset.setting&&c.dataset.setting.length){var a=r()(c);a.imagesLoaded().progress((function(){a.isotope({layout_mode:"fitRows",itemSelector:".gallery-item",percentPosition:!0,masonry:{columnWidth:".gallery-item"}})})).done((function(){var e=Object.assign(l,JSON.parse(c.dataset.setting));"on"===e.show_in_lightbox&&n()(c,function(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?u(Object(r),!0).forEach((function(t){var o,n,u;o=e,n=t,u=r[t],(n=function(e){var t=function(e,t){if("object"!==i(e)||null===e)return e;var r=e[Symbol.toPrimitive];if(void 0!==r){var o=r.call(e,"string");if("object"!==i(o))return o;throw new TypeError("@@toPrimitive must return a primitive value.")}return String(e)}(e);return"symbol"===i(t)?t:String(t)}(n))in o?Object.defineProperty(o,n,{value:u,enumerable:!0,configurable:!0,writable:!0}):o[n]=u})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):u(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}({},e.lightbox))}))}}}))}))}();