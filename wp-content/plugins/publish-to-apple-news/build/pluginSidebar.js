!function(e){var t={};function n(r){if(t[r])return t[r].exports;var i=t[r]={i:r,l:!1,exports:{}};return e[r].call(i.exports,i,i.exports,n),i.l=!0,i.exports}n.m=e,n.c=t,n.d=function(e,t,r){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var i in e)n.d(r,i,function(t){return e[t]}.bind(null,i));return r},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=4)}([function(e,t,n){e.exports=n(2)()},function(e,t,n){e.exports=function(){"use strict";var e=Object.hasOwnProperty,t=Object.setPrototypeOf,n=Object.isFrozen,r=Object.keys,i=Object.freeze,o=Object.seal,a="undefined"!=typeof Reflect&&Reflect,l=a.apply,c=a.construct;l||(l=function(e,t,n){return e.apply(t,n)}),i||(i=function(e){return e}),o||(o=function(e){return e}),c||(c=function(e,t){return new(Function.prototype.bind.apply(e,[null].concat(function(e){if(Array.isArray(e)){for(var t=0,n=Array(e.length);t<e.length;t++)n[t]=e[t];return n}return Array.from(e)}(t))))});var s=S(Array.prototype.forEach),u=S(Array.prototype.indexOf),p=S(Array.prototype.join),d=S(Array.prototype.pop),f=S(Array.prototype.push),m=S(Array.prototype.slice),h=S(String.prototype.toLowerCase),y=S(String.prototype.match),v=S(String.prototype.replace),g=S(String.prototype.indexOf),b=S(String.prototype.trim),w=S(RegExp.prototype.test),_=R(RegExp),E=R(TypeError);function S(e){return function(t){for(var n=arguments.length,r=Array(n>1?n-1:0),i=1;i<n;i++)r[i-1]=arguments[i];return l(e,t,r)}}function R(e){return function(){for(var t=arguments.length,n=Array(t),r=0;r<t;r++)n[r]=arguments[r];return c(e,n)}}function A(e,r){t&&t(e,null);for(var i=r.length;i--;){var o=r[i];if("string"==typeof o){var a=h(o);a!==o&&(n(r)||(r[i]=a),o=a)}e[o]=!0}return e}function T(t){var n={},r=void 0;for(r in t)l(e,t,[r])&&(n[r]=t[r]);return n}var O=i(["a","abbr","acronym","address","area","article","aside","audio","b","bdi","bdo","big","blink","blockquote","body","br","button","canvas","caption","center","cite","code","col","colgroup","content","data","datalist","dd","decorator","del","details","dfn","dir","div","dl","dt","element","em","fieldset","figcaption","figure","font","footer","form","h1","h2","h3","h4","h5","h6","head","header","hgroup","hr","html","i","img","input","ins","kbd","label","legend","li","main","map","mark","marquee","menu","menuitem","meter","nav","nobr","ol","optgroup","option","output","p","picture","pre","progress","q","rp","rt","ruby","s","samp","section","select","shadow","small","source","spacer","span","strike","strong","style","sub","summary","sup","table","tbody","td","template","textarea","tfoot","th","thead","time","tr","track","tt","u","ul","var","video","wbr"]),P=i(["svg","a","altglyph","altglyphdef","altglyphitem","animatecolor","animatemotion","animatetransform","audio","canvas","circle","clippath","defs","desc","ellipse","filter","font","g","glyph","glyphref","hkern","image","line","lineargradient","marker","mask","metadata","mpath","path","pattern","polygon","polyline","radialgradient","rect","stop","style","switch","symbol","text","textpath","title","tref","tspan","video","view","vkern"]),k=i(["feBlend","feColorMatrix","feComponentTransfer","feComposite","feConvolveMatrix","feDiffuseLighting","feDisplacementMap","feDistantLight","feFlood","feFuncA","feFuncB","feFuncG","feFuncR","feGaussianBlur","feMerge","feMergeNode","feMorphology","feOffset","fePointLight","feSpecularLighting","feSpotLight","feTile","feTurbulence"]),x=i(["math","menclose","merror","mfenced","mfrac","mglyph","mi","mlabeledtr","mmultiscripts","mn","mo","mover","mpadded","mphantom","mroot","mrow","ms","mspace","msqrt","mstyle","msub","msup","msubsup","mtable","mtd","mtext","mtr","munder","munderover"]),C=i(["#text"]),N=i(["accept","action","align","alt","autocomplete","background","bgcolor","border","cellpadding","cellspacing","checked","cite","class","clear","color","cols","colspan","controls","coords","crossorigin","datetime","default","dir","disabled","download","enctype","face","for","headers","height","hidden","high","href","hreflang","id","integrity","ismap","label","lang","list","loop","low","max","maxlength","media","method","min","minlength","multiple","name","noshade","novalidate","nowrap","open","optimum","pattern","placeholder","poster","preload","pubdate","radiogroup","readonly","rel","required","rev","reversed","role","rows","rowspan","spellcheck","scope","selected","shape","size","sizes","span","srclang","start","src","srcset","step","style","summary","tabindex","title","type","usemap","valign","value","width","xmlns"]),M=i(["accent-height","accumulate","additive","alignment-baseline","ascent","attributename","attributetype","azimuth","basefrequency","baseline-shift","begin","bias","by","class","clip","clip-path","clip-rule","color","color-interpolation","color-interpolation-filters","color-profile","color-rendering","cx","cy","d","dx","dy","diffuseconstant","direction","display","divisor","dur","edgemode","elevation","end","fill","fill-opacity","fill-rule","filter","filterunits","flood-color","flood-opacity","font-family","font-size","font-size-adjust","font-stretch","font-style","font-variant","font-weight","fx","fy","g1","g2","glyph-name","glyphref","gradientunits","gradienttransform","height","href","id","image-rendering","in","in2","k","k1","k2","k3","k4","kerning","keypoints","keysplines","keytimes","lang","lengthadjust","letter-spacing","kernelmatrix","kernelunitlength","lighting-color","local","marker-end","marker-mid","marker-start","markerheight","markerunits","markerwidth","maskcontentunits","maskunits","max","mask","media","method","mode","min","name","numoctaves","offset","operator","opacity","order","orient","orientation","origin","overflow","paint-order","path","pathlength","patterncontentunits","patterntransform","patternunits","points","preservealpha","preserveaspectratio","primitiveunits","r","rx","ry","radius","refx","refy","repeatcount","repeatdur","restart","result","rotate","scale","seed","shape-rendering","specularconstant","specularexponent","spreadmethod","stddeviation","stitchtiles","stop-color","stop-opacity","stroke-dasharray","stroke-dashoffset","stroke-linecap","stroke-linejoin","stroke-miterlimit","stroke-opacity","stroke","stroke-width","style","surfacescale","tabindex","targetx","targety","transform","text-anchor","text-decoration","text-rendering","textlength","type","u1","u2","unicode","values","viewbox","visibility","version","vert-adv-y","vert-origin-x","vert-origin-y","width","word-spacing","wrap","writing-mode","xchannelselector","ychannelselector","x","x1","x2","xmlns","y","y1","y2","z","zoomandpan"]),L=i(["accent","accentunder","align","bevelled","close","columnsalign","columnlines","columnspan","denomalign","depth","dir","display","displaystyle","encoding","fence","frame","height","href","id","largeop","length","linethickness","lspace","lquote","mathbackground","mathcolor","mathsize","mathvariant","maxsize","minsize","movablelimits","notation","numalign","open","rowalign","rowlines","rowspacing","rowspan","rspace","rquote","scriptlevel","scriptminsize","scriptsizemultiplier","selection","separator","separators","stretchy","subscriptshift","supscriptshift","symmetric","voffset","width","xmlns"]),D=i(["xlink:href","xml:id","xlink:title","xml:space","xmlns:xlink"]),I=o(/\{\{[\s\S]*|[\s\S]*\}\}/gm),j=o(/<%[\s\S]*|[\s\S]*%>/gm),H=o(/^data-[\-\w.\u00B7-\uFFFF]/),U=o(/^aria-[\-\w]+$/),F=o(/^(?:(?:(?:f|ht)tps?|mailto|tel|callto|cid|xmpp):|[^a-z]|[a-z+.\-]+(?:[^a-z+.\-:]|$))/i),q=o(/^(?:\w+script|data):/i),z=o(/[\u0000-\u0020\u00A0\u1680\u180E\u2000-\u2029\u205f\u3000]/g),B="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e};function W(e){if(Array.isArray(e)){for(var t=0,n=Array(e.length);t<e.length;t++)n[t]=e[t];return n}return Array.from(e)}var G=function(){return"undefined"==typeof window?null:window},K=function(e,t){if("object"!==(void 0===e?"undefined":B(e))||"function"!=typeof e.createPolicy)return null;var n=null;t.currentScript&&t.currentScript.hasAttribute("data-tt-policy-suffix")&&(n=t.currentScript.getAttribute("data-tt-policy-suffix"));var r="dompurify"+(n?"#"+n:"");try{return e.createPolicy(r,{createHTML:function(e){return e}})}catch(e){return console.warn("TrustedTypes policy "+r+" could not be created."),null}};return function e(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:G(),n=function(t){return e(t)};if(n.version="2.0.8",n.removed=[],!t||!t.document||9!==t.document.nodeType)return n.isSupported=!1,n;var o=t.document,a=!1,l=!1,c=t.document,S=t.DocumentFragment,R=t.HTMLTemplateElement,J=t.Node,V=t.NodeFilter,Y=t.NamedNodeMap,Q=void 0===Y?t.NamedNodeMap||t.MozNamedAttrMap:Y,X=t.Text,$=t.Comment,Z=t.DOMParser,ee=t.trustedTypes;if("function"==typeof R){var te=c.createElement("template");te.content&&te.content.ownerDocument&&(c=te.content.ownerDocument)}var ne=K(ee,o),re=ne?ne.createHTML(""):"",ie=c,oe=ie.implementation,ae=ie.createNodeIterator,le=ie.getElementsByTagName,ce=ie.createDocumentFragment,se=o.importNode,ue={};n.isSupported=oe&&void 0!==oe.createHTMLDocument&&9!==c.documentMode;var pe=I,de=j,fe=H,me=U,he=q,ye=z,ve=F,ge=null,be=A({},[].concat(W(O),W(P),W(k),W(x),W(C))),we=null,_e=A({},[].concat(W(N),W(M),W(L),W(D))),Ee=null,Se=null,Re=!0,Ae=!0,Te=!1,Oe=!1,Pe=!1,ke=!1,xe=!1,Ce=!1,Ne=!1,Me=!1,Le=!1,De=!1,Ie=!0,je=!0,He=!1,Ue={},Fe=A({},["annotation-xml","audio","colgroup","desc","foreignobject","head","iframe","math","mi","mn","mo","ms","mtext","noembed","noframes","plaintext","script","style","svg","template","thead","title","video","xmp"]),qe=A({},["audio","video","img","source","image"]),ze=null,Be=A({},["alt","class","for","id","label","name","pattern","placeholder","summary","title","value","style","xmlns"]),We=null,Ge=c.createElement("form"),Ke=function(e){We&&We===e||(e&&"object"===(void 0===e?"undefined":B(e))||(e={}),ge="ALLOWED_TAGS"in e?A({},e.ALLOWED_TAGS):be,we="ALLOWED_ATTR"in e?A({},e.ALLOWED_ATTR):_e,ze="ADD_URI_SAFE_ATTR"in e?A(T(Be),e.ADD_URI_SAFE_ATTR):Be,Ee="FORBID_TAGS"in e?A({},e.FORBID_TAGS):{},Se="FORBID_ATTR"in e?A({},e.FORBID_ATTR):{},Ue="USE_PROFILES"in e&&e.USE_PROFILES,Re=!1!==e.ALLOW_ARIA_ATTR,Ae=!1!==e.ALLOW_DATA_ATTR,Te=e.ALLOW_UNKNOWN_PROTOCOLS||!1,Oe=e.SAFE_FOR_JQUERY||!1,Pe=e.SAFE_FOR_TEMPLATES||!1,ke=e.WHOLE_DOCUMENT||!1,Ne=e.RETURN_DOM||!1,Me=e.RETURN_DOM_FRAGMENT||!1,Le=e.RETURN_DOM_IMPORT||!1,De=e.RETURN_TRUSTED_TYPE||!1,Ce=e.FORCE_BODY||!1,Ie=!1!==e.SANITIZE_DOM,je=!1!==e.KEEP_CONTENT,He=e.IN_PLACE||!1,ve=e.ALLOWED_URI_REGEXP||ve,Pe&&(Ae=!1),Me&&(Ne=!0),Ue&&(ge=A({},[].concat(W(C))),we=[],!0===Ue.html&&(A(ge,O),A(we,N)),!0===Ue.svg&&(A(ge,P),A(we,M),A(we,D)),!0===Ue.svgFilters&&(A(ge,k),A(we,M),A(we,D)),!0===Ue.mathMl&&(A(ge,x),A(we,L),A(we,D))),e.ADD_TAGS&&(ge===be&&(ge=T(ge)),A(ge,e.ADD_TAGS)),e.ADD_ATTR&&(we===_e&&(we=T(we)),A(we,e.ADD_ATTR)),e.ADD_URI_SAFE_ATTR&&A(ze,e.ADD_URI_SAFE_ATTR),je&&(ge["#text"]=!0),ke&&A(ge,["html","head","body"]),ge.table&&(A(ge,["tbody"]),delete Ee.tbody),i&&i(e),We=e)},Je=function(e){f(n.removed,{element:e});try{e.parentNode.removeChild(e)}catch(t){e.outerHTML=re}},Ve=function(e,t){try{f(n.removed,{attribute:t.getAttributeNode(e),from:t})}catch(e){f(n.removed,{attribute:null,from:t})}t.removeAttribute(e)},Ye=function(e){var t=void 0,n=void 0;if(Ce)e="<remove></remove>"+e;else{var r=y(e,/^[\s]+/);n=r&&r[0]}var i=ne?ne.createHTML(e):e;if(a)try{t=(new Z).parseFromString(i,"text/html")}catch(e){}if(l&&A(Ee,["title"]),!t||!t.documentElement){var o=(t=oe.createHTMLDocument("")).body;o.parentNode.removeChild(o.parentNode.firstElementChild),o.outerHTML=i}return e&&n&&t.body.insertBefore(c.createTextNode(n),t.body.childNodes[0]||null),le.call(t,ke?"html":"body")[0]};n.isSupported&&(function(){try{Ye('<svg><p><textarea><img src="</textarea><img src=x abc=1//">').querySelector("svg img")&&(a=!0)}catch(e){}}(),function(){try{var e=Ye("<x/><title>&lt;/title&gt;&lt;img&gt;");w(/<\/title/,e.querySelector("title").innerHTML)&&(l=!0)}catch(e){}}());var Qe=function(e){return ae.call(e.ownerDocument||e,e,V.SHOW_ELEMENT|V.SHOW_COMMENT|V.SHOW_TEXT,(function(){return V.FILTER_ACCEPT}),!1)},Xe=function(e){return!(e instanceof X||e instanceof $||"string"==typeof e.nodeName&&"string"==typeof e.textContent&&"function"==typeof e.removeChild&&e.attributes instanceof Q&&"function"==typeof e.removeAttribute&&"function"==typeof e.setAttribute&&"string"==typeof e.namespaceURI)},$e=function(e){return"object"===(void 0===J?"undefined":B(J))?e instanceof J:e&&"object"===(void 0===e?"undefined":B(e))&&"number"==typeof e.nodeType&&"string"==typeof e.nodeName},Ze=function(e,t,r){ue[e]&&s(ue[e],(function(e){e.call(n,t,r,We)}))},et=function(e){var t=void 0;if(Ze("beforeSanitizeElements",e,null),Xe(e))return Je(e),!0;var r=h(e.nodeName);if(Ze("uponSanitizeElement",e,{tagName:r,allowedTags:ge}),("svg"===r||"math"===r)&&0!==e.querySelectorAll("p, br").length)return Je(e),!0;if(!ge[r]||Ee[r]){if(je&&!Fe[r]&&"function"==typeof e.insertAdjacentHTML)try{var i=e.innerHTML;e.insertAdjacentHTML("AfterEnd",ne?ne.createHTML(i):i)}catch(e){}return Je(e),!0}return"noscript"===r&&w(/<\/noscript/i,e.innerHTML)||"noembed"===r&&w(/<\/noembed/i,e.innerHTML)?(Je(e),!0):(!Oe||e.firstElementChild||e.content&&e.content.firstElementChild||!w(/</g,e.textContent)||(f(n.removed,{element:e.cloneNode()}),e.innerHTML?e.innerHTML=v(e.innerHTML,/</g,"&lt;"):e.innerHTML=v(e.textContent,/</g,"&lt;")),Pe&&3===e.nodeType&&(t=e.textContent,t=v(t,pe," "),t=v(t,de," "),e.textContent!==t&&(f(n.removed,{element:e.cloneNode()}),e.textContent=t)),Ze("afterSanitizeElements",e,null),!1)},tt=function(e,t,n){if(Ie&&("id"===t||"name"===t)&&(n in c||n in Ge))return!1;if(Ae&&w(fe,t));else if(Re&&w(me,t));else{if(!we[t]||Se[t])return!1;if(ze[t]);else if(w(ve,v(n,ye,"")));else if("src"!==t&&"xlink:href"!==t&&"href"!==t||"script"===e||0!==g(n,"data:")||!qe[e])if(Te&&!w(he,v(n,ye,"")));else if(n)return!1}return!0},nt=function(e){var t=void 0,i=void 0,o=void 0,a=void 0,l=void 0;Ze("beforeSanitizeAttributes",e,null);var c=e.attributes;if(c){var s={attrName:"",attrValue:"",keepAttr:!0,allowedAttributes:we};for(l=c.length;l--;){var f=t=c[l],y=f.name,g=f.namespaceURI;if(i=b(t.value),o=h(y),s.attrName=o,s.attrValue=i,s.keepAttr=!0,s.forceKeepAttr=void 0,Ze("uponSanitizeAttribute",e,s),i=s.attrValue,!s.forceKeepAttr){if("name"===o&&"IMG"===e.nodeName&&c.id)a=c.id,c=m(c,[]),Ve("id",e),Ve(y,e),u(c,a)>l&&e.setAttribute("id",a.value);else{if("INPUT"===e.nodeName&&"type"===o&&"file"===i&&s.keepAttr&&(we[o]||!Se[o]))continue;"id"===y&&e.setAttribute(y,""),Ve(y,e)}if(s.keepAttr)if(Oe&&w(/\/>/i,i))Ve(y,e);else if(w(/svg|math/i,e.namespaceURI)&&w(_("</("+p(r(Fe),"|")+")","i"),i))Ve(y,e);else{Pe&&(i=v(i,pe," "),i=v(i,de," "));var E=e.nodeName.toLowerCase();if(tt(E,o,i))try{g?e.setAttributeNS(g,y,i):e.setAttribute(y,i),d(n.removed)}catch(e){}}}}Ze("afterSanitizeAttributes",e,null)}},rt=function e(t){var n=void 0,r=Qe(t);for(Ze("beforeSanitizeShadowDOM",t,null);n=r.nextNode();)Ze("uponSanitizeShadowNode",n,null),et(n)||(n.content instanceof S&&e(n.content),nt(n));Ze("afterSanitizeShadowDOM",t,null)};return n.sanitize=function(e,r){var i=void 0,a=void 0,l=void 0,c=void 0,s=void 0;if(e||(e="\x3c!--\x3e"),"string"!=typeof e&&!$e(e)){if("function"!=typeof e.toString)throw E("toString is not a function");if("string"!=typeof(e=e.toString()))throw E("dirty is not a string, aborting")}if(!n.isSupported){if("object"===B(t.toStaticHTML)||"function"==typeof t.toStaticHTML){if("string"==typeof e)return t.toStaticHTML(e);if($e(e))return t.toStaticHTML(e.outerHTML)}return e}if(xe||Ke(r),n.removed=[],"string"==typeof e&&(He=!1),He);else if(e instanceof J)1===(a=(i=Ye("\x3c!--\x3e")).ownerDocument.importNode(e,!0)).nodeType&&"BODY"===a.nodeName||"HTML"===a.nodeName?i=a:i.appendChild(a);else{if(!Ne&&!Pe&&!ke&&De&&-1===e.indexOf("<"))return ne?ne.createHTML(e):e;if(!(i=Ye(e)))return Ne?null:re}i&&Ce&&Je(i.firstChild);for(var u=Qe(He?e:i);l=u.nextNode();)3===l.nodeType&&l===c||et(l)||(l.content instanceof S&&rt(l.content),nt(l),c=l);if(c=null,He)return e;if(Ne){if(Me)for(s=ce.call(i.ownerDocument);i.firstChild;)s.appendChild(i.firstChild);else s=i;return Le&&(s=se.call(o,s,!0)),s}var p=ke?i.outerHTML:i.innerHTML;return Pe&&(p=v(p,pe," "),p=v(p,de," ")),ne&&De?ne.createHTML(p):p},n.setConfig=function(e){Ke(e),xe=!0},n.clearConfig=function(){We=null,xe=!1},n.isValidAttribute=function(e,t,n){We||Ke({});var r=h(e),i=h(t);return tt(r,i,n)},n.addHook=function(e,t){"function"==typeof t&&(ue[e]=ue[e]||[],f(ue[e],t))},n.removeHook=function(e){ue[e]&&d(ue[e])},n.removeHooks=function(e){ue[e]&&(ue[e]=[])},n.removeAllHooks=function(){ue={}},n}()}()},function(e,t,n){"use strict";var r=n(3);function i(){}function o(){}o.resetWarningCache=i,e.exports=function(){function e(e,t,n,i,o,a){if(a!==r){var l=new Error("Calling PropTypes validators directly is not supported by the `prop-types` package. Use PropTypes.checkPropTypes() to call them. Read more at http://fb.me/use-check-prop-types");throw l.name="Invariant Violation",l}}function t(){return e}e.isRequired=e;var n={array:e,bool:e,func:e,number:e,object:e,string:e,symbol:e,any:e,arrayOf:t,element:e,elementType:e,instanceOf:t,node:e,objectOf:t,oneOf:t,oneOfType:t,shape:t,exact:t,checkPropTypes:o,resetWarningCache:i};return n.PropTypes=n,n}},function(e,t,n){"use strict";e.exports="SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED"},function(e,t,n){"use strict";function r(e){return(r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function i(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function o(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function a(e,t){return!t||"object"!==r(t)&&"function"!=typeof t?function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e):t}function l(e){return(l=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}function c(e,t){return(c=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}n.r(t);var s=function(e){function t(){return i(this,t),a(this,l(t).apply(this,arguments))}var n,r,s;return function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&c(e,t)}(t,React.PureComponent),n=t,(r=[{key:"render",value:function(){return React.createElement("svg",{enableBackground:"new 0 0 26.77438 26.77438",height:"26.77438px",version:"1.1",viewBox:"0 0 26.77438 26.77438",width:"26.77438px",x:"0px",xmlns:"http://www.w3.org/2000/svg",y:"0px"},React.createElement("g",null,React.createElement("g",null,React.createElement("g",null,React.createElement("path",{d:"M4.71813,13.5345v4.77055l0.00002,0.65257 c0,0.0568-0.00002,0.11365,0.00035,0.17044c0.00025,0.04783,0.00081,0.0957,0.00209,0.14355 c0.00283,0.10423,0.00899,0.20939,0.02751,0.31251c0.01879,0.10458,0.04945,0.20193,0.0978,0.29693 c0.04756,0.0934,0.10962,0.17891,0.1837,0.253c0.07406,0.07407,0.15948,0.13619,0.25284,0.18372 c0.09503,0.0484,0.19239,0.07911,0.29701,0.09791c0.10299,0.01855,0.20809,0.02465,0.31225,0.02748 c0.04783,0.0013,0.09566,0.00183,0.14351,0.00215c0.05676,0.00033,0.11354,0.00031,0.17035,0.00031l0.65231,0.00002h4.77141 c0.13713,0,0.1819-0.18436,0.05989-0.24694c-2.91487-1.49526-5.22884-3.80922-6.72409-6.72407 c-0.02514-0.04903-0.06995-0.07114-0.11456-0.07114C4.78411,13.40348,4.71813,13.45246,4.71813,13.5345 M6.20869,4.71816 c-0.05692,0-0.11382,0.00002-0.17074,0.00035C5.99004,4.71878,5.9421,4.71937,5.89418,4.72066 C5.78975,4.72349,5.68438,4.72962,5.58112,4.74818C5.47637,4.76705,5.37885,4.79772,5.28367,4.84613 C5.1901,4.89378,5.10448,4.95595,5.03024,5.03016C4.95604,5.10438,4.8938,5.18993,4.84622,5.28341 c-0.0485,0.09524-0.07925,0.19277-0.09809,0.29751C4.72958,5.68414,4.72344,5.78941,4.72059,5.89376 C4.71932,5.94165,4.71875,5.98955,4.7185,6.03746c-0.00035,0.048-0.00037,0.09602-0.00037,0.14401v0.02665v1.96427 c0,0.40087,0.07796,0.6084,0.37512,0.90561l10.99255,10.9925c0.30093,0.30094,0.50562,0.37513,0.90561,0.37513h1.96366 c0.05688,0,0.11384,0,0.17074-0.00036c0.04794-0.00028,0.09584-0.00084,0.14378-0.00216 c0.10446-0.00281,0.20978-0.00894,0.31304-0.02751c0.10479-0.01884,0.20229-0.04954,0.29747-0.09796 c0.09358-0.04766,0.17919-0.10981,0.25338-0.18402c0.07428-0.07423,0.13647-0.15976,0.18408-0.25323 c0.04852-0.09524,0.07924-0.19277,0.09809-0.29752c0.01855-0.10318,0.0247-0.20845,0.0275-0.31283 c0.0013-0.04788,0.00187-0.09577,0.00214-0.14376c0.00031-0.04819,0.00034-0.09648,0.00034-0.14476v-1.99007 c0-0.40094-0.07794-0.60844-0.37513-0.90564L9.07799,5.09329C8.77706,4.79237,8.57234,4.71818,8.17237,4.71816H6.20869 M13.53452,4.71816c-0.13717,0-0.18194,0.18436-0.05994,0.24694c2.9149,1.49524,5.22883,3.8092,6.72409,6.72407 c0.06261,0.12206,0.24697,0.07725,0.24697-0.05992V6.85873l-0.00005-0.65257c0-0.05679,0.00005-0.11363-0.00028-0.17043 c-0.00027-0.04786-0.00084-0.0957-0.00216-0.14353c-0.0028-0.10428-0.00891-0.20943-0.02746-0.31252 c-0.0188-0.1046-0.04947-0.20194-0.09784-0.29695c-0.0475-0.09343-0.10962-0.17888-0.18367-0.25297 c-0.0741-0.0741-0.15947-0.1362-0.25285-0.18375c-0.09504-0.04841-0.19239-0.0791-0.297-0.09789 c-0.10299-0.01854-0.2081-0.02468-0.31226-0.02751c-0.04781-0.00125-0.09565-0.00183-0.14348-0.00211 c-0.0568-0.00035-0.1136-0.00032-0.17037-0.00032l-0.65233-0.00002H13.53452"})))))}}])&&o(n.prototype,r),s&&o(n,s),t}(),u=n(0),p=n.n(u);function d(e){var t=e.media_details,n=(t=void 0===t?{}:t).sizes,r=(n=void 0===n?{}:n)["post-thumbnail"],i=(r=void 0===r?{}:r).source_url,o=void 0===i?"":i,a=e.source_url;return o||(void 0===a?"":a)}function f(e){return(f="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function m(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function h(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function y(e,t){return!t||"object"!==f(t)&&"function"!=typeof t?function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e):t}function v(e){return(v=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}function g(e,t){return(g=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}var b=wp,w=b.components.Button,_=b.data.withSelect,E=b.editor.MediaPlaceholder,S=b.i18n.__,R=function(e){function t(){return m(this,t),y(this,v(t).apply(this,arguments))}var n,r,i;return function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&g(e,t)}(t,React.PureComponent),n=t,(r=[{key:"render",value:function(){var e=this.props,t=e.media,n=e.metaKey,r=e.onUpdate;return e.value&&t&&t.id?React.createElement("div",null,React.createElement("p",null,React.createElement("img",{alt:"",src:d(t)})),React.createElement("p",null,React.createElement(w,{isPrimary:!0,onClick:function(){r(n,0)}},S("Remove image","kauffman")))):React.createElement(E,{accept:"image/*",allowedTypes:["image"],icon:"format-image",labels:{title:S("Select Image","kauffman")},onSelect:function(e){var t=e.id;r(n,t)}})}}])&&h(n.prototype,r),i&&h(n,i),t}();R.propTypes={media:p.a.shape({}).isRequired,metaKey:p.a.string.isRequired,onUpdate:p.a.func.isRequired,value:p.a.number.isRequired};var A=_((function(e,t){var n=t.value,r=e("core").getMedia;return{media:n&&r(n)||{}}}))(R),T=n(1),O=n.n(T),P=function(e,t){var n=t.message,r=void 0===n?"":n,i=t.type;return"success"===(void 0===i?"success":i)?e("core/notices").createInfoNotice(O.a.sanitize(r),{type:"snackbar"}):e("core/notices").createErrorNotice(O.a.sanitize(r))};function k(e){var t=function(e){try{return JSON.parse(e)}catch(e){return null}}(e);return Array.isArray(t)?t:[]}function x(e){return(x="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function C(e){return function(e){if(Array.isArray(e)){for(var t=0,n=new Array(e.length);t<e.length;t++)n[t]=e[t];return n}}(e)||function(e){if(Symbol.iterator in Object(e)||"[object Arguments]"===Object.prototype.toString.call(e))return Array.from(e)}(e)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance")}()}function N(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function M(e){return(M=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}function L(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}function D(e,t){return(D=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}var I=wp,j=I.apiFetch,H=I.compose,U=(H=void 0===H?{}:H).compose,F=I.components,q=(F=void 0===F?{}:F).Button,z=F.CheckboxControl,B=F.PanelBody,W=F.SelectControl,G=F.Spinner,K=F.TextareaControl,J=I.data,V=(J=void 0===J?{}:J).withDispatch,Y=J.withSelect,Q=I.editPost,X=(Q=void 0===Q?{}:Q).PluginSidebar,$=Q.PluginSidebarMoreMenuItem,Z=I.element,ee=(Z=void 0===Z?{}:Z).Fragment,te=I.i18n,ne=(te=void 0===te?{}:te).__,re=function(e){function t(e){var n,r,i;return function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,t),r=this,(n=!(i=M(t).call(this,e))||"object"!==x(i)&&"function"!=typeof i?L(r):i).state={autoAssignCategories:!1,loading:!1,modified:0,publishState:"",sections:[],selectedSectionsPrev:null,settings:{},userCanPublish:!1},n.deletePost=n.deletePost.bind(L(n)),n.displayErrors=n.displayErrors.bind(L(n)),n.publishPost=n.publishPost.bind(L(n)),n.updatePost=n.updatePost.bind(L(n)),n.updateSelectedSections=n.updateSelectedSections.bind(L(n)),n}var n,r,i;return function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&D(e,t)}(t,React.PureComponent),n=t,(r=[{key:"componentDidMount",value:function(){this.fetchPublishState(),this.fetchSections(),this.fetchSettings(),this.fetchUserCanPublish()}},{key:"componentDidUpdate",value:function(e){var t=this.props,n=t.appleNewsNotices,r=void 0===n?[]:n,i=t.displayNotification,o=e.appleNewsNotices,a=void 0===o?[]:o;JSON.stringify(r)!==JSON.stringify(a)&&r.forEach(i)}},{key:"deletePost",value:function(){var e=this.props.post,t=(e=void 0===e?{}:e).id,n=void 0===t?0:t;this.modifyPost(n,"delete")}},{key:"displayErrors",value:function(e){(0,this.props.displayNotification)({dismissed:!1,dismissible:!1,message:e.message,timestamp:Math.ceil(Date.now()/1e3),type:"error"})}},{key:"fetchPublishState",value:function(){var e=this,t=this.props.post,n="/apple-news/v1/get-published-state/".concat(t.id);j({path:n}).then((function(t){var n=t.publishState;return e.setState({publishState:n})})).catch(this.displayErrors)}},{key:"fetchSections",value:function(){var e=this;j({path:"/apple-news/v1/sections"}).then((function(t){return e.setState({sections:t})})).catch(this.displayErrors)}},{key:"fetchSettings",value:function(){var e=this,t=this.props.meta,n=k((t=void 0===t?{}:t).selectedSections)||[];j({path:"/apple-news/v1/get-settings"}).then((function(t){return e.setState({autoAssignCategories:(null===n||0===n.length)&&!0===t.automaticAssignment,settings:t})})).catch(this.displayErrors)}},{key:"fetchUserCanPublish",value:function(){var e=this,t=this.props.post,n="/apple-news/v1/user-can-publish/".concat(t.id);j({path:n}).then((function(t){var n=t.userCanPublish;return e.setState({userCanPublish:n})})).catch(this.displayErrors)}},{key:"modifyPost",value:function(e,t){var n=this,r=this.props.displayNotification,i="/apple-news/v1/".concat(t);this.setState({loading:!0}),j({data:{id:e},method:"POST",path:i}).then((function(e){var t=e.notifications,i=void 0===t?[]:t,o=e.publishState,a=void 0===o?"":o;i.forEach(r),n.setState({loading:!1,publishState:a})})).catch(this.displayErrors).finally((function(){return n.setState({loading:!1})}))}},{key:"publishPost",value:function(){var e=this.props.post,t=(e=void 0===e?{}:e).id,n=void 0===t?0:t;this.modifyPost(n,"publish")}},{key:"updatePost",value:function(){var e=this.props.post,t=(e=void 0===e?{}:e).id,n=void 0===t?0:t;this.modifyPost(n,"update")}},{key:"updateSelectedSections",value:function(e,t){var n=this.props,r=n.onUpdate,i=n.meta,o=k((i=void 0===i?{}:i).selectedSections),a=Array.isArray(o)?JSON.stringify([].concat(C(o),[t])):null,l=o.filter((function(e){return e!==t})),c=0<l.length?JSON.stringify(l):null;r("apple_news_sections",e?a:c)}},{key:"render",value:function(){var e=this,t="publish-to-apple-news",n=ne("Apple News Options","apple-news"),r=this.props,i=r.onUpdate,o=r.meta,a=(o=void 0===o?{}:o).isPaid,l=void 0!==a&&a,c=o.isPreview,s=void 0!==c&&c,u=o.isHidden,p=void 0!==u&&u,d=o.isSponsored,f=void 0!==d&&d,m=o.maturityRating,h=void 0===m?"":m,y=o.pullquoteText,v=void 0===y?"":y,g=o.pullquotePosition,b=void 0===g?"":g,w=o.selectedSections,_=void 0===w?"":w,E=o.coverImageId,S=void 0===E?0:E,R=o.coverImageCaption,T=void 0===R?"":R,O=o.apiId,P=void 0===O?"":O,x=o.dateCreated,C=void 0===x?"":x,N=o.dateModified,M=void 0===N?"":N,L=o.shareUrl,D=void 0===L?"":L,I=o.revision,j=void 0===I?"":I,H=r.postIsDirty,U=r.post,F=(U=void 0===U?{}:U).status,J=void 0===F?"":F,V=this.state,Y=V.autoAssignCategories,Q=V.loading,Z=V.publishState,te=V.sections,re=V.settings,ie=(re=void 0===re?{}:re).apiAutosync,oe=re.apiAutosyncDelete,ae=re.apiAutosyncUpdate,le=re.automaticAssignment,ce=V.selectedSectionsPrev,se=V.userCanPublish,ue=k(_);return React.createElement(ee,null,React.createElement($,{target:t},n),React.createElement(X,{name:t,title:ne("Publish to Apple News Options","apple-news")},React.createElement("div",{className:"components-panel__body is-opened",id:"apple-news-publish"},React.createElement("h3",null,ne("Sections","apple-news")),le&&[React.createElement(z,{label:ne("Assign sections by category","apple-news"),checked:Y,onChange:function(t){e.setState({autoAssignCategories:t}),t?(e.setState({selectedSectionsPrev:_||null}),i("apple_news_sections",null)):(i("apple_news_sections",ce),e.setState({selectedSectionsPrev:null}))}}),React.createElement("hr",null)],(!Y||!le)&&te&&0<te.length&&React.createElement(React.Fragment,null,React.createElement("h4",null,"Manual Section Selection"),Array.isArray(te)&&React.createElement("ul",{className:"apple-news-sections"},te.map((function(t){var n=t.id,r=t.name;return React.createElement("li",{key:n},React.createElement(z,{label:r,checked:-1!==ue.indexOf(n),onChange:function(t){return e.updateSelectedSections(t,n)}}))}))),React.createElement("p",null,React.createElement("em",null,ne("Select the sections in which to publish this article. If none are selected, it will be published to the default section.","apple-news")))),React.createElement("h3",null,ne("Paid Article","apple-news")),React.createElement(z,{label:ne("Check this to indicate that viewing the article requires a paid subscription. Note that Apple must approve your channel for paid content before using this feature.","apple-news"),onChange:function(e){return i("apple_news_is_paid",e)},checked:l}),React.createElement("h3",null,ne("Preview Article","apple-news")),React.createElement(z,{label:ne("Check this to publish the article as a draft.","apple-news"),onChange:function(e){return i("apple_news_is_preview",e)},checked:s}),React.createElement("h3",null,"Hidden Article"),React.createElement(z,{label:ne("Hidden articles are visible to users who have a link to the article, but do not appear in feeds.","apple-news"),onChange:function(e){return i("apple_news_is_hidden",e)},checked:p}),React.createElement("h3",null,"Sponsored Article"),React.createElement(z,{label:ne("Check this to indicate this article is sponsored content.","apple-news"),onChange:function(e){return i("apple_news_is_sponsored",e)},checked:f})),React.createElement(B,{initialOpen:!1,title:ne("Maturity Rating","apple-news")},React.createElement(W,{label:ne("Select Maturity Rating","apple-news"),value:h,options:[{label:"",value:""},{label:ne("Kids","apple-news"),value:"KIDS"},{label:ne("Mature","apple-news"),value:"MATURE"},{label:ne("General","apple-news"),value:"GENERAL"}],onChange:function(e){return i("apple_news_maturity_rating",e)}}),React.createElement("p",null,React.createElement("em",null,"Select the optional maturity rating for this post."))),React.createElement(B,{initialOpen:!1,title:ne("Pull Quote","apple_news")},React.createElement(K,{label:ne("Description","apple_news"),value:v,onChange:function(e){return i("apple_news_pullquote",e)},placeholder:"A pull quote is a key phrase, quotation, or excerpt that has been pulled from an article and used as a graphic element, serving to entice readers into the article or to highlight a key topic."}),React.createElement("p",null,React.createElement("em",null,"This is optional and can be left blank.")),React.createElement(W,{label:ne("Pull Quote Position","apple-news"),value:b||"middle",options:[{label:ne("top","apple-news"),value:"top"},{label:ne("middle","apple-news"),value:"middle"},{label:ne("bottom","apple-news"),value:"bottom"}],onChange:function(e){return i("apple_news_pullquote_position",e)}}),React.createElement("p",null,React.createElement("em",null,ne("The position in the article where the pull quote will appear.","apple-news")))),React.createElement(B,{initialOpen:!1,title:ne("Cover Image","apple_news")},React.createElement(A,{metaKey:"apple_news_coverimage",onUpdate:i,value:S}),React.createElement(K,{label:ne("Caption","apple_news"),value:T,onChange:function(e){return i("apple_news_coverimage_caption",e)},placeholder:"Add an image caption here."}),React.createElement("p",null,React.createElement("em",null,"This is optional and can be left blank."))),React.createElement(B,{initialOpen:!1,title:ne("Apple News Publish Information","apple-news")},""!==Z&&"N/A"!==Z&&React.createElement(ee,null,React.createElement("h4",null,ne("API Id","apple-news")),React.createElement("p",null,P),React.createElement("h4",null,ne("Created On","apple-news")),React.createElement("p",null,C),React.createElement("h4",null,ne("Last Updated On","apple-news")),React.createElement("p",null,M),React.createElement("h4",null,ne("Share URL","apple-news")),React.createElement("p",null,D),React.createElement("h4",null,ne("Revision","apple-news")),React.createElement("p",null,j),React.createElement("h4",null,ne("Publish State","apple-news")),React.createElement("p",null,Z))),"publish"===J&&se&&React.createElement(ee,null,Q?React.createElement(G,null):React.createElement(ee,null,""!==Z&&"N/A"!==Z?React.createElement(ee,null,!ae&&React.createElement(q,{isPrimary:!0,onClick:this.updatePost,style:{margin:"1em"}},ne("Update","apple-news")),!oe&&React.createElement(q,{isDefault:!0,onClick:this.deletePost,style:{margin:"1em"}},ne("Delete","apple-news"))):React.createElement(ee,null,H&&React.createElement("div",{className:"components-notice is-warning"},React.createElement("strong",null,ne("Please click the Update button above to ensure that all changes are saved before publishing to Apple News.","apple-news"))),!ie&&React.createElement(q,{isPrimary:!0,onClick:this.publishPost,style:{margin:"1em"}},ne("Publish","apple-news")))))))}}])&&N(n.prototype,r),i&&N(n,i),t}();re.propTypes={appleNewsNotices:p.a.arrayOf(p.a.shape({dismissed:p.a.bool,dismissible:p.a.bool,message:p.a.string,timestamp:p.a.number,type:p.a.string})).isRequired,displayNotification:p.a.func.isRequired,meta:p.a.shape({isPaid:p.a.bool,isPreview:p.a.bool,isHidden:p.a.bool,isSponsored:p.a.bool,maturityRating:p.a.string,pullquoteText:p.a.string,pullquotePosition:p.a.string,selectedSections:p.a.string,coverImageId:p.a.number,coverImageCaption:p.a.string,apiId:p.a.string,dateCreated:p.a.string,dateModified:p.a.string,shareUrl:p.a.string,revision:p.a.string}).isRequired,onUpdate:p.a.func.isRequired,post:p.a.shape({}).isRequired};var ie=U([Y((function(e){var t=e("core/editor"),n=t.isEditedPostDirty(),r=t&&t.getEditedPostAttribute&&t.getEditedPostAttribute("meta")||{},i=r.apple_news_is_paid,o=void 0!==i&&i,a=r.apple_news_is_preview,l=void 0!==a&&a,c=r.apple_news_is_hidden,s=void 0!==c&&c,u=r.apple_news_is_sponsored,p=void 0!==u&&u,d=r.apple_news_maturity_rating,f=void 0===d?"":d,m=r.apple_news_pullquote,h=void 0===m?"":m,y=r.apple_news_pullquote_position,v=void 0===y?"":y,g=r.apple_news_sections,b=void 0===g?"":g,w=r.apple_news_coverimage,_=void 0===w?0:w,E=r.apple_news_coverimage_caption,S=void 0===E?"":E,R=r.apple_news_api_id,A=void 0===R?"":R,T=r.apple_news_api_created_at,O=void 0===T?"":T,P=r.apple_news_api_modified_at,k=void 0===P?"":P,x=r.apple_news_api_share_url,C=void 0===x?"":x,N=r.apple_news_api_revision,M=void 0===N?"":N,L=t&&t.getEditedPostAttribute&&t.getEditedPostAttribute("apple_news_notices")||[];return{meta:{isPaid:o,isPreview:l,isHidden:s,isSponsored:p,maturityRating:f,pullquoteText:h,pullquotePosition:v,selectedSections:b,coverImageId:_,coverImageCaption:S,apiId:A,dateCreated:O,dateModified:k,shareUrl:C,revision:M,postId:t&&t.getCurrentPostId?t.getCurrentPostId():0},appleNewsNotices:L,postIsDirty:n,post:t&&t.getCurrentPost?t.getCurrentPost():{}}})),V((function(e){return{displayNotification:function(t){return P(e,t)},onUpdate:function(t,n){var r,i,o;e("core/editor").editPost({meta:(r={},i=t,o=n,i in r?Object.defineProperty(r,i,{value:o,enumerable:!0,configurable:!0,writable:!0}):r[i]=o,r)})}}}))])(re);if("undefined"!=typeof wp){var oe=wp.plugins,ae=(oe=void 0===oe?{}:oe).registerPlugin,le=void 0===ae?null:ae;"function"==typeof le&&le("publish-to-apple-news",{icon:React.createElement(s,null),render:ie})}}]);
//# sourceMappingURL=pluginSidebar.js.map