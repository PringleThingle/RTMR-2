<!doctype html>
<head>
<?php
require_once("php/page.class.php");
require_once("php/view.class.php");
require_once("php/selectmovie.class.php");
$page = new Page(2);
$pagename = "Movie Search";
view::showHead($pagename);
view::showHeader($pagename);
?>
<body>
<nav>
<ul class="navbar">
<?php echo $page->getMenu(); ?>
</ul>
</nav>
<main>

<?php
// Default search term (if no input is provided)
$searchQuery = isset($_GET['query']) && !empty(trim($_GET['query'])) ? urlencode($_GET['query']) : null;

// The Movie Database API credentials
$bearerToken = "eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI1YTM1ODZhMWI2ODYyZjBhN2I4NjkyNDg5YzY2YzIwNiIsIm5iZiI6MTcyNTUyNTcwOS45MDEsInN1YiI6IjY2ZDk2ZWNkNWM1YTZiMmQwNDlkNGQ4YiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.DEamvyjmcqk2ycauQrGjBogoVlHdEU_l81ycN3I0cqo";

if ($searchQuery) {
    // cURL request to fetch movie data
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.themoviedb.org/3/search/movie?query=$searchQuery&include_adult=false&language=en-US&page=1",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $bearerToken",
            "Accept: application/json"
        ],
    ]);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($httpCode !== 200) {
        die("<h2>Error: Unable to fetch data. HTTP Code: $httpCode</h2>");
    }

    $data = json_decode($response, true);
    $movies = $data['results'] ?? [];
} else {
    $movies = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #121212; color: white; text-align: center; }
        .container { max-width: 1170px; margin: 20px auto; }
        .search-box { margin-bottom: 20px; align-items: center; justify-content: center;}
        input{ padding: 10px; margin-left:215px; width: 700px; font-size: 16px; border:2px solid #5A4027; background-color:rgb(54,54,54)}
        img { width: 95%; border-radius: 10px; }
        h2 { font-size: 18px; margin: 10px 0; border-radius:3px; padding-left:10px;}
        p { font-size: 14px; padding-left:5px; padding-right:10px; margin: 5px 0; max-height: 150px; overflow-y:auto; overflow-x:auto; border-radius:3px;}
    </style>
</head>
<body>
    <div class="container">
        <h1>Movie Search</h1>
        <form method="GET" class="search-box">
            <input type="text" name="query" placeholder="Search for a movie..." value="<?= htmlspecialchars($searchQuery ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            <button class="movieselectbutton" style="color:white" type="submit">Search</button>
        </form>

        <div class="movie-container" style="padding-top:10px">
            <?php if (!empty($movies)): ?>
                <?php 
                foreach ($movies as $movie) {
                    if (!empty($movie['id']) && !empty($movie['title']) && isset($movie['release_date'], $movie['overview'], $movie['poster_path'])) {
                        $movieObj = new SelectMovie(
                            $movie['id'],
                            htmlspecialchars($movie['title']),
                            $movie['release_date'],
                            htmlspecialchars($movie['overview']),
                            "https://image.tmdb.org/t/p/w500" . $movie['poster_path'],
                            "Unknown",
                            (!empty($movie['vote_average']) || $movie['vote_average'] === "0") ? number_format($movie['vote_average'], 1) : "N/A"
                        );
                        
                        echo $movieObj->displayMovie();
                    }
                }
                ?>
            <?php else: ?>
                <h2>No movies found. Try another search.</h2>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

</main>
</body>
</html>
