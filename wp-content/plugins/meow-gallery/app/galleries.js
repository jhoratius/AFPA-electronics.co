!function(o){var t={};function i(e){if(t[e])return t[e].exports;var n=t[e]={i:e,l:!1,exports:{}};return o[e].call(n.exports,n,n.exports,i),n.l=!0,n.exports}i.m=o,i.c=t,i.d=function(o,t,e){i.o(o,t)||Object.defineProperty(o,t,{enumerable:!0,get:e})},i.r=function(o){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(o,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(o,"__esModule",{value:!0})},i.t=function(o,t){if(1&t&&(o=i(o)),8&t)return o;if(4&t&&"object"==typeof o&&o&&o.__esModule)return o;var e=Object.create(null);if(i.r(e),Object.defineProperty(e,"default",{enumerable:!0,value:o}),2&t&&"string"!=typeof o)for(var n in o)i.d(e,n,function(t){return o[t]}.bind(null,n));return e},i.n=function(o){var t=o&&o.__esModule?function(){return o.default}:function(){return o};return i.d(t,"a",t),t},i.o=function(o,t){return Object.prototype.hasOwnProperty.call(o,t)},i.p="",i(i.s=0)}([function(o,t,i){i(1)},function(o,t,i){"use strict";i.r(t);var e={o:{box:"a",orientation:"landscape"},i:{box:"a",orientation:"portrait"},oo:{box:"a",orientation:"landscape"},ii:{box:"a",orientation:"portrait"},oi:{box:"a",orientation:"landscape"},io:{box:"a",orientation:"portrait"},ooo:{box:"c",orientation:"landscape"},ioo:{box:"b",orientation:"landscape"},oio:{box:"a",orientation:"landscape"},ooi:{box:"a",orientation:"landscape"},oii:{box:"a",orientation:"landscape"},ioi:{box:"b",orientation:"landscape"},iio:{box:"c",orientation:"landscape"},iii:{box:"a",orientation:"portrait"},"oooo-v0":{box:"c",orientation:"landscape"},"oooo-v1":{box:"a",orientation:"landscape"},"oooo-v2":{box:"a",orientation:"landscape"},oioo:{box:"a",orientation:"landscape"},iooo:{box:"d",orientation:"landscape"},ooio:{box:"d",orientation:"landscape"},oooi:{box:"a",orientation:"landscape"},iiii:{box:"a",orientation:"portrait"},aoooo:{box:"a",orientation:"portrait"},ioooo:{box:"a",orientation:"portrait"},ooioo:{box:"c",orientation:"portrait"},ooooi:{box:"e",orientation:"portrait"},iiooo:{box:"a",orientation:"portrait"},iooio:{box:"a",orientation:"portrait"},ooiio:{box:"e",orientation:"landscape"},ooioi:{box:"c",orientation:"portrait"},oooii:{box:"d",orientation:"portrait"},oiioo:{box:"b",orientation:"portrait"},oiooi:{box:"b",orientation:"portrait"},iiioo:{box:"a",orientation:"portrait"},iiooi:{box:"a",orientation:"portrait"},iooii:{box:"a",orientation:"portrait"},ooiii:{box:"c",orientation:"portrait"}};function n(o){return function(o){if(Array.isArray(o))return s(o)}(o)||function(o){if("undefined"!=typeof Symbol&&Symbol.iterator in Object(o))return Array.from(o)}(o)||a(o)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function r(o,t){var i;if("undefined"==typeof Symbol||null==o[Symbol.iterator]){if(Array.isArray(o)||(i=a(o))||t&&o&&"number"==typeof o.length){i&&(o=i);var e=0,n=function(){};return{s:n,n:function(){return e>=o.length?{done:!0}:{done:!1,value:o[e++]}},e:function(o){throw o},f:n}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}var r,s=!0,l=!1;return{s:function(){i=o[Symbol.iterator]()},n:function(){var o=i.next();return s=o.done,o},e:function(o){l=!0,r=o},f:function(){try{s||null==i.return||i.return()}finally{if(l)throw r}}}}function a(o,t){if(o){if("string"==typeof o)return s(o,t);var i=Object.prototype.toString.call(o).slice(8,-1);return"Object"===i&&o.constructor&&(i=o.constructor.name),"Map"===i||"Set"===i?Array.from(o):"Arguments"===i||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(i)?s(o,t):void 0}}function s(o,t){(null==t||t>o.length)&&(t=o.length);for(var i=0,e=new Array(t);i<t;i++)e[i]=o[i];return e}function l(o,t){for(var i=0;i<t.length;i++){var e=t[i];e.enumerable=e.enumerable||!1,e.configurable=!0,"value"in e&&(e.writable=!0),Object.defineProperty(o,e.key,e)}}var u=jQuery,c=function(){function o(t){!function(o,t){if(!(o instanceof t))throw new TypeError("Cannot call a class as a function")}(this,o),this.gallery=t.gallery,this.$gallery=u(this.gallery),this.$galleryItems=this.$gallery.find(".mgl-item"),this.galleryItems=[],this.rowClasses=[],this.rows=[],this.ooooLayoutVariant=0,this.density="high",this.currentDevice="desktop",this.options={density:t.density}}var t,i,a;return t=o,(i=[{key:"getCurrentDevice",value:function(){var o=window.innerWidth;return o<=460?"mobile":o<=768?"tablet":"desktop"}},{key:"setDensity",value:function(){this.density=this.options.density[this.currentDevice]}},{key:"getAvailableRowClasses",value:function(){switch(this.density){case"high":this.rowClasses=["o","i","oo","ii","oi","io","ooo","oii","ooi","ioo","oio","ioi","iio","iii","iooo","oioo","ooio","oooi","iiii","oooo","ioooo","ooioo","ooooi","iiooo","iooio","ooiio","ooioi","oooii","oiioo","oiooi","iiioo","iiooi","iooii","ooiii"];break;case"medium":this.rowClasses=["o","i","oo","ii","oi","io","ooo","oii","ooi","ioo","oio","ioi","iio","iii"];break;case"low":this.rowClasses=["o","i","oo","ii","oi","io"]}}},{key:"createGalleryItemsArray",value:function(){var o,t=r(this.$galleryItems);try{for(t.s();!(o=t.n()).done;){var i=o.value,e=u(i),n=parseInt(e.attr("data-mgl-width")),a=parseInt(e.attr("data-mgl-height")),s=void 0;s=n>=a?"o":"i",this.galleryItems.push({id:parseInt(e.attr("data-mgl-id")),width:n,height:a,orientation:s,markup:e.prop("outerHTML")})}}catch(o){t.e(o)}finally{t.f()}}},{key:"calculateGalleryRows",value:function(){var o=n(this.galleryItems);this.rows=[];for(var t="";o.length>0;){var i=o.shift(),e=t+i.orientation;this.rowClasses.includes(e)?t=e:(this.rows.push(t),t=i.orientation),0===o.length&&this.rows.push(t)}this.avoidLoneLastItems()}},{key:"avoidLoneLastItems",value:function(){if(this.rows.length>=2){var o=this.rows[this.rows.length-1].split(""),t=this.rows[this.rows.length-2].split("");if(1===o.length&&t.length>2){var i=t.pop();o.push(i)}this.rows[this.rows.length-2]=t.join(""),this.rows[this.rows.length-1]=o.join("")}}},{key:"getLetterFromIndex",value:function(o){switch(o){case 0:return"a";case 1:return"b";case 2:return"c";case 3:return"d";case 4:return"e"}}},{key:"getRowLayout",value:function(o){var t="";return"oooo"===o?(3===this.ooooLayoutVariant&&(this.ooooLayoutVariant=0),0===this.ooooLayoutVariant?t+="".concat(o,"-v0"):t+="".concat(o,"-v").concat(this.ooooLayoutVariant),this.ooooLayoutVariant++):t+=o,t}},{key:"getRowClass",value:function(o){return"mgl-layout-".concat(o.length,"-").concat(this.getRowLayout(o))}},{key:"getRowMarkup",value:function(o,t){for(var i=this.getRowLayout(o),e='<div class="mgl-row mgl-layout-'.concat(o.length,"-").concat(i,'" data-row-layout="').concat(i,'">'),n=0,r=0;r<o.length;r++){var a='<div class="mgl-box '.concat(this.getLetterFromIndex(n),'">');a+=t.shift().markup,e+=a+="</div>",n++}return e+="</div>"}},{key:"writeMarkup",value:function(o){var t,i=n(this.galleryItems),e="",a=r(n(this.rows));try{for(a.s();!(t=a.n()).done;){var s=t.value,l=i.splice(0,s.length);e+=this.getRowMarkup(s,l)}}catch(o){a.e(o)}finally{a.f()}this.$gallery.html(e),o()}},{key:"getHeightByWidth",value:function(o,t,i){if("landscape"==i)switch(o){case"three-two":return 2*t/3;case"five-four":return 4*t/5}else switch(o){case"three-two":return 3*t/2;case"five-four":return 5*t/4}}},{key:"setRowsHeight",value:function(){var o=this;this.$gallery.find(".mgl-row").each((function(t,i){var n=u(i).attr("data-row-layout"),r=e[n],a=u(i).find(".mgl-box."+r.box);0===o.getHeightByWidth("three-two",a.outerWidth(),r.orientation)?setTimeout((function(){u(i).css("height",o.getHeightByWidth("three-two",a.outerWidth(),r.orientation))}),750):u(i).css("height",o.getHeightByWidth("three-two",a.outerWidth(),r.orientation))}))}},{key:"init",value:function(o){this.currentDevice=this.getCurrentDevice(),this.setDensity(),this.getAvailableRowClasses(),this.createGalleryItemsArray(),this.calculateGalleryRows(),this.writeMarkup(o)}},{key:"tilify",value:function(o){this.currentDevice!==this.getCurrentDevice()&&(this.currentDevice=this.getCurrentDevice(),this.ooooLayoutVariant=0,this.setDensity(),this.getAvailableRowClasses(),this.calculateGalleryRows(),this.writeMarkup(o))}}])&&l(t.prototype,i),a&&l(t,a),o}(),h=jQuery,y=function(){h(".mgl-tiles").each((function(){var o=new c({gallery:h(this)[0],density:mgl_settings.tiles.density});o.init((function(){o.setRowsHeight()}));var t=null;h(window).on("resize",(function(){clearTimeout(t),t=setTimeout((function(){o.tilify((function(){document.body.dispatchEvent(new Event("post-load"))})),o.setRowsHeight()}),500)}))}))};window.mglInitTiles=y,jQuery(document).ready((function(){y()}))}]);