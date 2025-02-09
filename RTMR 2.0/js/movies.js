'use strict';

function MovieLoader(parentElementSelector) {
    this.lastWatchedDate = null;
    this.XHR = this.createXHR();
    this.parentElement = document.querySelector(parentElementSelector);

    if (!this.parentElement) {
        console.error("Parent element not found:", parentElementSelector);
        return;  // Exit the function if the parent element is not found
    }

    var checkInterval = setInterval(this.check.bind(this), 1000);
}



MovieLoader.prototype.getLastMovie = function() {
    const movies = document.querySelectorAll(".movie-item");
    return movies[movies.length - 1];  // Return the last <movie> element
};

MovieLoader.prototype.toBottom = function() {
    var d = document, b = d.body, e = d.documentElement;
    return Math.max(
        b.scrollHeight, e.scrollHeight,
        b.offsetHeight, e.offsetHeight,
        b.clientHeight, e.clientHeight
    ) - (window.innerHeight + window.scrollY);
};

MovieLoader.prototype.check = function() {
    if (this.toBottom() < 20 && !this.loading) {  // Only proceed if not already loading
        this.loading = true;  // Set the loading flag
        console.info("Reached the bottom, loading more movies...");

        var lastChild = this.getLastMovie();
        if (lastChild) {
            this.lastWatchedDate = lastChild.getAttribute("data-watched-date");
            console.log("Last Watched Date:", this.lastWatchedDate);
            this.loadMoreMovies(this.lastWatchedDate);
        }
    }
};

MovieLoader.prototype.createXHR = function() {
    if (window.XMLHttpRequest) {
        return new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        return new ActiveXObject("Microsoft.XMLHTTP");
    }
};

MovieLoader.prototype.loadMoreMovies = function(lastWatchedDate) {
    this.isLoading = true;  // Set loading flag
    console.log("Sending request to getMovie.php with lastWatchedDate:", lastWatchedDate);

    this.XHR.open("POST", "php/getMovie.php", true);
    this.XHR.onreadystatechange = this.appendMovies.bind(this);
    this.XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    this.XHR.send("lastWatchedDate=" + encodeURIComponent(lastWatchedDate));

    console.log("Request sent to getMovie.php!");
};

MovieLoader.prototype.appendMovies = function() {
    if (this.XHR.readyState === 4 && this.XHR.status === 200) {
        if (!this.parentElement) {
            console.error("Parent element is undefined.");
            return;
        }

        const responseHTML = this.XHR.responseText;
        if (responseHTML.trim() !== "") {
            this.parentElement.insertAdjacentHTML("beforeend", responseHTML);
        } else {
            console.info("No more movies to load.");
            document.querySelector(".movie-item").insertAdjacentHTML("beforeend", "<p>All movies loaded!</p>");
        }
    }
};


// Initialize MovieLoader
document.addEventListener('DOMContentLoaded', function() {
    new MovieLoader(".movie-item");  // Pass the class or ID of the container holding the movies
});

