!function(){"use strict";var t={n:function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,{a:n}),n},d:function(e,n){for(var r in n)t.o(n,r)&&!t.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:n[r]})},o:function(t,e){return Object.prototype.hasOwnProperty.call(t,e)}},e=window.jQuery,n=t.n(e);n()((()=>{var t;t=".scrolling-element",n()(".disq_scrolling_text").each(((e,r)=>{if(n()(r).find(t).length){const e=n()(r).find(t);if(e[0]){var a;const o=n()(e).attr("data-scroll-direction"),i=n()(e).attr("data-scroll-speed"),l=n()(e).attr("data-repeat-text"),u=n()(e).attr("data-scroll-pause");n()(r).find(t).marquee({duration:null!==(a=Number.parseInt(i))&&void 0!==a?a:15e3,gap:50,delayBeforeStart:0,direction:null!=o?o:"left",duplicated:!!l&&"on"===l,pauseOnHover:!!u&&"on"===u})}}}))}))}();