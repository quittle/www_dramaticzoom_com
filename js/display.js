var main;var sound;var legacySound;var soundToggle;var soundEnabled=getCookie("soundEnabled")!="0";var mainCanvas,context;var volume=0.1;var screenWidth=window.innerWidth||document.documentElement.clientWidth;var screenHeight=window.innerHeight||document.documentElement.clientHeight;window.addEventListener("load",function(){main=document.getElementById("main");sound=document.getElementById("sound");legacySound=document.getElementById("legacySound");soundToggle=document.getElementById("soundToggle");document.getElementById("overlay").style.opacity="";document.getElementById("overlay-header").style.opacity="";fitInput();setSound();var hidden=getVisibilityHidden();if(hidden){var focused=true;addOnVisibililtyChange(function(event){if(document[hidden]){focused=false;unzoom()}else{if(!focused&&main){focused=true;soundEnabled=getCookie("soundEnabled")!="0";setSound();unzoom();zoom()}}})}else{window.onfocus=function(){if(!focused&&main){focused=true;soundEnabled=getCookie("soundEnabled")!="0";setSound();unzoom();zoom()}};window.onblur=function(e){focused=false;unzoom()}}});window.addEventListener("resize",function(){screenWidth=window.innerWidth||document.documentElement.clientWidth;screenHeight=window.innerHeight||document.documentElement.clientHeight;mainCanvas.setAttribute("width",screenWidth);mainCanvas.setAttribute("height",screenHeight);var width=parseInt(style(main,"width"));var height=parseInt(style(main,"height"));var tempWidth=width*4,tempHeight=height*4;context.clearRect(0,0,mainCanvas.width,mainCanvas.height);context.drawImage(main,(screenWidth-tempWidth)/2+((0.5-storeX)*tempWidth),(screenHeight-tempHeight)/2+((0.5-storeY)*tempHeight),tempWidth,tempHeight)});login=(function(orig){return function(){orig.apply(login,arguments);var q=window.location.pathname.split("/");q=q[q.length-1];var ar=new AjaxRequest("/php/manage-zooms.php","get","application/x-www-form-urlencoded",true,true);ar.send({method:"claim",id:q},function(ret){if(ret=="1"){var op=document.getElementById("overlay-prompt");op.parentNode.removeChild(op)}})}})(login);function fitInput(){var overlayText=document.getElementById("overlay-text");var text=overlayText.value;var temp=genEl("span",{},{display:"inline-block",position:"absolute",top:"-9999px",left:"-9999px",width:"auto !important","font-weight":style(overlayText,"font-weight")+" !important","font-family":style(overlayText,"font-family")+" !important","font-size":style(overlayText,"font-size")+" !important","font-style":style(overlayText,"font-style")+" !important","font-variant":style(overlayText,"font-variant")+" !important","letter-spacing":style(overlayText,"letter-spacing")+" !important",kerning:style(overlayText,"kerning")+" !important",font:style(overlayText,"font")+" !important"},text);document.body.appendChild(temp);overlayText.style.width=style(temp,"width");document.body.removeChild(temp)}function hideOverlay(){var oh=document.getElementById("overlay-header");oh.style.opacity=0;oh.addEventListener("mouseover",function(){this.style.opacity=""})}function unhideOverlay(){var oh=document.getElementById("overlay-header");oh.style.opacity=1;oh.addEventListener("mouseover",function(){this.style.opacity=""})}var storeX,storeY,timer;function unzoom(){if(sound.pause){sound.pause()}else{var lsh=document.getElementById("legacySoundHolder");var copy=legacySound.outerHTML.replace('autostart="true"','autostart="false"');legacySound.parentNode.removeChild(legacySound);lsh.innerHTML=copy;legacySound=document.getElementById("legacySound")}clearTimeout(timer);if(context){context.clearRect(0,0,mainCanvas.width,mainCanvas.height)}else{main.className="";main.style.maxWidth="600px";main.style.maxHeight="600px";main.style.position="";main.style.height="";main.style.width="";main.style.left="";main.style.top=""}}function zoom(x,y){if(!x){x=storeX}else{storeX=x}if(!y){y=storeY}else{storeY=y}if(main==null){main=document.getElementById("main");mainCanvas=document.getElementById("main-canvas");if(main.src.substring(main.src.lastIndexOf(".")+1)!="gif"){context=mainCanvas.getContext?mainCanvas.getContext("2d"):null}}if(sound==null){sound=document.getElementById("sound")}var width=parseInt(style(main,"width"));var height=parseInt(style(main,"height"));var left=absoluteLeft(main);var top=absoluteTop(main);mainCanvas.setAttribute("width",screenWidth);mainCanvas.setAttribute("height",screenHeight);if(context){context.drawImage(main,left,top,width,height);main.style.visibility="hidden"}else{main.style.width=width+"px";main.style.height=height+"px";main.style.maxWidth="inherit";main.style.maxHeight="inherit";main.style.left=absoluteLeft(main)+"px";main.style.top=absoluteTop(main)+"px";main.style.position="absolute";main.className="animated"}var zoom=6;var time=4700;var fps=50;sound.volume=soundEnabled?volume:0;var iterations=Math.ceil(fps/1000*time);if(sound.readyState==4){sound.pause()}timer=setTimeout(function(){if(sound.currentTime){sound.currentTime=0}if(sound.play){sound.play()}else{if(soundEnabled){var lsh=document.getElementById("legacySoundHolder");var copy=legacySound.outerHTML.replace('autostart="false"','autostart="true"');legacySound.parentNode.removeChild(legacySound);lsh.innerHTML=copy;legacySound=document.getElementById("legacySound")}}var i=0;var maxLeft=screenWidth/2-(width*(1+iterations/zoom)*x);var maxTop=screenHeight/2-(height*(1+iterations/zoom)*y);var dx=(maxLeft-parseInt(main.style.left))/iterations;var dy=(maxTop-parseInt(main.style.top))/iterations;timer=setInterval(function(){i++;var percentThrough=i/iterations;if(context){context.clearRect(0,0,mainCanvas.width,mainCanvas.height);var tempWidth=width*(1+3*percentThrough),tempHeight=height*(1+3*percentThrough);context.drawImage(main,(screenWidth-tempWidth)/2+((0.5-x)*tempWidth)*percentThrough,(screenHeight-tempHeight)/2+((0.5-y)*tempHeight)*percentThrough,tempWidth,tempHeight)}else{main.style.width=width*(1+3*percentThrough)+"px";main.style.height=height*(1+3*percentThrough)+"px";main.style.left=(screenWidth-parseInt(main.style.width))/2+((0.5-x)*parseInt(main.style.width))*percentThrough+"px";main.style.top=(screenHeight-parseInt(main.style.height))/2+((0.5-y)*parseInt(main.style.height))*percentThrough+"px"}if(i==iterations){window.clearTimeout(timer)}},time/iterations)},2000)}function selectText(el){el.focus()}function setSound(){soundToggle.innerHTML=(soundEnabled?"Disable":"Enable")+" Sound";putClass(soundToggle,soundEnabled?"enabled":"disabled");remClass(soundToggle,soundEnabled?"disabled":"enabled");if(!soundEnabled){if(sound.volume){sound.volume=0}else{var lsh=document.getElementById("legacySoundHolder");var copy=legacySound.outerHTML.replace('autostart="true"','autostart="false"');legacySound.parentNode.removeChild(legacySound);lsh.innerHTML=copy;legacySound=document.getElementById("legacySound")}}else{sound.volume=volume}}function toggleSound(){soundEnabled=!soundEnabled;setSound();putCookie("soundEnabled",soundEnabled?"1":"0")};