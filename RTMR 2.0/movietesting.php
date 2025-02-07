<!doctype html>
<head>
<?php
require_once("php/page.class.php");
require_once("php/view.class.php");
require_once("php/selectmovie.class.php");
$page = new Page();
$pagename = "Movie testing";
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
$bearerToken = "eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI1YTM1ODZhMWI2ODYyZjBhN2I4NjkyNDg5YzY2YzIwNiIsIm5iZiI6MTcyNTUyNTcwOS45MDEsInN1YiI6IjY2ZDk2ZWNkNWM1YTZiMmQwNDlkNGQ4YiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.DEamvyjmcqk2ycauQrGjBogoVlHdEU_l81ycN3I0cqo";  // Replace with your TMDb API Key

if ($searchQuery) {

    // cURL request to fetch movie data
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.themoviedb.org/3/search/movie?query=$searchQuery&include_adult=false&language=en-US&page=1",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $bearerToken",
            "Accept: application/json"
        ],
    ]);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $err = curl_error($curl);
    curl_close($curl);

    // Handle errors
    if ($httpCode !== 200) {
        die("<h2>Error: Unable to fetch data. HTTP Code: $httpCode</h2>");
    }

    // Decode JSON response
    $data = json_decode($response, true);

    // Check if results exist
    $movies = isset($data['results']) ? $data['results'] : [];
} else {
    $movies = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Search</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #121212; color: white; text-align: center; }
        .container { max-width: 800px; margin: 20px auto; }
        .search-box { margin-bottom: 20px; }
        input[type="text"] { padding: 10px; width: 70%; font-size: 16px; }
        button { padding: 10px; font-size: 16px; cursor: pointer; }
        .movie-container { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; margin-top: 20px; }
        .movie-card { background: #1e1e1e; padding: 15px; border-radius: 10px; width: 250px; text-align: left; }
        img { width: 100%; border-radius: 10px; }
        h2 { font-size: 18px; margin: 10px 0; }
        p { font-size: 14px; margin: 5px 0; }
        .rating { font-weight: bold; color: #FFD700; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Movie Search</h1>
        <form method="GET" class="search-box">
            <input type="text" name="query" placeholder="Search for a movie..." value="<?= htmlspecialchars($searchQuery ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            <button type="submit">Search</button>
        </form>

        <div class="movie-container">
            <?php if (!empty($movies)): ?>
                <?php 

                    foreach ($movies as $movie) {
                        $movieObj = new SelectMovie(
                            $movie['id'],
                            htmlspecialchars($movie['title']),
                            !empty($movie['release_date']) ? $movie['release_date'] : "Unknown",
                            !empty($movie['overview']) ? htmlspecialchars(substr($movie['overview'], 0, 150)) . "..." : "No description available.",
                            !empty($movie['poster_path']) ? "https://image.tmdb.org/t/p/w500" . $movie['poster_path'] : "https://via.placeholder.com/300x450?text=No+Image",
                            isset($movie['vote_average']) ? number_format($movie['vote_average'], 1) : "N/A"
                        );
                        echo $movieObj->displayMovie();
                    }
                ?>
                    <div class="movie-card">
                        <img src="<?= $posterPath ?>" alt="<?= $title ?> Poster">
                        <h2><?= $title ?></h2>
                        <p><strong>Release Date:</strong> <?= $releaseDate ?></p>
                        <p class="rating">⭐ <?= $rating ?>/10</p>
                        <p><?= $description ?></p>
                    </div>
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