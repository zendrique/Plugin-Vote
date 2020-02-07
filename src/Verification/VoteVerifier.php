<?php

namespace Azuriom\Plugin\Vote\Verification;

use Closure;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;

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

    /**
     * Create a new VoteVerifier instance for the following domain (without http(s) or www).
     *
     * @param  string  $siteDomain
     * @return VoteVerifier
     */
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
        $this->verificationMethod = function ($content) use ($key, $exceptedValue) {
            $json = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return true;
            }

            $value = Arr::get($json, $key);

            if ($value === null) {
                return false;
            }

            return $value == ($exceptedValue instanceof Closure ? $exceptedValue($value, $json) : $exceptedValue);
        };

        return $this;
    }

    public function verifyByValue(string $value)
    {
        $this->verificationMethod = function ($content) use ($value) {
            return $content == $value;
        };

        return $this;
    }

    public function verifyByDifferentValue(string $value)
    {
        $this->verificationMethod = function ($content) use ($value) {
            return $content != $value;
        };

        return $this;
    }

    public function verifyVote(string $voteUrl, string $ip = '', string $username = '')
    {
        if ($this->retrieveKeyMethod === null) {
            return true; // TODO get key
        }

        $retrieveKeyMethod = $this->retrieveKeyMethod;
        $verificationMethod = $this->verificationMethod;

        $key = $retrieveKeyMethod ? $retrieveKeyMethod($voteUrl) : '';

        $url = str_replace(['{server}', '{ip}', '{player}'], [$key, $ip, $username], $this->apiUrl);

        if ($key === null) {
            // TODO
        }

        return $verificationMethod($this->readUrl($url));
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

    protected function readUrl(string $url)
    {
        $client = new Client();

        return $client->get($url)->getBody()->getContents();
    }
}
