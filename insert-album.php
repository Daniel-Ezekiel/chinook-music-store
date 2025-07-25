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
        // Require the file that sets up the connection to the db
        require_once "helpers/db_connection.php";
        // Require the file that has getArtistId function to manage artists for creating or updating album
        require_once "helpers/get_artistId.php";
        // Require the file that has addNewTrack function to add new track to db
        require_once "helpers/add_new-track.php";

           
        // This line fixes issues with unrecognised characters.   
        header('Content-Type: text/html; charset=ISO-8859-1');

        // Get the previous page URL for routing purposes on the back button
        $prev_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "index.php";

        // Query to get a list of all the artists in the DB for the artist name dropdown
        $sql_allArtists = "SELECT ArtistId, Name as ArtistName FROM artists ORDER BY Name ASC";
        $artists = $conn->query($sql_allArtists);

        // Handle getting album details to populate form fields in preparation for updating the selected album
        if($_SERVER["REQUEST_METHOD"] == "GET"){
            if(isset($_GET["id"])){
                // Grab album id from GET query parameter
                $album_id = explode("-", $_GET["id"])[0];

                $album_title;
                $artist_name;
                $track1_title;

                // Query that retrieves an album based on the id for updating
                $sql_album = "SELECT albums.Title as AlbumTitle, artists.Name as ArtistName from albums JOIN artists ON albums.ArtistId = artists.ArtistId WHERE albums.AlbumId = $album_id";
                // Query that retrieves the tracks associated with that album
                $sql_tracks = "SELECT * FROM tracks WHERE AlbumId = $album_id";

                $album_details = $conn->query($sql_album);
                $tracks = $conn->query($sql_tracks);

                // Storing the album title and artist name with which to populate the respective form fields
                while($album_detail = $album_details->fetch_assoc()){
                    $album_title = $album_detail["AlbumTitle"];
                    $artist_name = $album_detail["ArtistName"];
                }

                $retrieved_tracks = [];
                // using this loop, to add each $track which is an associative array into another array of all tracks called $retrieved_tracks
                while($track = $tracks->fetch_assoc()){
                    array_push($retrieved_tracks, $track);
                }
                // storing other tracks after track 1. i.e track 2 and so on...
                $other_tracks = array_slice($retrieved_tracks, 1);
            }
        }

        // Handle inserting/updating a new album in the chinook database
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            // Grabbing the album title and artist name from the POST parameters
            $album_title = $_POST["album-title"];
            $artist_name = $_POST["artist-name"];
            // Grabbing the list of all tracks from the POST parameters
            $all_tracks = array_slice($_POST, 2, null, true);

            // Handle update request using the album ID
            if(isset($_GET["id"])){
                $album_id = explode("-", $_GET["id"])[0];
                $artist_id = getArtistId($conn, $artist_name);             

                // Query to update album with artist id from getArtistId function above
                $sql_updateAlbum = "UPDATE albums SET Title = \"$album_title\", ArtistId = \"$artist_id\" WHERE AlbumId = \"$album_id\"";
                $conn->query($sql_updateAlbum);

                // Managing the updating of tracks by checking if the fields to be used for the update do not have some tracks in the db, then delete those tracks from the db
                $sql_allTracksInAlbum = "SELECT TrackId, Name FROM tracks WHERE AlbumId = \"$album_id\"";
                $all_tracksInAlbum = $conn->query($sql_allTracksInAlbum);

                // Remove track from the DB if it is not in the new list of tracks gotten from the form
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
                
                // For each track in the form, update the album with those tracks
                foreach($all_tracks as $track_id => $track_name){
                    $track_id = intval($track_id); // changing track id to int so as to use it in a conditional to add a track that does not exist in album
                    
                    if($track_id != 0){ // track is an existing track
                        $sql_updateTrack = "UPDATE tracks SET Name = \"$track_name\" WHERE TrackId = \"$track_id\"";
                        $conn->query($sql_updateTrack);
                    }else { // track is a new track
                        addNewTrack($conn, $album_id, $track_name);
                    }
                }

                header("Location: details.php/?id=$album_id");
            }else {  // Handle insert request if new album information              
                $artists_count;
                $artist_id;
                $album_id;

                // Get the current maximum AlbumId
                $sql_getMaxId = "SELECT MAX(AlbumId) AS max_id FROM albums";
                $result = $conn->query($sql_getMaxId);
                $row = $result->fetch_assoc();
                $new_album_id = $row['max_id'] ? $row['max_id'] + 1 : 1;
                $album_id = $new_album_id;                

                $artist_id = getArtistId($conn, $artist_name);  

                // Query to add album to the database and tie it to the artist id gotten from getArtistId function
                $sql_addAlbum = "INSERT INTO albums (AlbumId, Title, ArtistId) VALUES (\"$album_id\", \"$album_title\", \"$artist_id\")";
                $conn->query($sql_addAlbum);

                // Add each track from form as a new entry for the new album
                foreach($all_tracks as $track_num => $track_name){
                    addNewTrack($conn, $album_id, $track_name);
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
                    // adding the tracks to the form for the update process
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
                        <?php isset($_GET["id"]) ? print("Update") : print("Insert"); ?> 
                        Album
                    </button>
                </div>
            </form>

        </div>
    </main>

    <script type="module" src="/project/js/tracks.js"></script>
    <?php 
        // Closing the connection
        $conn->close(); 
    ?>
</body>
</html>