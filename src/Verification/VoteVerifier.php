<?php

namespace Azuriom\Plugin\Vote\Verification;

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
    private $retrieveIdMethod;

    /**
     * @param  string  $siteDomain
     * @return VoteVerifier
     */
    public function setSiteDomain(string $siteDomain)
    {
        $this->siteDomain = $siteDomain;
        return $this;
    }

    /**
     * @param  string  $apiUrl
     * @return VoteVerifier
     */
    public function setApiUrl(string $apiUrl)
    {
        $this->apiUrl = $apiUrl;
        return $this;
    }

    /**
     * @param  callable|string|null  $retrieveIdMethod
     * @return VoteVerifier
     */
    public function setRetrieveIdMethod($retrieveIdMethod)
    {
        $this->retrieveIdMethod = $retrieveIdMethod;
        return $this;
    }


}