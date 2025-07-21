<?php
class DomDocumentParser 
{
    private $doc;
    private $url;
    private $skippedDueTo429 = false;

    public function __construct($url) 
    {
        $this->url = $url;

        $options = array(
            'http' => array(
                'method'  => "GET",
                'header'  => "User-Agent: loogleBot/0.1\r\n"
            )
        );
        $context = stream_context_create($options);

        $response = @file_get_contents($url, false, $context);

        if (isset($http_response_header)) {
            foreach ($http_response_header as $header) {
                if (preg_match('#HTTP/\d+\.\d+\s+429#', $header)) {
                    $this->skippedDueTo429 = true;
                    $this->doc = null;
                    return; 
                }
            }
        }

        if ($response === false) {
            $this->doc = null;
            return;
        }

        $html = '<?xml encoding="UTF-8">' . $response;

        $this->doc = new DomDocument('1.0', 'utf-8');
        @$this->doc->loadHTML($html);
    }

    public function isSkippedDueTo429() 
    {
        return $this->skippedDueTo429;
    }

    public function isValid()
    {
        return $this->doc !== null;
    }

    public function getLinks() 
    {
        if (!$this->isValid()) {
            return null; // no document loaded, so no links
        }
        return $this->doc->getElementsByTagName("a");
    }

    public function getTitleTags() 
    {
        if (!$this->isValid()) {
            return null;
        }
        return $this->doc->getElementsByTagName("title");
    }

    public function getMetaTags() 
    {
        if (!$this->isValid()) {
            return null;
        }
        return $this->doc->getElementsByTagName("meta");
    }

    public function getImages() 
    {
        if (!$this->isValid()) {
            return null;
        }
        return $this->doc->getElementsByTagName("img");
    }
}
?>
