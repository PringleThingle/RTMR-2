<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Movie</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <h1>Add Movie</h1>
    <?php
    require_once("php/page.class.php");
    require_once("php/util.class.php");
    require_once("php/movie.class.php");
    require_once("php/directorcrud.class.php");
    error_reporting(E_ALL);
    ini_set('display_errors', 1);


    $page = new Page(3);
    ?>

    <nav>
        <ul class="navbar">
            <?php echo $page->getMenu(); ?>
        </ul>
    </nav>

    <main>
    <?php
    if (util::posted($_POST['title']) && util::posted($_POST['description']) && util::posted($_POST['mid'])) {
        $movieId = util::sanInt($_POST['mid']);
        $title = util::sanStr($_POST['title']);
        $description = util::sanStr($_POST['description']);
        $posterLink = util::sanStr($_POST['posterLink']);
        $releaseDate = util::sanStr($_POST['releaseDate']);
        $bearerToken = "eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI1YTM1ODZhMWI2ODYyZjBhN2I4NjkyNDg5YzY2YzIwNiIsIm5iZiI6MTcyNTUyNTcwOS45MDEsInN1YiI6IjY2ZDk2ZWNkNWM1YTZiMmQwNDlkNGQ4YiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.DEamvyjmcqk2ycauQrGjBogoVlHdEU_l81ycN3I0cqo";  // Replace with your actual TMDb Bearer Token

        // Step 1: Fetch movie credits to get the director
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.themoviedb.org/3/movie/$movieId/credits?language=en-US",
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
            die("<h2>Error fetching movie credits. HTTP Code: $httpCode</h2>");
        }

        $data = json_decode($response, true);

        // Find the director in the credits response
        $director = null;
        foreach ($data['crew'] as $crewMember) {
            if ($crewMember['job'] === 'Director') {
                $director = $crewMember;
                break;
            }
        }

        if (!$director) {
            die("<h2>Director not found for this movie.</h2>");
        }

        // Step 2: Fetch detailed director info
        $directorId = $director['id'];
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.themoviedb.org/3/person/$directorId?language=en-US",
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
            die("<h2>Error fetching director details. HTTP Code: $httpCode</h2>");
        }

        $directorDetails = json_decode($response, true);
        $directorName = $directorDetails['name'];
        $directorDOB = $directorDetails['birthday'] ?? null;
        $directorBirthplace = $directorDetails['place_of_birth'] ?? 'Unknown';

        // Step 3: Add the director to the database (if not already present)
        $directorCRUD = new DirectorCRUD();
        try {
            $finalDirectorId = $directorCRUD->getOrCreateDirector($directorId, $directorName, $directorDOB, $directorBirthplace);
        } catch (Exception $e) {
            die("<h2>Error: " . $e->getMessage() . "</h2>");
        }

        // Step 4: Add the movie to the database
        $movietoadd = new Movie();
        $result = $movietoadd->addMovie($movieId, $title, $description, $releaseDate, $posterLink, $finalDirectorId);

        if ($result['insert'] > 0) {
            echo "<h2>Movie and Director Added Successfully!</h2>";
            echo $page->displayMovies();
        } else {
            echo "<h2>Add Failed</h2>";
            echo $result['messages'];
        }
    } else {
        echo "<h2>Error: Missing required movie data.</h2>";
    }
    ?>
    </main>
</body>
</html>
