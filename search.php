<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/project/css/style.css">
    <title>Chinook</title>
</head>
<body>
    <?php 
        // Require the file that sets up the connection to the db
        require_once "helpers/db_connection.php";

        // This line fixes issues with unrecognised characters
        header('Content-Type: text/html; charset=ISO-8859-1');

        // Pagination setup on each page load
        $curr_page = 1;
        $row_count = 35;
        if(isset($_GET["page"])){
            $curr_page = intval($_GET["page"]);
        }
        $page_offset = ($curr_page - 1) * 35;

        if(isset($_GET["category"]) && isset($_GET["value"])){
            $category = $_GET["category"];
            $value_unformatted = $_GET["value"];
            $value = explode("+", $value_unformatted);
            $value = implode(" ", $value);
        }

        // sql query to select all rows to determine the max number of pages
        $sql_unformatted = "SELECT albums.AlbumId, albums.Title, artists.Name as ArtistName FROM albums JOIN artists ON albums.ArtistId = artists.ArtistId WHERE $category LIKE \"%$value%\"";
        
        // sql query to limit the number of rows to 35
        $sql_formatted = "SELECT albums.AlbumId, albums.Title, albums.ArtistId, artists.Name as ArtistName FROM albums JOIN artists ON albums.ArtistId = artists.ArtistId WHERE $category LIKE \"%$value%\" LIMIT $row_count OFFSET $page_offset";
        
        // linking the connection to the sql query to make the request
        $albums = $conn->query($sql_formatted);
        // getting the total count of all rows and calculating the number of pages
        $total_rows = $conn->query($sql_unformatted)->num_rows;
        $total_pages = ceil($total_rows / $row_count);

        // <!-- PHP Code to handle album deletion -->
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $album_id = $_POST["AlbumId"];

            // query to delete album
            $sql_delete_album = "DELETE FROM albums WHERE AlbumId = $album_id";
            $sql_delete_albumTracks = "DELETE FROM tracks WHERE AlbumId = $album_id";

            $conn->query($sql_delete_album);
            $conn->query($sql_delete_albumTracks);

            header("Location: ./");
        }
    ?>

    <header>
        <h1 class="gradient-bg1 gradient-text-color-support">.Chinook Music Store.</h1>
        <a href="./insert-album.php" class="gradient-bg2">Insert Album</a>
    </header>

    <main>
        <div class="top-container">            
            <div class="back-btn">
                <a href="http://localhost:85/project/">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"><path stroke="#d1d5db" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M9.57 5.93L3.5 12l6.07 6.07M20.5 12H3.67"></path></svg>
                    Back to Home
                </a>
            </div>

            <div class="table_actions search_page">
                <form method="get" class="form_search">
                    <div class="form_control">
                        <select name="category" id="category">
                            <option value="Title">Album Title</option>
                            <option value="Name">Artist Name</option>
                        </select>
                        <input type="text" name="value" id="value" placeholder="Enter your search details" required>
                    </div>
                    
                    <button type="submit" class="gradient-bg3 form_btn">Search</button>
                </form>
            </div>
        </div>

        <div class="bottom-container">
           <div class="table-container">
                 <?php 
                    if($total_pages <= 0){ // handling the occurence where there are no results to display
                        echo "<p class=\"message\">No results to show</p>";
                    }else{
                        // Displaying the table of albums
                        echo "<table>";
                            echo "<thead class=\"gradient-bg3\">";
                                echo "<tr>";
                                    echo "<th>ID</th>";
                                    echo "<th>Title</th>";
                                    echo "<th>Artist</th>";
                                    echo "<th>Actions</th>";
                                echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                                while($album = $albums->fetch_assoc()){
                                    echo "<tr>"; 
                                        echo "<td>";
                                            echo $album["AlbumId"];
                                        echo "</td>";

                                        echo "<td>";
                                            echo $album["Title"];
                                        echo "</td>";

                                        echo "<td>";
                                            echo $album["ArtistName"];
                                        echo "</td>";

                                        echo "<td class=\"actions-cell\">";
                                        echo "<div>";
                                                    echo "<a class=\"gradient-bg3\" href=\"details.php?id=" . $album["AlbumId"] . "\">";
                                                    echo "Details</a>";

                                                    echo "<a class=\"gradient-bg3\" href=\"insert-album.php?id=" . $album["AlbumId"] . "\">";
                                                    echo "Update</a>";

                                                    echo "<button type=\"button\" class=\"action delete-btn" 
                                                        . "\" data-album-Id=\"" . $album["AlbumId"]
                                                        . "\" data-album-Title=\"" . $album["Title"]
                                                        . "\" data-artist-Name=\"" . $album["ArtistName"]
                                                    . "\">Delete</button>";
                                        echo "</div>";
                                        echo "</td>";
                                    echo "</tr>";   
                                }                            
                            echo "</tbody>";
                        echo "</table>";
                    
                        echo "<div>";
                            echo "<form method=\"get\" class=\"form_pagination\">";
                                echo "<input type=\"hidden\" name=\"category\" value=\"$category\">";

                                echo "<input type=\"hidden\" name=\"value\" value=\"$value_unformatted\">";

                                echo "<select name=\"page\" id=\"page\">";
                                    // getting the total number of pages for the pagination feature
                                    for($i = 1; $i <= $total_pages; $i++){
                                        $is_selected = $i == $curr_page ? "selected" : "";
                                        echo "<option value=\"$i\" $is_selected >$i</option>";
                                    }
                                echo "</select>";
                                    
                                echo "<button type=\"submit\" class=\"gradient-bg3 form_btn\">Go to Page</button>";
                            echo "</form>";
                        echo "</div>";
                    }
                ?>
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