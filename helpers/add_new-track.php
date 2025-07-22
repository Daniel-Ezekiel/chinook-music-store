<?php
    function addNewTrack($conn, $album_id, $track_name){
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
?>