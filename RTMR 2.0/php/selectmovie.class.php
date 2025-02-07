<?php
class SelectMovie {
    private $id;
    private $title;
    private $releaseDate;
    private $description;
    private $posterPath;
    private $rating;

    public function __construct($id, $title, $releaseDate, $description, $posterPath, $rating) {
        $this->id = $id;
        $this->title = $title;
        $this->releaseDate = $releaseDate;
        $this->description = $description;
        $this->posterPath = $posterPath;
        $this->rating = $rating;
    }

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getReleaseDate() {
        return $this->releaseDate;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getPosterPath() {
        return $this->posterPath;
    }

    public function getRating() {
        return $this->rating;
    }

    // Function to display the movie card
    public function displayMovie() {
        return "
        <div class='movie-card'>
            <img src='{$this->posterPath}' alt='{$this->title} Poster'>
            <h2>{$this->title}</h2>
            <p><strong>Release Date:</strong> {$this->releaseDate}</p>
            <p class='rating'>⭐ {$this->rating}/10</p>
            <p>{$this->description}</p>
            <form method='POST' action='addMovie.php'>
                <input type='hidden' name='movie_id' value='{$this->id}'>
                <input type='hidden' name='title' value='{$this->title}'>
                <input type='hidden' name='release_date' value='{$this->releaseDate}'>
                <input type='hidden' name='poster_path' value='{$this->posterPath}'>
                <button type='submit'>Add Movie</button>
            </form>
        </div>";
    }
}
?>
