<?php
namespace YahooFantasySports\Authentication;

/**
 * Parent class for YahooOAuth functionality. 
 * 
 * @author Kenneth Davidson
 * @version 2.0.0
 */
abstract class YahooOAuth {
    
    /**
     * Default configuration
     */
    const DEFAULT_CONFIG = array(
        'consumerKey'       => '',
        'consumerSecret'    => '',
        'accessToken'         => '',
        'accessSecret'      => '',
        'accessSession'     => '',
        'requestToken'        => '',
        'requestSecret'     => '',
        'requestVerifier'   => '',
        'callback'          => 'oop'
    );
    
    /**
     * Current OAuth configuration
     * @var Array
     */
    protected $config;   
    
    /**
     * Instantiate the YahooOAuth Object.  Requires a supplied configuration
     * array.
     * @param type $config
     */
    public function __construct($config) {
        $this->config = array_merge(YahooOAuth::DEFAULT_CONFIG, $config);
    }
    
    /**
     * Abstract method request, performs the request using the specified 
     * endpoint.  Returns the response content as a string.
     * @param String $endpoint
     * @param String $method
     * @returns String
     */
    abstract public function request($endpoint, $method = 'GET');
    
    /**
     * Abstract method to request the verifier URL.  This URL is used to allow
     * the user to login.
     */
    abstract public function getVerifierUrl();
    
    /**
     * Refresh the access token if it's been invalidated due to time or re-login.
     * @param Boolean $useHmacSha1Sig
     * @param Boolean $passOAuthInHeader
     * @param Boolean $usePost
     */
    abstract public function refreshAccessToken($useHmacSha1Sig = true, 
            $passOAuthInHeader = true, 
            $usePost = false);
    
    /**
     * Set the access token fields.  Doing so also updates the OAuth Token
     * values.
     * @param String $token
     * @param String $secret
     * @param String $session
     */
    public function setAccessTokens($token, $secret, $session) {
        $this->config['accessToken'] = $token;
        $this->config['accessSecret'] = $secret;
        $this->config['accessSession'] = $session;       
    }

    /**
     * Set the request token fields.  If no Verifier is provided, then it
     * is set to a null value, expecting that a new verifier will be requested
     * using the new token set.
     * 
     * @param String $token
     * @param String $secret
     * @param String $verifier
     */
    public function setRequestTokens($token, $secret, $verifier = null) {
        $this->config['requestToken'] = $token;
        $this->config['requestSecret'] = $secret;
        $this->config['requestVerifier'] = $verifier;     
    }    
}