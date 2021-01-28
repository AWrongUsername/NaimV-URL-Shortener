<?php

function GenerateShortUUID($length, $conn) {
    $good = false;
    while(!$good) {
        $random = '';
        for ($i = 0; $i < $length; $i++) {
            $random .= chr(rand(ord('a'), ord('z')));
        }

        $stmt2 = $conn->prepare("SELECT * FROM urls WHERE id=?");
        $stmt2->bind_param("s", $random);
        $stmt2->execute();
        if($stmt2->num_rows == 0) {
            $good = true;
            return $random;
        }
    }

    return $random;
}

function AddURL($URL, $MadeBy, $conn) {
    $result = $conn->query("SELECT id, Url FROM urls");

    $uuid = '';
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if($row['Url'] == $URL) {
                $uuid = $row['id'];
                return $uuid;
            }
        }

        if($uuid == '') {
            $uuid = GenerateShortUUID(6, $conn);
            $stmt = $conn->prepare("INSERT INTO urls (id, Url, Email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $uuid, $URL, $MadeBy);
            $stmt->execute();
        }
    } else {
        $uuid = GenerateShortUUID(6, $conn);
        $stmt = $conn->prepare("INSERT INTO urls (id, Url, Email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $uuid, $URL, $MadeBy);
        $stmt->execute();
    }
        
    return $uuid;
}

function GetLink($uuid, $conn) {
    $stmt = $conn->prepare("SELECT Url FROM urls WHERE id=?");
    $stmt->bind_param("s", $uuid);
    $stmt->execute();
    $link = '';
    $stmt->bind_result($link);
    $stmt->fetch();
    return $link;

}

?>