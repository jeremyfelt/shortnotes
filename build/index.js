!function(e){var t={};function n(o){if(t[o])return t[o].exports;var r=t[o]={i:o,l:!1,exports:{}};return e[o].call(r.exports,r,r.exports,n),r.l=!0,r.exports}n.m=e,n.c=t,n.d=function(e,t,o){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:o})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var o=Object.create(null);if(n.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var r in e)n.d(o,r,function(t){return e[t]}.bind(null,r));return o},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=7)}([function(e,t){e.exports=window.wp.i18n},function(e,t){e.exports=window.wp.element},function(e,t){e.exports=window.wp.data},function(e,t){e.exports=function(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e},e.exports.default=e.exports,e.exports.__esModule=!0},function(e,t){e.exports=window.wp.plugins},function(e,t){e.exports=window.wp.components},function(e,t){e.exports=window.wp.editPost},function(e,t,n){"use strict";n.r(t);var o=n(3),r=n.n(o),i=n(1),u=n(0),l=n(4),c=n(5),p=n(6),a=n(2);Object(l.registerPlugin)("reply-to-panel",{render:function(){var e=Object(a.useSelect)((function(e){return e("core/editor").getEditedPostAttribute("meta")})),t=Object(a.useDispatch)("core/editor").editPost;return Object(i.createElement)(p.PluginDocumentSettingPanel,{name:"reply-to-panel",title:Object(u.__)("Reply to","shortnotes"),icon:!1},Object(i.createElement)(c.TextareaControl,{label:Object(u.__)("Reply to URL (optional)","shortnotes"),help:Object(u.__)("Enter a URL if this note is a reply","shortnotes"),value:e.shortnotes_reply_to_url,onChange:function(e){t({meta:r()({},"shortnotes_reply_to_url",e)})}}))},icon:""})}]);