!function(){"use strict";var e={n:function(n){var t=n&&n.__esModule?function(){return n.default}:function(){return n};return e.d(t,{a:t}),t},d:function(n,t){for(var r in t)e.o(t,r)&&!e.o(n,r)&&Object.defineProperty(n,r,{enumerable:!0,get:t[r]})},o:function(e,n){return Object.prototype.hasOwnProperty.call(e,n)}},n=window.wp.apiFetch,t=e.n(n),r=window.DISQAdminCommonBackend||{},i=window.document.querySelector(".divi-squad-notice .divi-squad-banner");if(i&&r.rest_api){var o=r.rest_api,c=function(e){arguments.length>1&&void 0!==arguments[1]&&arguments[1]&&i.remove(),t()({path:"".concat(o.namespace,"/").concat(o.routes[e].root)})};i.querySelector(".divi-squad-notice-action-button").addEventListener("click",(function(){return c("reviewDone",!0),!0})),i.querySelector(".divi-squad-notice-close").addEventListener("click",(function(){return c("reviewNextWeek",!0)})),i.querySelector(".divi-squad-notice-already").addEventListener("click",(function(){return c("reviewDone",!0)})),i.querySelector("a.support").addEventListener("click",(function(){return c("reviewAskSupportCount",!0),!0})),setTimeout((function(){i.querySelector("button.notice-dismiss").addEventListener("click",(function(){return c("reviewNoticeCloseCount",!0),!0}))}),800)}}();