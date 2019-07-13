var imageFileWords;var imageFile;var previewHolder;var preview;var previewText;var farbColor;var color;var cp;var colorPicker;var focus;var fx,fy;var dndFile;var submitButton;var cancelIntro;var defaultColor=new Color("#b0ccd9");window.addEventListener("load",function(){imageFileWords=document.getElementById("imageFile-words");imageFile=document.getElementById("imageFile");previewHolder=document.getElementById("previewHolder");preview=document.getElementById("preview");previewText=document.getElementById("previewText");color=document.getElementById("color");colorPicker=document.getElementById("colorPicker");focus=document.getElementById("focus");fx=document.getElementById("fx");fy=document.getElementById("fy");submitButton=document.getElementById("submitBtn");document.getElementById("upload").style.display="block";cp=new ColorPicker("#colorPickerD",updateColor,defaultColor.hex());for(var i=0;i<drops.length;i++){drops[i].addEventListener("mousemove",function(){cancelIntro=true})}window.addEventListener("keydown",function(e){e=e||window.event;var active=document.activeElement;if(!this.copy){this.copy=genEl("input");copy.style.opacity=0;copy.style.position="absolute";copy.style.left=copy.style.top="-9999px";copy.style.width=copy.style.height="1px";copy.style.padding=copy.style.border=copy.style.outline=0}var somethingSelected=function(){if(window.getSelection){return window.getSelection().toString()!=""}else{if(document.selection){return document.createRange().text!=""}else{return false}}};if(e.ctrlKey&&!(active instanceof HTMLInputElement||active instanceof HTMLTextAreaElement)&&!somethingSelected()){copy.addEventListener("keyup",function(e){var url=copy.value;if(url!=""){showURL();imageFileWords.value=url;imageFileWords.select()}try{copy.parentNode.removeChild(copy);copy.value=""}catch(e){}});copy.addEventListener("blur",function(){try{copy.parentNode.removeChild(copy);copy.value=""}catch(e){}});document.body.appendChild(copy);copy.select()}});initDragNDrop();initNavbar()});login=(function(orig){return function(){orig.apply(this,arguments);cancelIntro=true}})(login);function initDragNDrop(){function cancel(e){e.preventDefault();return false}var dropLoc=document.getElementsByClassName("section")[0];var lastEnter;childApply(dropLoc,function(el){el.addEventListener("dragenter",function(e){lastEnter=e.target;cancel(e);putClass(dropLoc,"droppable")})});childApply(dropLoc,function(el){el.addEventListener("dragover",function(e){if(window.FileReader){cancel(e)}putClass(dropLoc,"droppable")})});dropLoc.addEventListener("dragleave",function(e){if(lastEnter==e.target){cancel(e);remClass(dropLoc,"droppable")}});dropLoc.addEventListener("drop",function(event){if(window.FileReader){cancel(event);if(event.dataTransfer.files.length==1){loadFile(event.dataTransfer.files[0])}else{if(event.dataTransfer.files.length>1){alert("Only one image may be uploaded at a time")}else{var img=event.dataTransfer.getData("text/html");img=img.substring(img.indexOf('src="')+5);img=img.substring(0,img.indexOf('"'));showURL();imageFileWords.value=img;loadURL()}}}remClass(dropLoc,"droppable")});if(!window.FileReader){document.body.appendChild(genEl("style",{},{},'			.droppable input[type="file"], .droppable #imageFile-holder, .droppable #imageFile-holder .button{							visibility: visible;																									width: 100%;																											height: 100%;																											position: absolute;																										top: 0;																													left: 0;																											}																														.droppable #imageFile-holder .button{																						opacity: 0;																											}																													'))}}function initNavbar(){var navbarItems=document.getElementsByClassName("navbar-item");for(var i=0;i<navbarItems.length;i++){(function(i){setTimeout(function(){var item=navbarItems[i];if(i==2&&!cancelIntro){item.onclick()}putClass(item,"glow");setTimeout(function(){remClass(item,"glow")},1000)},1500*i)})(i)}}function showURL(){if(imageFileWords.hasAttribute("readonly")){imageFileWords.removeAttribute("readonly");imageFileWords.value=""}imageFileWords.style.width="";imageFileWords.style.visibility="visible";imageFileWords.style.opacity="1";imageFileWords.focus()}function loadURL(){if(!imageFileWords.hasAttribute("readonly")){preview.setAttribute("src","external-image.php?url="+imageFileWords.value);previewHolder.style.display="block";previewText.style.display="block"}}function previewLoad(img){if(previewHolder){remClass(previewHolder,"error");img.style.visibility="visible";submitButton.removeAttribute("disabled")}}function previewError(img){if(previewHolder){putClass(previewHolder,"error");cp.setColor(defaultColor);focus.style.left=focus.style.top="";dndFile=null;var tempImageFile=imageFile.cloneNode();imageFile.parentNode.insertBefore(tempImageFile,imageFile);imageFile.parentNode.removeChild(imageFile);imageFile=tempImageFile;submitButton.setAttribute("disabled","disabled");img.style.visibility="hidden"}}function uploadImage(inputFile){var b=document.body;var newInput;if(inputFile instanceof HTMLInputElement){newInput=inputFile.cloneNode(true)}var form=document.createElement("form");form.style.display="none";form.setAttribute("action","tempImg.php");form.setAttribute("method","post");form.setAttribute("enctype","multipart/form-data");form.setAttribute("encoding","multipart/form-data");form.setAttribute("target","uploadFileIframe");if(window.File&&inputFile instanceof File){var fd=new FormData(form);fd.append("imageFile",inputFile)}else{form.appendChild(newInput)}var iFrame=document.createElement("iframe");iFrame.id="uploadFileIframe";iFrame.name="uploadFileIframe";iFrame.style.display="none";var init=false;iFrame.onload=function(){var iFrameDoc=iFrame.contentDocument||iFrame.contentWindow.document;if(init){preview.src=iFrameDoc.getElementById("passedText").getAttribute("alt");b.removeChild(iFrame)}else{init=true;iFrameDoc.body.appendChild(form);swapElements(newInput,inputFile);form.submit();swapElements(newInput,inputFile)}};b.appendChild(iFrame)}function loadFile(upload){var dropLoc=document.getElementsByClassName("section")[0];remClass(dropLoc,"droppable");var backup=upload;if(window.File){if(upload instanceof File){dndFile=upload}else{upload=upload.files[0]}if(upload.type&&!upload.type.match(/image\/((jpg)|(jpeg)|(gif)|(png)|(bmp))/)){alert("Invalid filetype");return}}var name=upload.fileName||upload.name||upload.value;var ext=name.substring(name.lastIndexOf(".")+1).toLowerCase();switch(ext){case"jpg":case"jpeg":case"gif":case"png":case"bmp":break;default:alert("Invalid filetype");return}if(window.File&&backup instanceof File){clearForm(imageFile)}imageFileWords.setAttribute("readonly","readonly");imageFileWords.value=name.substring(name.lastIndexOf("\\")+1);imageFileWords.style.width="";imageFileWords.style.visibility="visible";imageFileWords.style.opacity="1";preview.src="/resources/images/loading.gif";if(window.FileReader){var fr=new FileReader;fr.onloadend=function(f){try{var checkRealUpload=function(){var ctx=genEl("canvas").getContext("2d");var w=parseInt(preview.width),h=parseInt(preview.height);ctx.drawImage(preview,0,0,w,h);var d=ctx.getImageData(0,0,w,h).data;var found;for(var i=0;i<d.length;i++){if(d[i]){found=1;break}}if(!found){uploadImage(backup)}preview.removeEventListener(checkRealUpload)};preview.addEventListener("load",checkRealUpload);preview.setAttribute("src",f.target.result)}catch(e){alert("failed: "+e)}};fr.readAsDataURL(upload)}else{if(window.File&&backup instanceof File){alert("Unfortunately, you're browser does not support drag and drop :(")}else{uploadImage(backup)}}previewText.style.display="block";previewHolder.style.display="block"}function updateColor(){var c=cp.getSelectedColor().hex();color.value=c;previewHolder.style.backgroundColor=c}var focusDown=false;function setFocus(e,setDown){if(e.button==2){var canvas=document.createElement("canvas");if(canvas.getContext){canvas.style.position="absolute";canvas.style.top=canvas.style.left="0";canvas.setAttribute("width",preview.width);canvas.setAttribute("height",preview.height);var ctx=canvas.getContext("2d");ctx.drawImage(preview,0,0,parseInt(preview.width),parseInt(preview.height));var d=ctx.getImageData(getRelativeX(e,preview),getRelativeY(e,preview),1,1).data;cp.setColor(new Color(d[0],d[1],d[2],d[3]));updateColor();e.stopPropagation()}}else{if(setDown===true){focusDown=true;this.longpressTimer=setTimeout(function(){e.button=2;setFocus(e)},700)}else{clearTimeout(this.longpressTimer)}if(setDown===false){focusDown=false}if((isMobile&&(setDown===undefined||setDown===false))||(!isMobile&&focusDown)){var width=parseInt(style(focus,"width")),height=parseInt(style(focus,"height"));focus.style.left=(preview.offsetLeft+getRelativeX(e,preview)-width/2)+"px";focus.style.top=(preview.offsetTop+getRelativeY(e,preview)-height/2)+"px";var step3=document.getElementById("step3");var step4=document.getElementById("step4");showSection(step3);showSection(step4)}}}function s(form){color.value=Color.hex(color.value);if(color.value==""){color.value="#fff"}if(focus.style.left==""){fx.value=0.5;fy.value=0.5}else{fx.value=(parseInt(focus.style.left)+6-preview.offsetLeft)/parseInt(style(preview,"width"));fy.value=(parseInt(focus.style.top)+6-preview.offsetTop)/parseInt(style(preview,"height"))}if(imageFileWords.hasAttribute("readonly")){imageFileWords.value=""}if(preview.width==0){alert("You forgot to upload an image!");return false}var submitBtn=document.getElementById("submitBtn");submitBtn.style.width=style(submitBtn,"width");submitBtn.style.height=style(submitBtn,"height");submitBtn.value="";submitBtn.style.background="url('/resources/images/loading.gif') center no-repeat";if(preview.width&&!imageFile.value&&imageFileWords.hasAttribute("readonly")&&dndFile&&window.FormData){var fd=new FormData();fd.append("color",color.value);fd.append("fx",fx.value);fd.append("fy",fy.value);fd.append("imageFileWords",imageFileWords.value);fd.append("imageFile",dndFile,dndFile.fileName||dndFile.name);fd.append("ajax","1");new AjaxRequest("upload.php","post").send(fd,function(ret){if(ret==="-1"){alert("ERROR: Potentially bad image file")}else{window.location=ret}});return false}return preview.width?true:false}function getColor(str){if(str.match(/^([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/)){str="#"+str}return str}function showSection(section){section.style.transition="none";section.style.top="0";section.style.position="relative";section.style.transition="";section.style.visibility="visible";section.style.opacity=1};