function TouchScaler(targetlist) {
	this.targetlist=[];
	for(i=0;i<targetlist.length;i++) {
		this.targetlist.push(this.getTarget(targetlist[i]));
	}
	this.minsize=6;
	this.maxsize=30;
	this.targetstyletype="font-size";
	this.targetstyle=null;
	this.currentvalue=10;
	this.linestart=0;
	this.linecurrent=0;
	this.linechange=0;
	this.newsize=0;
	this.multitouch=false;
	this.cookie=new CookieHandler("fontsize");
	this.setFromCookie();
	this.refreshStyle();
	this.cookie.setValue(this.currentvalue);
	this.cookie.setCookie();
	for(i=0;i<this.targetlist.length;i++) {
		this.targetlist[i].addEventListener("touchstart", this.startTouch.bind(this), false);
		this.targetlist[i].addEventListener("touchmove", this.moveTouch.bind(this), false);
		this.targetlist[i].addEventListener("touchend", this.endTouch.bind(this), false);
	}

}

	TouchScaler.prototype.getTarget=function(target) {
		if(target.indexOf('#')!==-1) {
			// id passed
			return document.getElementById(target.substr(1));
		} else {
			//tagname passed get first occurrence
			return document.getElementsByTagName(target)[0];
		}
	};

	TouchScaler.prototype.startTouch=function(e) {

		if(e.touches.length>1 && !this.multitouch) {
			this.multitouch=true;
			this.refreshStyle();
			var sx1=e.touches[0].clientX;
			var sy1=e.touches[0].clientY;
			var sx2=e.touches[1].clientX;
			var sy2=e.touches[1].clientY;
			this.linestart=Math.sqrt(Math.pow(Math.abs(sx1-sx2),2)+Math.pow(Math.abs(sy1-sy2),2));
			e.preventDefault();
		}
		
	};
	
	TouchScaler.prototype.moveTouch=function(e) {
		if(e.touches.length>1 && this.multitouch) {
			var cx1=e.changedTouches[0].clientX;
			var cy1=e.changedTouches[0].clientY;
			var cx2=e.changedTouches[1].clientX;
			var cy2=e.changedTouches[1].clientY;
			this.linecurrent=Math.sqrt(Math.pow(Math.abs(cx1-cx2),2)+Math.pow(Math.abs(cy1-cy2),2));
			this.linechange=(this.linecurrent-this.linestart)/10;
			
			this.newsize=this.currentvalue+this.linechange;
			if(this.newsize<this.minsize){this.newsize=this.minsize;}
			if(this.newsize>this.maxsize){this.newsize=this.maxsize;}

			document.documentElement.style.setProperty(this.targetstyletype,this.newsize+"px",null);
			e.preventDefault();
		}
		
	};
	
	TouchScaler.prototype.endTouch=function(e) {
		if(this.multitouch) {
			this.multitouch=false;
			this.currentvalue=this.newsize;
			if(this.currentvalue==0){this.currentvalue=this.minsize;}
			this.cookie.setValue(this.currentvalue);
			this.cookie.setCookie();
		}

	};

	
	TouchScaler.prototype.setFromCookie=function() {
		if(this.cookie.getValue()!==null) {
			document.documentElement.style.setProperty(this.targetstyletype,this.cookie.value+"px",null);
		}
	};
	
	TouchScaler.prototype.refreshStyle=function() {
		this.targetstyle=window.getComputedStyle(document.documentElement);
		this.currentvalue=parseFloat(this.targetstyle.getPropertyValue(this.targetstyletype));
	};
	
function CookieHandler(cookiename) {
	this.name=cookiename;
	this.days=7;
	this.value=this.getCookie();
	
};
	CookieHandler.prototype.getCookie=function() {
		return this.gc(this.name);
	};
	// Get cookie
	CookieHandler.prototype.gc = function(name) {
		if (document.cookie.length>0) {
			c_start=document.cookie.indexOf(name+"=");
			if (c_start!=-1) {
				c_start=c_start+name.length+1;
				c_end=document.cookie.indexOf(";",c_start);
				if (c_end==-1) { c_end=document.cookie.length; }
				return decodeURIComponent(document.cookie.substring(c_start,c_end));
			}
		}
		return null;
	};
	
	// Set Cookie
	CookieHandler.prototype.setCookie=function() {
		this.sc(this.value,this.name,this.days);
	};
	
	CookieHandler.prototype.sc= function(value, name, days) {
		var expires = "";
		if (days) {
			var thedate = new Date();
			thedate.setTime(thedate.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+thedate.toUTCString();
		}
		document.cookie = name+"="+encodeURIComponent(value)+((days==null) ? "" : expires+"; path=/");
	};
	
	CookieHandler.prototype.getValue=function() {return this.value;}
	CookieHandler.prototype.setValue=function(value){this.value=value;}
	