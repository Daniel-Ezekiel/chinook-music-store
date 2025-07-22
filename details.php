<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/project/css/details.css">
    <title>Details</title>
</head>
<body>
    <?php 
        // Require the file that sets up the connection to the db
        require_once "helpers/db_connection.php";
        
        // This line fixes issues with unrecognised characters
        header('Content-Type: text/html; charset=ISO-8859-1');

        // Handling previous page link for the back button
        $prev_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "index.php";

        // Handling the process for showing album details
        if(isset($_GET["id"])){
            // Grabbing the album id from the GET query parameter
            $album_id = $_GET["id"];
            $album_title;
            $artist_name;
            
            // Query to selectthe appropriate ablum using the album id from the GET parameter
            $sql_album = "SELECT albums.Title as AlbumTitle, artists.Name as ArtistName from albums JOIN artists ON albums.ArtistId = artists.ArtistId WHERE albums.AlbumId = $album_id";
            // Query to select the tracks that match the album id from the GET parameter
            $sql_tracks = "SELECT * FROM tracks WHERE AlbumId = $album_id";

            $album_details = $conn->query($sql_album);
            $tracks = $conn->query($sql_tracks);

            // From the database query response for album details, update the values for the album_title and artist_name variables
            while($album_detail = $album_details->fetch_assoc()){
                $album_title = $album_detail["AlbumTitle"];
                $artist_name = $album_detail["ArtistName"];
            }
        }

        // PHP Code to handle album deletion
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            // Getting the album id to setup the deletion task
            $album_id = $_POST["AlbumId"];

            // query to delete album
            $sql_delete_album = "DELETE FROM albums WHERE AlbumId = $album_id";
            // query to delete tracks for the selected album
            $sql_delete_albumTracks = "DELETE FROM tracks WHERE AlbumId = $album_id";

            $conn->query($sql_delete_album);
            $conn->query($sql_delete_albumTracks);

            // Redirect to the page containing the deleted album
            header("Location: /project");
        }
    ?>

    <header>
        <h1 class="gradient-bg1 gradient-text-color-support">.Chinook Music Store.</h1>

        <h2 class="gradient-bg2 gradient-text-color-support">
            <span>Album Name</span>
            <span><?php echo "$album_title" ?></span>
        </h2>
                        
        <h3 class="gradient-bg2 gradient-text-color-support">
            <span>Artist</span>
            <span><?php echo "$artist_name" ?></span>
        </h3>
                        
    </header>

    <main>
        <div class="top-container">
            <div class="back-btn">
                <a href=<?php echo "$prev_page"; ?>>
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"><path stroke="#d1d5db" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M9.57 5.93L3.5 12l6.07 6.07M20.5 12H3.67"></path></svg>
                    Back
                </a>
            </div>

            <div class="page-actions">
                <?php
                    // Handle routing to the page for updating album details
                    echo "<a class=\"gradient-bg3\" href=\"/project/insert-album.php?id=" . $album_id . "\">";
                    echo "Update Album</a>";

                    echo "<button type=\"button\" class=\"action delete-btn\" data-album-Id=\"$album_id\" data-album-Title=\"$album_title\" data-artist-Name=\"$artist_name\">Delete Album</button>";
                ?>
            </div>
        </div>
        <div class="bottom-container">
            <div class="table-container">
                <table>
                    <thead class="gradient-bg3">
                        <tr>
                            <th>ID</th>
                            <th>Track Name</th>
                            <th>Composer</th>
                            <th>Duration(m:s)</th>
                            <th>Size(MB)</th>
                            <th>Unit Price(£)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            //  Displaying the track information for that particular album
                            while($track = $tracks->fetch_assoc()){
                                echo "<tr>"; 
                                    echo "<td>";
                                        echo $track["TrackId"];
                                    echo "</td>";

                                    echo "<td>";
                                        echo $track["Name"];
                                    echo "</td>";

                                    echo "<td>";
                                        echo $track["Composer"] !== "" ? $track["Composer"] : "None";
                                    echo "</td>";

                                    echo "<td>";
                                        $duration_mins = floor(intval($track["Milliseconds"]) / (1000 * 60));
                                        $duration_secs = intval($track["Milliseconds"]) % 60;
                                        $duration = "$duration_mins:" . str_pad($duration_secs, 2, "0", STR_PAD_LEFT);
                                        echo $duration;
                                    echo "</td>";

                                    echo "<td>";
                                        $size_in_mb = intval($track["Bytes"]) / (1024 * 1024);
                                        $size_formatted = round($size_in_mb, 2);
                                        echo $size_formatted;
                                    echo "</td>";

                                    echo "<td>";
                                        echo $track["UnitPrice"];
                                    echo "</td>";
                                echo "</tr>";   
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Delete modal -->
        <div class="modal_overlay">
            <div class="modal">
                <h5>Delete Album</h5>

                <p>Are you sure you want to delete Album <span class="gradient-bg1 gradient-text-color-support modal-info id">25</span>: <span class="gradient-bg1 gradient-text-color-support modal-info title">Album Title</span> by <span class="gradient-bg1 gradient-text-color-support modal-info artist-name">Artist name</span> including <span class="gradient-bg1 gradient-text-color-support modal-info">all its tracks</span>?</p>

                <form method="post" id="delete-form">
                    <button type="button" class="modal-btn cancel-btn">Cancel</button>
                    <button type=submit class="modal-btn delete-btn">Delete</button>
                    
                    <input class="modal-form album-id" type="hidden" name="AlbumId">
                    <input class="modal-form album-title" type="hidden" name="AlbumTitle">
                    <input class="modal-form artist-name" type="hidden" name="ArtistName">
                </form>
            </div>
        </div>
    </main>

    <script defer src="/project/js/script.js"></script>
    <?php 
        // Closing the connection
        $conn->close(); 
    ?>
</body>
</html>