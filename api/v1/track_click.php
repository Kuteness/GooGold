<?php
header("Content-Type: application/json");
include("../../config.php");

try {
    if (!isset($_GET["id"]) || !isset($_GET["type"])) {
        echo json_encode([
            "status" => "failed",
            "message" => "Missing 'id' or 'type' parameter."
        ]);
        exit;
    }

    $id = (int) $_GET["id"];
    $type = strtolower($_GET["type"]);

    if ($type === "images") {
        $table = "images";
    } elseif ($type === "search" || $type === "web") {
        $table = "sites";
    } else {
        echo json_encode([
            "status" => "failed",
            "message" => "Invalid type parameter."
        ]);
        exit;
    }

    $stmt = $con->prepare("UPDATE $table SET clicks = clicks + 1 WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
        "status" => "success",
        "message" => "Click tracked."
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "failed",
        "message" => $e->getMessage()
    ]);
}
?>
