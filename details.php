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
        header('Content-Type: text/html; charset=ISO-8859-1');

        $prev_page = $_SERVER['HTTP_REFERER'];

        $host = "localhost";
        $user = "root";
        $password = "";
        $dbname = "chinook";

        $conn = new mysqli($host, $user, $password, $dbname);

        if(isset($_GET["id"])){
            $album_id = $_GET["id"];
            
            // $sql = "SELECT tracks.*, albums.Title as AlbumTitle FROM tracks JOIN albums ON $album_id = albums.AlbumId JOIN artists ON albums.ArtistId = artists.ArtistId WHERE tracks.AlbumId = $album_id";
            $sql_album = "SELECT albums.Title as AlbumTitle, artists.Name as ArtistName from albums JOIN artists ON albums.ArtistId = artists.ArtistId WHERE albums.AlbumId = $album_id";
            $sql = "SELECT * FROM tracks WHERE AlbumId = $album_id";

            $album_details = $conn->query($sql_album);
            $tracks = $conn->query($sql);
        }
    ?>

    <header>
        <h1 class="gradient-bg1 gradient-text-color-support">.Chinook Music Store.</h1>
        <?php 
                while($album_detail = $album_details->fetch_assoc()){
                    echo "<h2 class=\"gradient-bg2 gradient-text-color-support\">";
                        echo "<span>Album Name</span>";
                        echo "<span>" . $album_detail["AlbumTitle"] . "</span>";
                    echo "</h2>";
                        
                    echo "<h3 class=\"gradient-bg2 gradient-text-color-support\">";
                        echo "<span>Artist</span>";
                        echo "<span>" . $album_detail["ArtistName"] . "</span>";
                    echo "</h3>";
                }
        ?>
        <!-- <button class="gradient-bg2">Insert Album</button> -->

        
    </header>

    <main>
        <div class="top-container">
            <div class="back-btn">
                <a href=<?php echo "$prev_page"; ?>>
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"><path stroke="#d1d5db" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M9.57 5.93L3.5 12l6.07 6.07M20.5 12H3.67"></path></svg>
                    Back
                </a>
            </div>

            <div class="update-btn">
                <a class="gradient-bg3" href="<?php echo "insert-album.php?id=$album_id" ?>">Update Album</a>
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
    </main>
    <?php $conn->close() ?>
</body>
</html>