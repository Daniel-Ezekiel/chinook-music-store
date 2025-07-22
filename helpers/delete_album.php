<?php
    function deleteAlbum($conn, $album_id){
        // query to delete album
        $sql_delete_album = "DELETE FROM albums WHERE AlbumId = $album_id";
        // query to delete tracks for the selected album
        $sql_delete_albumTracks = "DELETE FROM tracks WHERE AlbumId = $album_id";

        $conn->query($sql_delete_album);
        $conn->query($sql_delete_albumTracks);
    }
?>