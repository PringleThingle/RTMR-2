'use strict';

function Article(parentElement) {
    this.blogpostdate="";
    this.XHR=this.createXHR();
	this.parentElement=this.getTarget(parentElement);		
    var checkinterval = setInterval(this.check.bind(this),1000);
};

	Article.prototype.getTarget=function(target) {
		if(target.indexOf('#')!==-1) {
			// id passed
			return document.getElementById(target.substr(1));
		} else {
			//tagname passed get first occurrence
			return document.getElementsByTagName(target)[0];
		}
	};

	//Cross platform support for the total height of the document
	Article.prototype.getDocHeight=function() {
		var d=document, b = d.body, e=d.documentElement;
		return Math.max(
		b.scrollHeight, e.scrollHeight,	b.offsetHeight, e.offsetHeight,	b.clientHeight, e.clientHeight
		);	
	};

	// Cross platform support for the inner height of the client window
	Article.prototype.getWinHeight=function() {
		var w=window, d=document, e=d.documentElement,g=d.getElementsByTagName('body')[0],
		y = w.innerHeight || e.clientHeight || g.clientHeight;
		return y;
	};

	// Cross platform support to get the Y coordinate of the top of the visible part of the page
	Article.prototype.getScrollPosition=function() {
		var w=window, d=document, e=d.documentElement;
		var scrollposition = (w.scrollY || e.scrollTop)  - (e.clientTop || 0);
		return scrollposition;
	}
	
	Article.prototype.toBottom=function() {
		return this.getDocHeight()-(this.getWinHeight()+this.getScrollPosition());
	};

    Article.prototype.check=function() {
		if(this.toBottom()<20 && typeof JSON==="object") {
			console.info("hit");
			var lastchild=this.parentElement.children.length-1;
			var timeelements=this.parentElement.children[lastchild].getElementsByTagName("time");
			this.blogpostdate=timeelements[0].getAttribute("datetime");
			console.log(this.blogpostdate);
            this.getArticle();
		}
	}

	/***************************
	* Create XHR - supported from IE9
	* ActiveXObject included as example
	***************************/
	Article.prototype.createXHR=function() {
		if (window.XMLHttpRequest) {
			return new XMLHttpRequest();
		}
		else if(window.ActiveXObject) {
			return new ActiveXObject("Microsoft.XMLHTTP");
		}
	};

	Article.prototype.getArticle=function() {
		this.XHR.open("POST","php/getArticle.php",true);
		this.XHR.onreadystatechange = this.makeArticle.bind(this);
		// Send request
		this.XHR.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		this.XHR.send("blogpostdate="+encodeURIComponent(this.blogpostdate));
	};

	Article.prototype.makeArticle = function () {
		if (this.XHR.readyState === 4 && this.XHR.status === 200) {
			// Fetch the pre-rendered HTML from PHP
			const responseHTML = this.XHR.responseText;
	
			// Insert the HTML into the parent container
			this.parentElement.insertAdjacentHTML("beforeend", responseHTML);
		}
	};

	function loadArticles(blogpostdate) {
		const xhr = new XMLHttpRequest();
		xhr.open("POST", "getArticle.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.onreadystatechange = function () {
			if (xhr.readyState === 4 && xhr.status === 200) {
				const parentElement = document.getElementById("articles-container");
				parentElement.insertAdjacentHTML("beforeend", xhr.responseText);
			}
		};
		xhr.send(`blogpostdate=${encodeURIComponent(blogpostdate)}`);
	}
