<?php
include("config.php");
session_start();

function linkExists($url) {
    global $con;
    $query = $con->prepare("SELECT * FROM sites WHERE url = :url");
    $query->bindParam(":url", $url);
    $query->execute();
    return $query->rowCount() != 0;
}

function insertLink($url, $title, $description, $keywords) {
    global $con;

    if (linkExists($url)) {
        $query = $con->prepare("UPDATE sites SET title = :title, description = :description, keywords = :keywords WHERE url = :url");
    } else {
        $query = $con->prepare("INSERT INTO sites(url, title, description, keywords) VALUES(:url, :title, :description, :keywords)");
    }

    $query->bindParam(":url", $url);
    $query->bindParam(":title", $title);
    $query->bindParam(":description", $description);
    $query->bindParam(":keywords", $keywords);

    return $query->execute();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = isset($_POST['url']) ? trim($_POST['url']) : "";
    $title = isset($_POST['title']) ? trim($_POST['title']) : "";
    $description = isset($_POST['description']) ? trim($_POST['description']) : "";
    $keywords = isset($_POST['keywords']) ? trim($_POST['keywords']) : "";

    if (filter_var($url, FILTER_VALIDATE_URL)) {
        if (insertLink($url, $title, $description, $keywords)) {
            $message = "<div class='alert alert-success'>URL saved successfully.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Failed to save URL.</div>";
        }
    } else if ($url !== "") {
        $message = "<div class='alert alert-danger'>Invalid URL provided.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Site</title>
    <link rel="icon" type="image/x-icon" href="assets/images/favicon/favicon.ico">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body {
            background: #e5e5e5;
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
        }
        .headerContent {
            background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                    url("/assets/site_assets/reagan.png");
            padding: 15px 30px;
            color: white;
            margin-bottom: 30px;
            box-shadow: 0 3px 6px rgba(0,0,0,.1);
        }
        #submit-wrapper {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            padding: 25px;
            border: 1px solid #ddd;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            min-width: 120px;
            border-radius: 0;
            font-weight: bold;
            box-shadow: 0 3px 6px rgba(0,0,0,.1);
        }
        .form-control {
            border-radius: 0;
        }
        .alert {
            margin-bottom: 0px;
        }
    </style>
</head>
<body>
    <div class="headerContent text-center">
        <h1 style="font-weight: 643;">Submit a Site</h1>
    </div>

    <div id="submit-wrapper">
        <?php echo $message; ?>
        <form action="" method="post" accept-charset="utf-8" class="form">
            <div class="form-group">
                <label for="url">URL:</label>
                <input type="text" name="url" id="url" class="form-control" placeholder="Enter URL" required
                       value="<?php echo isset($_POST['url']) ? htmlspecialchars($_POST['url']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" class="form-control"
                       value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" id="description" class="form-control" rows="3"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            </div>
            <div class="form-group">
                <label for="keywords">Keywords (comma separated):</label>
                <input type="text" name="keywords" id="keywords" class="form-control"
                       value="<?php echo isset($_POST['keywords']) ? htmlspecialchars($_POST['keywords']) : ''; ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-lg">Submit Site</button>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
