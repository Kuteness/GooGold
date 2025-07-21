<?php
include("../../config.php");

try {
    $query = $con->prepare("SELECT url FROM sites ORDER BY RAND() LIMIT 1");
    $query->execute();

    $row = $query->fetch(PDO::FETCH_ASSOC);

    if ($row && isset($row["url"])) {
        header("Location: " . $row["url"]);
        exit;
    } else {
        http_response_code(404);
        echo "No sites available.";
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "Server error: " . $e->getMessage();
}
?>
