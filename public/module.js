Brickrouge.Widget.AdjustImage=new Class({Extends:Brickrouge.Widget.AdjustNode}),Brickrouge.Widget.AdjustThumbnail=new Class({Implements:[Events],initialize:function(a){this.element=a=document.id(a),this.control=a.getElement('input[type="hidden"]'),this.thumbnailOptions=a.getElement(".widget-adjust-thumbnail-options").get("widget"),this.image=a.getElement(".widget-adjust-image").get("widget"),this.thumbnailOptions.addEvent("change",this.onChange.bind(this)),this.image.addEvent("change",this.onChange.bind(this)),a.getFirst(".more").addEvent("click",function(){this.element.toggleClass("unfold-thumbnail"),this.fireEvent("adjust",{target:this.element,widget:this})}.bind(this))},decodeOptions:function(a){var b=a.match(/\/api\/images\/(\d+)(\/(\d*)x(\d*)(\/([a-z\-]+))?)?/),c=b[1],d=b[3]||null,e=b[4]||null,f=b[6],g=a.indexOf("?"),h={};return g&&(h=a.substring(g+1).parseQueryString()),c&&(h.nid=c),h.width=d,h.height=e,h.method=f,h},setValue:function(a){var b={nid:null},c=null;"element"==typeOf(a)&&(c=a,a=c.get("data-nid")||c.get("src")),"string"==typeOf(a)&&-1!==a.indexOf("/api/")?b=this.decodeOptions(a):"object"==typeOf(a)?b=a:b.nid=a,c&&(b.width=c.get("width")||b.width,b.height=c.get("height")||b.height),this.image.setValue(b.nid),this.thumbnailOptions.setValue(b)},getValue:function(){var a=this.image.getSelected(),b=this.thumbnailOptions.getValue(),c=null,d=null;if(a){c=a.get("data-path");try{d=new ICanBoogie.Modules.Thumbnailer.Thumbnail(c,b),c=d.toString()}catch(e){console&&console.log(e)}}return c},onChange:function(){var b=this.image.getSelected();this.fireEvent("change",{target:this,url:this.getValue(),nid:b?b.get("data-nid"):null,selected:b,options:this.thumbnailOptions.getValue()})}}),Brickrouge.Widget.PopImage=function(){return new Class({Extends:Brickrouge.Widget.PopNode,options:{thumbnailVersion:"$popimage"},initialize:function(a,b){this.parent(a,b);var c=this.element.getElement("img");c.addEvent("load",function(a){var b=a.target;b.set("width",b.naturalWidth),b.set("height",b.naturalHeight),this.popover&&this.popover.reposition()}.bind(this)),this.img=c},change:function(a){this.setValue(a.selected.get("data-nid"))},formatValue:function(a){var b=this.img;return a?(b.src=Request.API.encode("images/"+a+"/thumbnails/"+this.options.thumbnailVersion+"?_r="+Date.now()),b):""}})}(),Brickrouge.Widget.PopOrUploadImage=function(){var a="upload-mode",b="create",c="update",d=new Class({initialize:function(a){this.element=a=document.id(a);var d=a.getElement(".widget-pop-image").get("widget"),e=a.getElement(".widget-file").get("widget"),f=null;e.options.uploadMode==c&&(f=d.getValue()),e.addEvents({prepare:function(a){var b=a.data;b.append("title",a.file.name),b.append("is_online",!0),f&&b.append(ICanBoogie.Operation.KEY,f)},success:function(a){f=a.rc.key,d.setValue(a.rc.key)}})}});return d.UPLOAD_MODE=a,d.UPLOAD_MODE_CREATE=b,d.UPLOAD_MODE_UPDATE=c,d}();