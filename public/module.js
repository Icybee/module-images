define("icybee/images/save-operation",[],function(){return new Class({options:{constructor:"files",url:""},Implements:[Options,Events],initialize:function(a){this.setOptions(a)},process:function(a){var b=new XMLHttpRequest,c=this.createFormData(a);b.onreadystatechange=this.onReadyStateChange.bind(this),b.upload.onprogress=this.onProgress.bind(this),b.upload.onload=this.onProgress.bind(this),b.open("POST",this.options.url),b.setRequestHeader("Accept","application/json"),b.setRequestHeader("X-Requested-With","XMLHttpRequest"),b.setRequestHeader("X-Request","JSON"),this.request(b),b.send(c)},createFormData:function(a){var b=new FormData;return b.append(ICanBoogie.Operation.DESTINATION,this.options.constructor),b.append(ICanBoogie.Operation.NAME,"save"),Object.each(a,function(a,c){b.append(c,a)}),b},onReadyStateChange:function(a){var b=a.target;if(b.readyState==XMLHttpRequest.DONE){if(200==b.status)try{this.success(JSON.decode(b.responseText),b)}catch(c){console&&console.error(c),this.failure(b)}else b.status>=400&&this.failure(b);this.complete(b)}},onProgress:function(a){this.fireEvent("progress",a)},request:function(a){this.fireEvent("request",a)},success:function(a,b){this.fireEvent("success",a,b)},failure:function(a){this.fireEvent("failure",a)},complete:function(a){this.fireEvent("complete",a)}})}),define("icybee/images/image-control",["icybee/images/save-operation"],function(a){return new Class({options:{maxFileSize:0,maxFileSizeAlert:"The selected file is too big.",acceptedExtensions:null,acceptedExtensionsAlert:"Wrong file type."},Implements:[Options,Events],initialize:function(a,b){this.element=a=document.id(a),this.fileElement=a.getElement('[type="file"]'),this.nidElement=a.getElement('[type="hidden"]'),this.progressPositionElement=a.getElement(".progress-position"),this.alertContentElement=a.getElement(".alert .content"),this.defaultAlertMessage=this.alertContentElement.innerHTML,this.setOptions(b),"string"==typeOf(this.options.acceptedExtensions)&&(this.options.acceptedExtensions=this.options.acceptedExtensions.split(" ")),this.fileElement.addEvent("change",function(){this.start()}.bind(this)),a.addEventListener("dragover",this.onDragover.bind(this),!1),a.addEventListener("dragleave",this.onDragleave.bind(this),!1),a.addEventListener("drop",this.onDrop.bind(this),!1)},onDragover:function(a){a.preventDefault(),this.element.addClass("dnd-hover")},onDragleave:function(a){a.preventDefault(),this.element.removeClass("dnd-hover")},onDrop:function(a){a.stopPropagation(),a.preventDefault(),this.element.removeClass("dnd-hover");var b=a.target.files||a.dataTransfer.files;this.upload(b[0])},setPosition:function(a){this.progressPositionElement.setStyle("width",100*a+"%"),this.element.getElement(".progress-label").innerHTML=Math.round(100*a)+"%"},alert:function(a){this.alertContentElement.innerHTML=a,this.element.addClass("has-error")},validate:function(a){var b=this.options.acceptedExtensions,c=this.options.maxFileSize;return b&&(b=b.join("|").replace(/\./g,""),b=new RegExp(".("+b+")$"),!b.test(a.name))?(this.alert(this.options.acceptedExtensionsAlert),!1):a.size>c?(this.alert(this.options.maxFileSizeAlert),!1):!0},start:function(){var a=this.fileElement.files;this.upload(a[0])},upload:function(b){if(this.validate(b)){var c=new a({constructor:"images",onRequest:this.request.bind(this),onSuccess:this.success.bind(this),onFailure:this.failure.bind(this),onComplete:this.complete.bind(this),onProgress:this.progress.bind(this)}),d=this.nidElement.get("value"),e={path:b,is_online:!0};d&&(e[ICanBoogie.Operation.KEY]=d),c.process(e)}},request:function(){this.setPosition(0),this.element.removeClass("has-error"),this.element.addClass("uploading"),this.fireEvent("request",arguments)},success:function(a){this.setValue(a.rc.key),this.fireEvent("success",arguments)},failure:function(a){var b=this.defaultAlertMessage;try{response=JSON.decode(a.responseText),response.errors.path?b=response.errors.path:response.exception&&(b=response.errors.path)}catch(c){}this.alert(b),this.fireEvent("failure",arguments)},complete:function(){this.element.removeClass("uploading"),this.fireEvent("complete",arguments)},progress:function(a){if(a.lengthComputable){var b=a.loaded/a.total;this.setPosition(b)}this.fireEvent("progress",arguments)},setValue:function(a){this.nidElement.value=a,this.fireEvent("change",a)},getValue:function(){return this.nidElement.value}})}),define(["icybee/images/image-control"],function(a){Brickrouge.Widget.ImageControl=a}),define("icybee/images/image-control-with-preview",["icybee/images/image-control"],function(){return new Class({options:{thumbnailVersion:"$popimage"},initialize:function(a){var c;this.element=a=document.id(a),this.imgElement=a.getElement("img"),this.control=c=a.getElement(Brickrouge.WIDGET_SELECTOR).get("widget"),this.control.addEvent("change",this.rethinkState.bind(this)),a.addEventListener("dragover",function(a){c.onDragover(a)},!1),a.addEventListener("dragleave",function(a){c.onDragleave(a)},!1),a.addEventListener("drop",function(a){c.onDrop(a)},!1),a.addEvent('click:relay([data-dismiss="value"])',function(){this.setValue(null)}.bind(this)),this.rethinkState()},rethinkState:function(){var a=this.getValue(),b=a?"/api/images/"+a+"/thumbnails/"+this.options.thumbnailVersion+"?no-cache="+(new Date).getTime().toString(16):"";this.imgElement.src=b,this.element[b?"removeClass":"addClass"]("empty")},setValue:function(a){this.control.setValue(a),this.rethinkState()},getValue:function(){return this.control.getValue()}})}),define(["icybee/images/image-control-with-preview"],function(a){Brickrouge.Widget.ImageControlWithPreview=a}),define("icybee/images/adjust-image",["icybee/nodes/adjust-node"],function(a){return new Class({Extends:a})}),define(["icybee/images/adjust-image"],function(a){Brickrouge.Widget.AdjustImage=a}),Brickrouge.Widget.AdjustThumbnail=new Class({Implements:[Events],initialize:function(a){this.element=a=document.id(a),this.control=a.getElement('input[type="hidden"]'),this.thumbnailOptions=a.getElement(".widget-adjust-thumbnail-options").get("widget"),this.image=a.getElement(".widget-adjust-image").get("widget"),this.thumbnailOptions.addEvent("change",this.onChange.bind(this)),this.image.addEvent("change",this.onChange.bind(this)),a.getFirst(".more").addEvent("click",function(){this.element.toggleClass("unfold-thumbnail"),this.fireEvent("adjust",{target:this.element,widget:this})}.bind(this))},decodeOptions:function(a){var b=a.match(/\/api\/images\/(\d+)(\/(\d*)x(\d*)(\/([a-z\-]+))?)?/),c=b[1],d=b[3]||null,e=b[4]||null,f=b[6],g=a.indexOf("?"),h={};return g&&(h=a.substring(g+1).parseQueryString()),c&&(h.nid=c),h.width=d,h.height=e,h.method=f,h},setValue:function(a){var b={nid:null},c=null;"element"==typeOf(a)&&(c=a,a=c.get("data-nid")||c.get("src")),"string"==typeOf(a)&&-1!==a.indexOf("/api/")?b=this.decodeOptions(a):"object"==typeOf(a)?b=a:b.nid=a,c&&(b.width=c.get("width")||b.width,b.height=c.get("height")||b.height),this.image.setValue(b.nid),this.thumbnailOptions.setValue(b)},getValue:function(){var a=this.image.getSelected(),b=this.thumbnailOptions.getValue(),c=null,d=null;if(a){c=a.get("data-path");try{d=new ICanBoogie.Modules.Thumbnailer.Thumbnail(c,b),c=d.toString()}catch(e){console&&console.log(e)}}return c},onChange:function(){var b=this.image.getSelected();this.fireEvent("change",{target:this,url:this.getValue(),nid:b?b.get("data-nid"):null,selected:b,options:this.thumbnailOptions.getValue()})}}),define("icybee/images/pop-image",["icybee/nodes/pop-node"],function(a){return new Class({Extends:a,options:{thumbnailVersion:"$popimage"},initialize:function(a,b){this.parent(a,b);var c=this.element.getElement("img");c.addEvent("load",function(a){var b=a.target;b.set("width",b.naturalWidth),b.set("height",b.naturalHeight),this.popover&&this.popover.reposition()}.bind(this)),this.img=c},change:function(a){this.setValue(a.selected.get("data-nid"))},formatValue:function(a){var b=this.img;return a?(b.src=Request.API.encode("images/"+a+"/thumbnails/"+this.options.thumbnailVersion+"?_r="+Date.now()),b):""}})}),define(["icybee/images/pop-image"],function(a){Brickrouge.Widget.PopImage=a}),Brickrouge.Widget.PopOrUploadImage=function(){var a="upload-mode",b="create",c="update",d=new Class({initialize:function(a){this.element=a=document.id(a);var d=a.getElement(".widget-pop-image").get("widget"),e=a.getElement(".widget-file").get("widget"),f=null;e.options.uploadMode==c&&(f=d.getValue()),e.addEvents({prepare:function(a){var b=a.data;b.append("title",a.file.name),b.append("is_online",!0),f&&b.append(ICanBoogie.Operation.KEY,f)},success:function(a){f=a.rc.key,d.setValue(a.rc.key)}})}});return d.UPLOAD_MODE=a,d.UPLOAD_MODE_CREATE=b,d.UPLOAD_MODE_UPDATE=c,d}();