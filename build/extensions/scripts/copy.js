(()=>{"use strict";var t={n:o=>{var n=o&&o.__esModule?()=>o.default:()=>o;return t.d(n,{a:n}),n},d:(o,n)=>{for(var e in n)t.o(n,e)&&!t.o(o,e)&&Object.defineProperty(o,e,{enumerable:!0,get:n[e]})},o:(t,o)=>Object.prototype.hasOwnProperty.call(t,o)};const o=window.lodash;var n=t.n(o);const e=window.jQuery;t.n(e)()((t=>{const o=t(".wp-list-table .copy_this a.copy-this-post-link");if(o&&"function"==typeof o.tooltipster){const e=e=>{t(".divi-squad-ext-copy-this .ext-copy-loader-overlay").css({opacity:1,display:"block"}),o.tooltipster("hide");const i=n().merge({ajax_url:"",ajax_nonce_copy:"",ajax_action_copy:"",l10n:{copy:"Copy",toast_title:"Squad Copy Extension"}},window.DiviSquadExtra,window.DiviSquadProExtra),a={action:i.ajax_action_copy,_wpnonce:i.ajax_nonce_copy,copyQueryOptions:e};t.post(i.ajax_url,a,(function(o){o&&"success"===o.type&&(t(".divi-squad-ext-copy-this .ext-copy-loader-overlay").css({opacity:0,display:"none"}),t.toast({heading:i.l10n.toast_title,text:o.message,showHideTransition:"slide",allowToastClose:!0,position:"top-right",icon:"success",loader:!1}),window.setTimeout((function(){window.location.reload()}),500))}))},i={content:t("#squad_ext_copy_content"),contentAsHTML:!0,contentCloning:!0,animation:"grow",animationDuration:350,theme:"tooltipster-default",trigger:"hover",interactive:!0,interactiveTolerance:500,trackTooltip:!1,zIndex:9999999,anchor:"top-center",position:"bottom",restoration:"none",functionReady(t){t._$origin.parent().parent().css({position:"static"}),t.__Content.find(".squad-admin-button.fill-button").on("click",(function(){e({postID:t._$origin.data("id"),siteID:t.__Content.find("select").val()||1,postCount:t.__Content.find('input[type="number"]').val()||1})}))},functionAfter(t){t._$origin.parent().parent().removeAttr("style")},hideOnClick:!0,debug:!0};o.tooltipster(i),o.on("click",(function(o){e({postID:t(o.target).data("id"),siteID:1,postCount:1}),o.preventDefault()}))}}))})();