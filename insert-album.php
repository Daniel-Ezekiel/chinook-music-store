<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/project/css/insert-album.css">
    <title>Insert Album</title>
</head>
<body>
    <?php        
        header('Content-Type: text/html; charset=ISO-8859-1');

        $prev_page = $_SERVER['HTTP_REFERER'] ?? "index.php";
        $operation;

        $host = "localhost";
        $user = "root";
        $password = "";
        $dbname = "chinook";

        $conn = new mysqli($host, $user, $password, $dbname);

        // Get a list of all the artisits for the artist name dropdown
        $sql_allArtists = "SELECT ArtistId, Name as ArtistName FROM artists ORDER BY Name ASC";
        $artists = $conn->query($sql_allArtists);

        if($_SERVER["REQUEST_METHOD"] == "GET"){
            if(isset($_GET["id"])){
                $operation = "update";
                $album_id = explode("-", $_GET["id"])[0];

                $album_title;
                $artist_name;
                $track1_title;

                $sql_album = "SELECT albums.Title as AlbumTitle, artists.Name as ArtistName from albums JOIN artists ON albums.ArtistId = artists.ArtistId WHERE albums.AlbumId = $album_id";
                $sql_tracks = "SELECT * FROM tracks WHERE AlbumId = $album_id";

                $album_details = $conn->query($sql_album);
                $tracks = $conn->query($sql_tracks);

                while($album_detail = $album_details->fetch_assoc()){
                    $album_title = $album_detail["AlbumTitle"];
                    $artist_name = $album_detail["ArtistName"];
                }

                $retrieved_tracks = [];
                // using this loop, to add each $track which is an associative array into another array of all tracks
                while($track = $tracks->fetch_assoc()){
                    array_push($retrieved_tracks, $track);
                }
                $other_tracks = array_slice($retrieved_tracks, 1);
                // print_r($other_tracks);
            }
        }

        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $album_title = $_POST["album-title"];
            $artist_name = $_POST["artist-name"];
            $all_tracks = array_slice($_POST, 2, null, true);

            if(isset($_GET["id"])){
                $album_id = explode("-", $_GET["id"])[0];

                // Check if artist exists in DB, then dettermine id based on outcome of conditional
                $sql_selectArtist = "SELECT ArtistId from artists WHERE Name = \"$artist_name\"";
                $artists =  $conn->query($sql_selectArtist);
                if($artists->num_rows){
                    while($artist = $artists->fetch_assoc()){
                        $artist_id = $artist["ArtistId"];
                    }
                
                    $sql_updateArtist = "UPDATE artists SET Name = \"$artist_name\" WHERE ArtistId = \"$artist_id\"";
                    // echo "<pre>$sql_updateArtist</pre>";
                    $conn->query($sql_updateArtist);
                }else{
                    // Get the current maximum ArtistId
                    $sql_getMaxId = "SELECT MAX(ArtistId) AS max_id FROM artists";
                    $result = $conn->query($sql_getMaxId);
                    $row = $result->fetch_assoc();
                    $new_artist_id = $row['max_id'] ? $row['max_id'] + 1 : 1;
                    $artist_id = $new_artist_id;

                    $sql_addArtist = "INSERT INTO artists (ArtistId, Name) VALUES ($artist_id, \"$artist_name\")";
                    $conn->query($sql_addArtist);
                }

                
                $sql_updateAlbum = "UPDATE albums SET Title = \"$album_title\", ArtistId = \"$artist_id\" WHERE AlbumId = \"$album_id\"";
                // echo "<pre>$sql_updateAlbum</pre>";
                $conn->query($sql_updateAlbum);

                // Managing the updating of tracks by checking if the fields to be used for the update do not have some tracks in the db, then delete those tracks from the db
                $sql_allTracksInAlbum = "SELECT TrackId, Name FROM tracks WHERE AlbumId = \"$album_id\"";
                $all_tracksInAlbum = $conn->query($sql_allTracksInAlbum);

                // Remove track if it is not in the list of tracks from the form
                while($track = $all_tracksInAlbum->fetch_assoc()){
                    $track_id = $track["TrackId"];
                    $is_track_in_form = array_key_exists($track_id, $all_tracks);
                    
                    if(!$is_track_in_form){
                        $sql_removeTrack = "DELETE from tracks WHERE TrackId = \"$track_id\"";
                        $conn->query($sql_removeTrack);
                    }else{
                        continue;
                    }
                }
                
                foreach($all_tracks as $track_id => $track_name){
                    $track_id = intval($track_id); // changing track id to int so as to use it in a conditional to add a track that does not exist in album
                    
                    if($track_id != 0){
                        $sql_updateTrack = "UPDATE tracks SET Name = \"$track_name\" WHERE TrackId = \"$track_id\"";
                        $conn->query($sql_updateTrack);
                    }else {
                        // Get the current maximum TrackId
                        $sql_getMaxId = "SELECT MAX(TrackId) AS max_id FROM tracks";
                        $result = $conn->query($sql_getMaxId);
                        $row = $result->fetch_assoc();
                        $new_track_id = $row['max_id'] ? $row['max_id'] + 1 : 1;
                        $track_id = $new_track_id;

                        $random_mediaTypeId = rand(1,5);
                        $random_genreId = rand(1,25);
                        $random_milliSecs = rand(190000,380000);
                        $random_bytes = rand(3900000,12000000);

                        $sql_addNewTrack = "INSERT INTO tracks (TrackId, Name, AlbumId, MediaTypeId, GenreId, Composer, Milliseconds, Bytes, UnitPrice) VALUES (\"$track_id\", \"$track_name\", \"$album_id\", \"$random_mediaTypeId\", \"$random_genreId\", \"\", \"$random_milliSecs\", \"$random_bytes\", \"0.99\")";
                        $conn->query($sql_addNewTrack);
                        // echo "<pre>$sql_updateTrack</pre>";
                    }
                }

                header("Location: details.php/?id=$album_id");
            }else {                
                $artists_count;
                $artist_id;
                $album_id;

                // Get the current maximum AlbumId
                $sql_getMaxId = "SELECT MAX(AlbumId) AS max_id FROM albums";
                $result = $conn->query($sql_getMaxId);
                $row = $result->fetch_assoc();
                $new_album_id = $row['max_id'] ? $row['max_id'] + 1 : 1;
                $album_id = $new_album_id;

                // Check if artist exists in DB, then dettermine id based on outcome of conditional
                $sql_selectArtist = "SELECT ArtistId from artists WHERE Name = \"$artist_name\"";
                $artists =  $conn->query($sql_selectArtist);
                if($artists->num_rows){
                    while($artist = $artists->fetch_assoc()){
                        $artist_id = $artist["ArtistId"];
                    }
                }else{
                    // Get the current maximum ArtistId
                    $sql_getMaxId = "SELECT MAX(ArtistId) AS max_id FROM artists";
                    $result = $conn->query($sql_getMaxId);
                    $row = $result->fetch_assoc();
                    $new_artist_id = $row['max_id'] ? $row['max_id'] + 1 : 1;
                    $artist_id = $new_artist_id;

                    $sql_addArtist = "INSERT INTO artists (ArtistId, Name) VALUES ($artist_id, \"$artist_name\")";
                    $conn->query($sql_addArtist);
                }

                $sql_addAlbum = "INSERT INTO albums (AlbumId, Title, ArtistId) VALUES (\"$album_id\", \"$album_title\", \"$artist_id\")";
                $conn->query($sql_addAlbum);

                foreach($all_tracks as $track_num => $track_name){
                    // Get the current maximum TrackId
                    $sql_getMaxId = "SELECT MAX(TrackId) AS max_id FROM tracks";
                    $result = $conn->query($sql_getMaxId);
                    $row = $result->fetch_assoc();
                    $new_track_id = $row['max_id'] ? $row['max_id'] + 1 : 1;
                    $track_id = $new_track_id;
                    
                    $random_mediaTypeId = rand(1,5);
                    $random_genreId = rand(1,25);
                    $random_milliSecs = rand(190000,380000);
                    $random_bytes = rand(3900000,12000000);

                    $sql_addTrack = "INSERT INTO tracks (TrackId, Name, AlbumId, MediaTypeId, GenreId, Composer, Milliseconds, Bytes, UnitPrice) VALUES (\"$track_id\", \"$track_name\", \"$album_id\", \"$random_mediaTypeId\", \"$random_genreId\", \"\", \"$random_milliSecs\", \"$random_bytes\", \"0.99\")";
                    $conn->query($sql_addTrack);
                }

                header("Location: details.php/?id=$album_id");
            }
        }
    ?>

    <header>
        <h1 class="gradient-bg1 gradient-text-color-support">.Chinook Music Store.</h1>
        <div class="back-btn">
            <a href=<?php echo "/project"; ?>>
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"><path stroke="#d1d5db" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M9.57 5.93L3.5 12l6.07 6.07M20.5 12H3.67"></path></svg>
                Back
            </a>
        </div>
    </header>
    
    <main>
        <div>
            <div>
                <?php
                    if(isset($_GET["id"])){
                        echo "<h2>Update Existing Album</h2>";
            
                        echo "<p>Enter the <span class=\"gradient-bg2 gradient-text-color-support\">album details in the fields below</span> to update the album</p>";
                    }else {
                        echo "<h2>Create New Album</h2>";
            
                        echo "<p>Enter the <span class=\"gradient-bg2 gradient-text-color-support\">album details in the fields below</span> to add a new entry to the list of albums</p>";
                    }

                ?>
            </div>

            <form method="post">
                <div class="form_control">
                    <label for="album-title">Album Title*</label>
                    <input type="text" name="album-title" id="album-title" placeholder="Enter the title of the album" value="<?php  echo $album_title ?? "" ?>" required>
                </div>

                <div class="form_control">
                    <label for="artist-name">Artist Name*</label>
                    <input type="text" list="artist-names" name="artist-name" id="artist-name" placeholder="Enter the artist name for the album" value="<?php echo $artist_name ?? "" ?>" required>
                    <datalist id="artist-names">
                        <?php
                            while($artist = $artists->fetch_assoc()){
                                echo "<option value=\"" . $artist["ArtistName"] . "\"></option>";
                            }
                        ?>
                    </datalist>
                </div>

                <div class="form_control track_control">
                    <label for="track-1">Track 1*</label>
                    <input type="text" name="<?php echo $retrieved_tracks[0]["TrackId"] ?? "track-1" ?>" id="track-1" placeholder="Enter the name for track 1 in the album" value="<?php echo $retrieved_tracks[0]["Name"] ?? "" ?>" required>
                    <!-- <input type="hidden" name="track1-id" id="track1-id" value="" required> -->
                    <!-- <button type="button" class="btn_remove-track">
                        <img src="./img/icons/close-circle.svg" alt="icon">
                    </button> -->
                </div>

                <?php 
                    if(isset($other_tracks)){
                        for($i = 0; $i < count($other_tracks); $i++){
                            $track_number = $i + 2;
                            echo "<div class=\"form_control track_control\" data-track-number=\"$track_number\">";
                                echo "<label for=\"track-$track_number\">";
                                    echo "track $track_number*";
                                echo "</label>";

                                echo "<input type=\"text\" name=\"" . $other_tracks[$i]["TrackId"] . "\" id=\"track-$track_number\" placeholder=\"Enter the name for track $track_number in the album\" value=\"" . $other_tracks[$i]["Name"] . "\" required=\"true\">";

                                // echo "<input type=\"hidden\" name=\"track$track_number-id\" id=\"track$track_number-id\" value=\"" . $other_tracks[$i]["TrackId"] . "\" required=\"true\">";

                                echo "<button type=\"button\" class=\"btn_remove-track\">";
                                    echo "<img src=\"./img/icons/close-circle.svg\" alt=\"icon\">";
                                echo "</button>";
                            echo "</div>";
                        }
                    }
                ?>

                <div class="form_control btn_control">
                    <button type="button" class="gradient-bg2 btn_add-track">
                        Add Track
                    </button>

                    <button type="button" class="gradient-bg2 btn_populate">
                        Populate Form
                    </button>

                    <button class="gradient-bg3 btn_insert">
                        Insert Album
                    </button>
                </div>
            </form>

        </div>
    </main>

    <script type="module" src="/project/js/tracks.js"></script>
    <?php $conn->close() ?>
</body>
</html>