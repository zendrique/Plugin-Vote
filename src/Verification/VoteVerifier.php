<?php

namespace Azuriom\Plugin\Vote\Verification;

use GuzzleHttp\Client;

class VoteVerifier
{
    /**
     * The domain (without www) of this site.
     *
     * @var string
     */
    private $siteDomain;

    /**
     * The api url of this site.
     *
     * @var string
     */
    private $apiUrl;

    /**
     * The method to verify is a user voted on this site.
     *
     * @var string|callable
     */
    private $verificationMethod;

    /**
     * The method to retrieve the server id from the vote url.
     *
     * @var string|callable|null
     */
    private $retrieveKeyMethod;

    private function __construct(string $siteDomain)
    {
        $this->siteDomain = $siteDomain;
    }

    public static function for(string $siteDomain)
    {
        return new self($siteDomain);
    }

    /**
     * Set the API verification url for this vote site.
     *
     * @param  string  $apiUrl
     * @return VoteVerifier
     */
    public function setApiUrl(string $apiUrl)
    {
        $this->apiUrl = $apiUrl;

        return $this;
    }

    public function retrieveKeyByRegex(string $regex, int $index = 1)
    {
        $this->retrieveKeyMethod = function ($url) use ($regex, $index) {
            if (! preg_match($regex, $url, $matches)) {
                return null;
            }

            return $matches[$index];
        };

        return $this;
    }

    public function retrieveKeyByDynamically(callable $method)
    {
        $this->retrieveKeyMethod = $method;
    }

    public function verifyByJson(string $key, $exceptedValue)
    {
        $this->verificationMethod = function ($ip, $userName) use ($key, $exceptedValue) {
            $content = $this->readUrl($this->apiUrl, $ip, $userName);
            $json = json_decode($content, true);

            if (json_last_error()) {
                return true;
            }

            return array_key_exists($key, $json) && $json[$key] === $exceptedValue;
        };

        return $this;
    }

    public function verifyByValue(string $value)
    {
        $this->verificationMethod = function ($ip, $userName) use ($value) {
            return $this->readUrl($this->apiUrl, $ip, $userName) == $value;
        };

        return $this;
    }

    public function verifyByDifferentValue(string $value)
    {
        $this->verificationMethod = function ($ip, $userName) use ($value) {
            return $this->readUrl($this->apiUrl, $ip, $userName) != $value;
        };

        return $this;
    }

    public function verifyVote($ip, $username)
    {
        $method = $this->verificationMethod;

        return $method($ip, $username);
    }

    public function needManualServerId()
    {
        return is_string($this->retrieveKeyMethod);
    }

    public function verificationTypeKey()
    {
        return $this->retrieveKeyMethod;
    }

    public function getSiteDomain()
    {
        return $this->siteDomain;
    }

    protected function readUrl(string $url, string $ip = '0.0.0.0', string $name = '')
    {
        $client = new Client();

        $fullUrl = str_replace(['{player}', '{ip}'], [$ip, $name], $url);

        return $client->get($fullUrl)->getBody()->getContents();
    }
}
