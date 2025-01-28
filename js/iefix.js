// Create listed HTML5 elements in e for IE7 and 8
var e = ("main,section,article,aside,figure,footer,header,hgroup,nav,time,figcaption,summary").split(',');
for (var i = 0; i < e.length; i++){
	document.createElement(e[i]);
}