<?php

namespace Tal7aouy;

/**
 * Class Detector - Detect technologies used by a website.
 * @author Tal7aouy
 * @version 1.0.0
 * @license MIT
 */
class Detector
{
    private string $url;
    private $html;
    private $headers;
    private array $techPatterns = [];

    public function __construct(string $url)
    {
        $this->url = $this->validateUrl($url);
        $this->fetchWebsite();
        $this->load_techs();
    }

    private function validateUrl(string $url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("Invalid URL.");
        }
        return $url;
    }

    private function fetchWebsite()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
        $this->html = curl_exec($ch);
        $this->headers = curl_getinfo($ch);
        curl_close($ch);
    }

    private function load_techs()
    {
        $data = file_get_contents(__DIR__ . "/data/techs.json");
        if ($data === false) {
            throw new RuntimeException("Unable to read techs.json file.");
        }
        $this->techPatterns = json_decode($data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Error decoding JSON: " . json_last_error_msg());
        }
    }

    public function detectTechnologies()
    {
        $technologies = [];

        $techPatterns = $this->techPatterns;

        foreach ($techPatterns as $tech => $patterns) {
            if (!is_array($patterns)) {
                $patterns = [$patterns];
            }
            foreach ($patterns as $pattern) {
                if (stripos($this->html, $pattern) !== false) {
                    $technologies[] = $tech;
                    break;
                }
            }
        }

        $serverTech = $this->headers['http_code'] ? $this->headers['http_code'] : '';
        if (strpos($serverTech, 'PHP') !== false) {
            $technologies[] = 'PHP';
        } elseif (strpos($serverTech, 'ASP.NET') !== false) {
            $technologies[] = 'ASP.NET';
        }

        return array_unique($technologies);
    }
}
