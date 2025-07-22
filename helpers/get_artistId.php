<?php
    function getArtistId($conn, $artist_name){
        /*
            Check whether artist exists in database. If they exist, get the artist id for updating album; if not, add new artist to database and use new artist id to update album
        */
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

        return $artist_id;
    }
?>