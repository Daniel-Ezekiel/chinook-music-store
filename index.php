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
        header('Content-Type: text/html; charset=ISO-8859-1');

        // Pagination setup on each page load
        $curr_page = 1;
        $row_count = 35;
        if(isset($_GET["page"])){
            $curr_page = intval($_GET["page"]);
        }
        $page_offset = ($curr_page - 1) * 35;

        if(isset($_GET["sort"])){
            $sort_conditions = explode("-", $_GET["sort"]);
            $sort_tag = $sort_conditions[0];
            $sort_type = $sort_conditions[1];
        }else{
            $sort_tag = "AlbumId";
            $sort_type = "ASC";
        }

        //  Connecting to the Chinook Database
        $host = "localhost";
        $user = "root";
        $password = "";
        $dbname = "chinook";

        // setting up the connection
        $conn = new mysqli($host, $user, $password, $dbname);

        // sql query to select all rows to determine the max number of pages
        $sql_unformatted = "SELECT albums.AlbumId, albums.Title, artists.Name FROM albums JOIN artists ON albums.ArtistId = artists.ArtistId";
        // sql query to limit the number of rows to 35
        $sql_formatted = "SELECT albums.AlbumId, albums.Title, albums.ArtistId, artists.Name as ArtistName FROM albums JOIN artists ON albums.ArtistId = artists.ArtistId ORDER BY $sort_tag $sort_type LIMIT $row_count OFFSET $page_offset";
        
        // linking the connection to the sql query to make the request
        $albums = $conn->query($sql_formatted);
        // getting the total count of all rows and calculating the number of pages
        $total_rows = $conn->query($sql_unformatted)->num_rows;
        $total_pages = ceil($total_rows / $row_count);
        // echo $total_pages;
    ?>

    <!-- PHP Code to handle album deletion -->
    <?php 
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $album_id = $_POST["AlbumId"];

            // echo $album_id;
            // query to delete album
            $sql_delete_album = "DELETE FROM albums WHERE AlbumId = $album_id";
            $sql_delete_albumTracks = "DELETE FROM tracks WHERE AlbumId = $album_id";

            $conn->query($sql_delete_album);
            $conn->query($sql_delete_albumTracks);

            header("Location: ?page=$curr_page");
        }
    ?>

    <header>
        <h1 class="gradient-bg1 gradient-text-color-support">.Chinook Music Store.</h1>
        <a href="./insert-album.php" class="gradient-bg2">Insert Album</a>
    </header>

    <main>
        <div class="top-container">
            <div class="table_actions">
                <form method="get" class="form_sort">
                    <div class="form_control">
                        <div>
                            <label for="sort">Sort Albums</label>
                            <select name="sort" id="sort">
                                <option value="AlbumId-ASC">Default</option>
                                <option value="Title-ASC">Album Title (Ascending)</option>
                                <option value="Title-DESC">Album Title (Descending)</option>
                                <option value="ArtistName-ASC">Artist Name (Ascending)</option>
                                <option value="ArtistName-DESC">Artist Name (Descending)</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="gradient-bg3 form_btn">Apply</button>
                </form>

                <form action="search.php" method="get" class="form_search">
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
                <table>
                    <thead class="gradient-bg3">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Artist</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
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
                        ?>
                    </tbody>
                </table>
            </div>
            
            <div>
                <form method="get" class="form_pagination">                    
                    <?php 
                        if(isset($_GET["sort"])){
                            echo "<input type=\"hidden\" name=\"sort\" value=\"" . implode("-", $sort_conditions) . "\">";
                        };
                        echo "<select name=\"page\" id=\"page\">";
                            for($i = 1; $i <= $total_pages; $i++){
                                $is_selected = $i == $curr_page ? "selected" : "";
                                echo "<option value=\"$i\" $is_selected >$i</option>";
                            }
                        echo "</select>";
                    ?>

                    <button type="submit" class="gradient-bg3 form_btn">Go to Page</button>
                </form>
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
    <?php $conn->close(); ?>
</body>
</html>