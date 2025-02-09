'use strict';

function MovieLoader() {
    this.lastWatchedDate = null;  // Track the last watched date
    this.loadedMovieIDs = new Set();  // Keep track of loaded movie IDs
    this.XHR = this.createXHR();
    setInterval(this.check.bind(this), 1000);  // Check every second
}

MovieLoader.prototype.getLastMovie = function() {
    const movies = document.querySelectorAll("movie[data-movie-id]");
    return movies[movies.length - 1] || null;  // Return the last movie element
};

MovieLoader.prototype.toBottom = function() {
    const docHeight = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
    return docHeight - (window.innerHeight + window.scrollY);
};

MovieLoader.prototype.check = function() {
    if (this.toBottom() < 20) {
        console.info("Reached the bottom, loading more movies...");
        
        const lastMovie = this.getLastMovie();
        if (lastMovie) {
            const movieID = lastMovie.getAttribute("data-movie-id");
            const watchedDate = lastMovie.getAttribute("data-watched-date");
            
            if (this.loadedMovieIDs.has(movieID)) {
                console.warn("Movie already loaded or missing data.");
                return;
            }

            this.lastWatchedDate = watchedDate;
            console.log("Loading more movies with watchedDate:", this.lastWatchedDate);
            this.loadMoreMovies(this.lastWatchedDate);
        }
    }
};

MovieLoader.prototype.createXHR = function() {
    if (window.XMLHttpRequest) return new XMLHttpRequest();
    return new ActiveXObject("Microsoft.XMLHTTP");
};

MovieLoader.prototype.loadMoreMovies = function(lastWatchedDate) {
    console.log("Requesting more movies with watchedDate:", lastWatchedDate);

    this.XHR.open("POST", "php/getMovie.php", true);
    this.XHR.onreadystatechange = this.appendMovies.bind(this);
    this.XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    this.XHR.send("lastWatchedDate=" + encodeURIComponent(lastWatchedDate));
};

MovieLoader.prototype.appendMovies = function() {
    if (this.XHR.readyState === 4 && this.XHR.status === 200) {
        console.log("Response received:", this.XHR.responseText);
        if (this.XHR.responseText.trim() !== "") {
            const newMovies = document.createElement('div');
            newMovies.innerHTML = this.XHR.responseText;

            // Add each new movie while tracking its ID
            newMovies.querySelectorAll("movie[data-movie-id]").forEach(movie => {
                const movieID = movie.getAttribute("data-movie-id");
                if (!this.loadedMovieIDs.has(movieID)) {
                    document.body.appendChild(movie);  // Append the new movie to the body
                    this.loadedMovieIDs.add(movieID);
                }
            });
        } else {
            console.info("No more movies to load.");
        }
    }
};

// Initialize MovieLoader
document.addEventListener('DOMContentLoaded', () => new MovieLoader());
