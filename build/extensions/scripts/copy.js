(()=>{"use strict";var t={n:o=>{var n=o&&o.__esModule?()=>o.default:()=>o;return t.d(n,{a:n}),n},d:(o,n)=>{for(var e in n)t.o(n,e)&&!t.o(o,e)&&Object.defineProperty(o,e,{enumerable:!0,get:n[e]})},o:(t,o)=>Object.prototype.hasOwnProperty.call(t,o)};const o=window.jQuery;t.n(o)()((t=>{const o=t(".wp-list-table .copy_this a.copy-this-post-link");if(o&&"function"==typeof o.tooltipster){const n=n=>{t(".divi-squad-ext-copy-this .ext-copy-loader-overlay").css({opacity:1,display:"block"}),o.tooltipster("hide");const e=window.DiviSquadExtCopy||{isMultisite:"",currentSiteID:"",selectOptions:"",ajaxURL:"",ajaxNonce:"",ajaxAction:"",l10n:{copy:"Copy",toast_title:"Squad Copy Extension"}},i={action:e.ajaxAction,_wpnonce:e.ajaxNonce,copyQueryOptions:n};t.post(e.ajaxURL,i,(function(o){o&&"success"===o.type&&(t(".divi-squad-ext-copy-this .ext-copy-loader-overlay").css({opacity:0,display:"none"}),t.toast({heading:e.l10n.toast_title,text:o.message,showHideTransition:"slide",allowToastClose:!0,position:"top-right",icon:"success",loader:!1}),window.setTimeout((function(){window.location.reload()}),500))}))},e={content:t("#squad_ext_copy_content"),contentAsHTML:!0,contentCloning:!0,animation:"grow",animationDuration:350,theme:"tooltipster-default",trigger:"hover",interactive:!0,interactiveTolerance:500,trackTooltip:!1,zIndex:9999999,anchor:"top-center",position:"bottom",restoration:"none",functionReady(t){t._$origin.parent().parent().css({position:"static"}),t.__Content.find(".disq-admin-button.fill-button").on("click",(function(){n({postID:t._$origin.data("id"),siteID:t.__Content.find("select").val()||1,postCount:t.__Content.find('input[type="number"]').val()||1})}))},functionAfter(t){t._$origin.parent().parent().removeAttr("style")},hideOnClick:!0,debug:!0};o.tooltipster(e),o.on("click",(function(o){n({postID:t(o.target).data("id"),siteID:1,postCount:1}),o.preventDefault()}))}}))})();