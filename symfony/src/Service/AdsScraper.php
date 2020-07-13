<?php

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AdsScraper
{
    const ADS_FILE_NAME = 'ads.txt';
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function checkDomainHaveMoreThanTenLineOfAds(string $domain)
    {
        try {
            $content = $this->getAdsContentFromDomain($domain);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }

        $lines = explode(PHP_EOL, $content);
        $cleanedLines = $this->cleanLinesFromComment($lines);

        if (count($cleanedLines) > 10) {
            return "The ads.txt has more than 10 lines";
        }

        return "The ads.txt has less than 10 lines";
    }

    private function getAdsContentFromDomain(string $domain): string
    {
        $url = $this->buildAdsUrlFrom($domain);

        $response = $this->httpClient->request(
            'GET',
            $url
        );

        $statusCode = $response->getStatusCode();
        if ($statusCode === 200) {
            $content = $response->getContent();
        } else {
            throw new \Exception("The given url does not have ads.txt file");
        }

        return $content;
    }

    private function buildAdsUrlFrom(string $domain): string
    {
        $pureDomain = $this->getDomainName($domain);

        return 'https://' . $pureDomain . '/' . self::ADS_FILE_NAME;
    }

    private function getDomainName(string $domain): string
    {
        $parsedUrl = parse_url($domain);
        if(!isset($parsedUrl['host'])) {
            return trim($domain, '/');
        }

        return $parsedUrl['host'];
    }

    private function cleanLinesFromComment(array $lines): array
    {
        return array_filter($lines, function ($line) {
            return strpos($line, '#') !== 0;
        });
    }
}