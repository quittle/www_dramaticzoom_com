var Sha256={};Sha256.hash=function(msg,utf8encode){utf8encode=(typeof utf8encode=="undefined")?true:utf8encode;if(utf8encode){msg=Utf8.encode(msg)}var K=[1116352408,1899447441,3049323471,3921009573,961987163,1508970993,2453635748,2870763221,3624381080,310598401,607225278,1426881987,1925078388,2162078206,2614888103,3248222580,3835390401,4022224774,264347078,604807628,770255983,1249150122,1555081692,1996064986,2554220882,2821834349,2952996808,3210313671,3336571891,3584528711,113926993,338241895,666307205,773529912,1294757372,1396182291,1695183700,1986661051,2177026350,2456956037,2730485921,2820302411,3259730800,3345764771,3516065817,3600352804,4094571909,275423344,430227734,506948616,659060556,883997877,958139571,1322822218,1537002063,1747873779,1955562222,2024104815,2227730452,2361852424,2428436474,2756734187,3204031479,3329325298];var H=[1779033703,3144134277,1013904242,2773480762,1359893119,2600822924,528734635,1541459225];msg+=String.fromCharCode(128);var l=msg.length/4+2;var N=Math.ceil(l/16);var M=new Array(N);for(var i=0;i<N;i++){M[i]=new Array(16);for(var j=0;j<16;j++){M[i][j]=(msg.charCodeAt(i*64+j*4)<<24)|(msg.charCodeAt(i*64+j*4+1)<<16)|(msg.charCodeAt(i*64+j*4+2)<<8)|(msg.charCodeAt(i*64+j*4+3))}}M[N-1][14]=((msg.length-1)*8)/Math.pow(2,32);M[N-1][14]=Math.floor(M[N-1][14]);M[N-1][15]=((msg.length-1)*8)&4294967295;var W=new Array(64);var a,b,c,d,e,f,g,h;for(var i=0;i<N;i++){for(var t=0;t<16;t++){W[t]=M[i][t]}for(var t=16;t<64;t++){W[t]=(Sha256.sigma1(W[t-2])+W[t-7]+Sha256.sigma0(W[t-15])+W[t-16])&4294967295}a=H[0];b=H[1];c=H[2];d=H[3];e=H[4];f=H[5];g=H[6];h=H[7];for(var t=0;t<64;t++){var T1=h+Sha256.Sigma1(e)+Sha256.Ch(e,f,g)+K[t]+W[t];var T2=Sha256.Sigma0(a)+Sha256.Maj(a,b,c);h=g;g=f;f=e;e=(d+T1)&4294967295;d=c;c=b;b=a;a=(T1+T2)&4294967295}H[0]=(H[0]+a)&4294967295;H[1]=(H[1]+b)&4294967295;H[2]=(H[2]+c)&4294967295;H[3]=(H[3]+d)&4294967295;H[4]=(H[4]+e)&4294967295;H[5]=(H[5]+f)&4294967295;H[6]=(H[6]+g)&4294967295;H[7]=(H[7]+h)&4294967295}return Sha256.toHexStr(H[0])+Sha256.toHexStr(H[1])+Sha256.toHexStr(H[2])+Sha256.toHexStr(H[3])+Sha256.toHexStr(H[4])+Sha256.toHexStr(H[5])+Sha256.toHexStr(H[6])+Sha256.toHexStr(H[7])};Sha256.ROTR=function(n,x){return(x>>>n)|(x<<(32-n))};Sha256.Sigma0=function(x){return Sha256.ROTR(2,x)^Sha256.ROTR(13,x)^Sha256.ROTR(22,x)};Sha256.Sigma1=function(x){return Sha256.ROTR(6,x)^Sha256.ROTR(11,x)^Sha256.ROTR(25,x)};Sha256.sigma0=function(x){return Sha256.ROTR(7,x)^Sha256.ROTR(18,x)^(x>>>3)};Sha256.sigma1=function(x){return Sha256.ROTR(17,x)^Sha256.ROTR(19,x)^(x>>>10)};Sha256.Ch=function(x,y,z){return(x&y)^(~x&z)};Sha256.Maj=function(x,y,z){return(x&y)^(x&z)^(y&z)};Sha256.toHexStr=function(n){var s="",v;for(var i=7;i>=0;i--){v=(n>>>(i*4))&15;s+=v.toString(16)}return s};var Utf8={};Utf8.encode=function(strUni){var strUtf=strUni.replace(/[\u0080-\u07ff]/g,function(c){var cc=c.charCodeAt(0);return String.fromCharCode(192|cc>>6,128|cc&63)});strUtf=strUtf.replace(/[\u0800-\uffff]/g,function(c){var cc=c.charCodeAt(0);return String.fromCharCode(224|cc>>12,128|cc>>6&63,128|cc&63)});return strUtf};Utf8.decode=function(strUtf){var strUni=strUtf.replace(/[\u00e0-\u00ef][\u0080-\u00bf][\u0080-\u00bf]/g,function(c){var cc=((c.charCodeAt(0)&15)<<12)|((c.charCodeAt(1)&63)<<6)|(c.charCodeAt(2)&63);return String.fromCharCode(cc)});strUni=strUni.replace(/[\u00c0-\u00df][\u0080-\u00bf]/g,function(c){var cc=(c.charCodeAt(0)&31)<<6|c.charCodeAt(1)&63;return String.fromCharCode(cc)});return strUni};