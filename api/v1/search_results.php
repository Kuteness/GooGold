<?php
header("Content-Type: application/json");

include("../../config.php");

try {
    if (!isset($_GET["q"])) {
        echo json_encode([
            "status" => "failed",
            "message" => "Missing 'q' parameter."
        ]);
        exit;
    }

    $term = $_GET["q"];
    $type = isset($_GET["type"]) ? strtolower($_GET["type"]) : "search";
    $page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
    $pageSize = isset($_GET["pageSize"]) ? (int)$_GET["pageSize"] : 20;

    if ($type === "images") {
        $provider = new ImageResultsProvider($con);
    } else if($type === "news") {
        $provider = new NewsResultsProvider($con);
    } else {
        $provider = new SiteResultsProvider($con);
    }

    $response = [
        "status" => "success",
        "term" => $term,    
        "type" => $type,
        "page" => $page,
        "pageSize" => $pageSize,
        "totalResults" => $provider->getNumResults($term),
        "results" => $provider->getResultsRaw($page, $pageSize, $term)
    ];

    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo json_encode([
        "status" => "failed",
        "message" => $e->getMessage()
    ]);
}

class SiteResultsProvider
{
    private $con;

    public function __construct($con) 
    {
        $this->con = $con;
    }

    public function getNumResults($term) 
    {
        $query = $this->con->prepare("SELECT COUNT(*) as total 
            FROM sites WHERE title LIKE :term 
            OR url LIKE :term 
            OR keywords LIKE :term 
            OR description LIKE :term");

        $searchTerm = "%" . $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);
        return $row["total"];
    }

    public function getResultsRaw($page, $pageSize, $term) 
    {
        $fromLimit = ($page - 1) * $pageSize;

        $query = $this->con->prepare("SELECT * 
            FROM sites WHERE title LIKE :term 
            OR url LIKE :term 
            OR keywords LIKE :term 
            OR description LIKE :term
            ORDER BY clicks DESC
            LIMIT :fromLimit, :pageSize");

        $searchTerm = "%" . $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
        $query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
        $query->execute();

        $results = array();

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $results[] = array(
                "id" => $row["id"],
                "clicks" => $row["clicks"],
                "url" => $row["url"],
                "title" => $this->trimField($row["title"], 55),
                "description" => $this->trimField($row["description"], 230)
            );
        }

        return $results;
    }

    private function trimField($string, $characterLimit) 
    {
        $dots = strlen($string) > $characterLimit ? "..." : "";
        return substr($string, 0, $characterLimit) . $dots;
    }	
}

class ImageResultsProvider
{
    private $con;

    public function __construct($con) 
    {
        $this->con = $con;
    }

    public function getNumResults($term) 
    {
        $query = $this->con->prepare("SELECT COUNT(*) as total 
            FROM images WHERE title LIKE :term 
            OR alt LIKE :term");

        $searchTerm = "%" . $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);
        return $row["total"];
    }

    public function getResultsRaw($page, $pageSize, $term) 
    {
        $fromLimit = ($page - 1) * $pageSize;

        $query = $this->con->prepare("SELECT * 
            FROM images WHERE title LIKE :term 
            OR alt LIKE :term
            ORDER BY clicks DESC
            LIMIT :fromLimit, :pageSize");

        $searchTerm = "%" . $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
        $query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
        $query->execute();

        $results = array();

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $results[] = array(
                "id" => $row["id"],
                "clicks" => $row["clicks"],
                "siteUrl" => $row["siteUrl"],
                "imageUrl" => $row["imageUrl"],
                "title" => $this->trimField($row["title"], 60),
                "alt" => $this->trimField($row["alt"], 60)
            );
        }

        return $results;
    }

    private function trimField($string, $characterLimit) 
    {
        $dots = strlen($string) > $characterLimit ? "..." : "";
        return substr($string, 0, $characterLimit) . $dots;
    }
}

class NewsResultsProvider
{
    private $con;

    public function __construct($con) 
    {
        $this->con = $con;
    }

    public function getNumResults($term) 
    {
        $query = $this->con->prepare("SELECT COUNT(*) as total 
            FROM news 
            WHERE title LIKE :term 
            OR description LIKE :term");

        $searchTerm = "%" . $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);
        return $row["total"];
    }

    private function timeAgo($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    public function getResultsRaw($page, $pageSize, $term)
    {
        $fromLimit = ($page - 1) * $pageSize;

        $query = $this->con->prepare("SELECT * 
            FROM news 
            WHERE title LIKE :term 
            OR description LIKE :term
            LIMIT :fromLimit, :pageSize");

        $searchTerm = "%" . $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
        $query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
        $query->execute();

        $results = array();

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $publishedDateAgo = $this->timeAgo($row["publishedDate"]);

            $results[] = array(
                "id" => $row["id"],
                "url" => $row["url"],
                "title" => $this->trimField($row["title"], 100),
                "description" => $this->trimField($row["description"], 200),
                "source" => $row["source"],
                "publishedDate" => $publishedDateAgo,
                "imageUrl" => $row["imageUrl"],
                "clicks" => $row["clicks"]
            );
        }

        return $results;
    }

    private function trimField($string, $characterLimit) 
    {
        $dots = strlen($string) > $characterLimit ? "..." : "";
        return substr($string, 0, $characterLimit) . $dots;
    }
}

?>
