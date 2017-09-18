<?php

namespace YahooFantasySports\Authentication;

require_once 'YahoOAuth.php';

/**
 * YahooOAuth1 handles all Yahoo OAuth 1.0 network requests.  It's responsible
 * for creating and refreshing the access token, as well as making all the 
 * regular API calls.
 * 
 * Information about the Yahoo Developer Network architecture can be found at
 * https://developer.yahoo.com/fantasysports/guide/
 * which provides sample code and examples.   
 * 
 * In order to use YahooOauth1 a consumer application must be registered and 
 * the Consumer key and secret should be provided.  To register for a YDN
 * consumer key, go to the following page:
 * https://developer.yahoo.com/apps/
 * 
 * A number of helper functions were added to fix some limitations and issues
 * with the default OAuth plugin and the Yahoo OAuth services.  Information
 * on these additions can be found here:
 * https://github.com/joechung/oauth_yahoo
 * 
 * @author Kenneth Davidson
 * @version 2.0.0
 */
class YahooOAuth1 extends YahooOAuth {    
        
    /**
     * Attempts to make an OAuth request to the specified $endpoint.  This method
     * makes a single refresh, and returns either True or False, based on 
     * whether there was a successful response.  A new OAuth object is 
     * created for each request, allowing for updates and refreshes.
     * 
     * @param String $endpoint
     * @param String $method
     * @return 
     * @throws Exception
     */
    public function request($endpoint, $method = 'GET', $params = array(), $headers = array()) {          
        try {        
            $oauth = $this->createAccessOAuth();
            if (!$oauth->fetch($endpoint, $params, $method, $headers)){
                throw new Exception('Unable to perform fetch of '
                        . $endpoint . ' with error: '
                        . $oauth->getLastResponse());
            }             
        } catch (OAuthException $ex) {
            throw new Exception($ex->lastResponse);
        }

        return $oauth->getLastResponse();
    }
    
    
    /**
     * Requests the verifier URL.  Verifier URL is used by the user to supply the
     * confirmation to Yahoo! services that they are giving this application
     * rights to access data.  The verifier should be updated with setRequestVerifier
     * in order to complete.
     * 
     * @return String verifierUrl
     * @throws Exception
     */
    public function getVerifierUrl() {    
        $oauth = $this->createRequestOAuth();
        $response = $oauth->getRequestToken('https://api.login.yahoo.com/oauth/v2/get_request_token', 
                $this->config['callback'], OAUTH_HTTP_METHOD_GET);
        $this->setRequestTokens($response['oauth_token'], 
                $response['oauth_token_secret']);
        return $response['xoauth_request_auth_url'];
    }
    
    /**
     * Initializes the connection by making a request to the
     * Access token.  This requires that the requestUrl was requested and that
     * the user has completed their confirmation.
     * 
     * @param String $verifier
     * @return Array
     * @throws Exception
     */
    public function requestAccessToken($verifier, $method = 'GET') {
               
        if ($verifier) {
            $this->config['requestVerifier'] = $verifier;
        }

        $oauth = $this->createRequestOAuth();               
        $response = $this->oauth->getAccessToken('https://api.login.yahoo.com/oauth/v2/get_token', 
                NULL,                               // auth session handle
                $this->config['requestVerifier'],   // Verifier from previous login
                $method);                           // HTTP GET required 

        $this->setAccessTokens($response['oauth_token'], 
                $response['oauth_token_secret'], 
                $response['oauth_session_handle']);
        
        return $response;
    }
    
    /**
     * Refresh an access token using an expired request token.  This refresh is
     * done manually, and taken from https://github.com/joechung/oauth_yahoo/blob/master/refacctok.php
     * as a backup and test to the PECL OAuth module.
     * 
     * @param string $consumer_key obtained when you registered your app
     * @param string $consumer_secret obtained when you registered your app
     * @param string $old_access_token obtained previously
     * @param string $old_token_secret obtained previously
     * @param string $oauth_session_handle obtained previously
     * @param bool $usePost use HTTP POST instead of GET (default false)
     * @param bool $useHmacSha1Sig use HMAC-SHA1 signature (default false)
     * @return response string with token or empty array on error
     */
    function refreshAccessToken($useHmacSha1Sig = true, 
            $passOAuthInHeader = true, 
            $usePost = false) {
        
        $response = array();
        $url = 'https://api.login.yahoo.com/oauth/v2/get_token';
        $params['oauth_version'] = '1.0';
        $params['oauth_nonce'] = mt_rand();
        $params['oauth_timestamp'] = time();
        $params['oauth_consumer_key'] = $this->config['consumerKey'];
        $params['oauth_token'] = $this->config['accessToken'];
        $params['oauth_session_handle'] = $this->config['accessSession'];
        
        // Compute signature and add it to the params list.  Signature is
        // created by using the consumerSecret and accessSecret
        if ($useHmacSha1Sig) {
            $params['oauth_signature_method'] = 'HMAC-SHA1';
            $params['oauth_signature'] = $this->computeHmacSignature(
                    $usePost ? 'POST' : 'GET', 
                    $url, 
                    $params, 
                    $this->consumerSecret, 
                    $this->accessSecret);
        } 
        
        // Pass OAuth credentials in a separate header or in the query string
        if ($passOAuthInHeader) {
            $query_parameter_string = $this->oauthHttpBuildQuery($params, true);
            $header = build_oauth_header($params, "yahooapis.com");
            $headers[] = $header;
        } else {
            $query_parameter_string = $this->oauthHttpBuildQuery($params);
        }
        
        // Attempt the refresh.
        // If successful then parse the data and update the config
        // If Exception then bubble it up
        try {
            if ($usePost) {
                $request_url = $url;
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                $response = doPost($request_url, $query_parameter_string, 443, $headers);
            } else {
                $request_url = $url . ($query_parameter_string ? ('?' . $query_parameter_string) : '' );
                $response = doGet($request_url, 443, $headers);
            }           
            
            // At this point we assume the request was successful, since 
            // we would expect an Exception any other way
            list($info, $header, $body) = $response;
            $body_parsed = oauth_parse_str($body); 
            
            // Display the body parsed content to the screen.  Only used for
            // debugging and confirmation purposes.
            //print_r($body_parsed);            
            if (!array_key_exists('oauth_problem', $body_parsed)) {
                $this->setAccessTokens(rfc3986_decode($body_parsed['oauth_token']), 
                        rfc3986_decode($body_parsed['oauth_token_secret']), 
                        rfc3986_decode($body_parsed['oauth_session_handle']));                                       
            } else {
                throw new Exception('An error occurred while refreshing the token: '
                        . $body_parsed['oauth_problem']);
            }            
        } catch (Exception $ex) {
            throw $ex;
        }
        
        return $response;
    }    
    
    /**
     * Helper method used to get the access OAuth Object
     * 
     * @return \YahooFantasySports\Authentication\OAuth
     */
    private function createAccessOAuth() {
        $oauth = new OAuth($this->config['consumerKey'], 
            $this->config['consumerSecret'], 
            OAUTH_SIG_METHOD_HMACSHA1, 
            OAUTH_AUTH_TYPE_URI);
        $oauth->setToken($this->config['accessToken'], $this->config['accessSecret']);   
        return $oauth;
    }
    
    /**
     * Helper method used to get the request OAuth Object
     * 
     * @return \YahooFantasySports\Authentication\OAuth
     */
    private function createRequestOAuth() {
        $oauth = new OAuth($this->config['consumerKey'], 
            $this->config['consumerSecret'], 
            OAUTH_SIG_METHOD_HMACSHA1, 
            OAUTH_AUTH_TYPE_URI);
        $oauth->setToken($this->config['requestToken'], $this->config['requestSecret']);        
        return $oauth;
    }
    
    /**
     * Perform a GET HTTP operation.  Helper function required due to 
     * limitations of the standard OAuth refresh using the Yahoo OAuth
     * services.  Without this standard request, the refresh of token
     * fails.
     * 
     * @param String $url
     * @param int $port (optional)
     * @param array $headers an array of HTTP headers (optional)
     * @return array ($info, $header, $response) on success or empty array on error.
     */
    private function doGet($url, $port = 80, $headers = NULL) {
        
        $curl_opts = array(CURLOPT_URL => $url,
            CURLOPT_PORT => $port,
            CURLOPT_POST => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true);
        if ($headers) {
            $curl_opts[CURLOPT_HTTPHEADER] = $headers;
        }

        // Make request bubbling Exception
        return $this->do_curl($curl_opts);
    }

    /**
     * Perform a POST HTTP operation.  Helper function required due to 
     * limitations of the standard OAuth refresh using the Yahoo OAuth
     * services.  Without this standard request, the refresh of token
     * fails.
     * 
     * @param string $url
     * @param int $port (optional)
     * @param array $headers an array of HTTP headers (optional)
     * @return array ($info, $header, $response) on success
     * @throws Exception
     */
    private function doPost($url, $postbody, $port = 80, $headers = NULL) {
        
        $curl_opts = array(CURLOPT_URL => $url,
            CURLOPT_PORT => $port,
            CURLOPT_POST => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => $postbody,
            CURLOPT_RETURNTRANSFER => true);
        if ($headers) {
            $curl_opts[CURLOPT_HTTPHEADER] = $headers;
        }

        // Make request, bubbling exceptions
        return $this->do_curl($curl_opts);
    }

    /**
     * Make a curl call with given options.  Helper function required due to 
     * limitations of the standard OAuth refresh using the Yahoo OAuth
     * services.  Without this standard request, the refresh of token
     * fails.
     * 
     * @param array $curl_opts an array of options to curl
     * @return array ($info, $header, $response) on success
     * @throws Exception
     */
    private function doCurl($curl_opts) {
        $debug = false;
        $retarr = array();
        if (!$curl_opts) {
            throw new Exception("Required CURL options are missing form request");
        }

        // Open curl session
        $ch = curl_init();
        if (!$ch) {
            throw new Exception("Could not initialize CURL session.");
        }

        // Set curl options that were passed in
        curl_setopt_array($ch, $curl_opts);

        // Ensure that we receive full header
        curl_setopt($ch, CURLOPT_HEADER, true);
        if ($debug) {
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
        }

        // Send the request and get the response
        ob_start();
        $response = curl_exec($ch);
        //$curl_spew = ob_get_contents();   // Debugging purposes
        ob_end_clean();

        // Check for errors
        if (curl_errno($ch)) {
            $errno = curl_errno($ch);
            $errmsg = curl_error($ch);
            curl_close($ch);
            unset($ch);

            throw new Exception($errno . ': ' . $errmsg);
        }

        // Get information about the transfer
        $info = curl_getinfo($ch);

        // Parse out header and body
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        // Close curl session
        curl_close($ch);
        unset($ch);

        // Set return value
        array_push($retarr, $info, $header, $body);
        return $retarr;
    }

    /**
     * Build a query parameter string according to OAuth Spec.
     * @param array $params an array of query parameters
     * @return string all the query parameters properly sorted and encoded
     * according to the OAuth spec, or an empty string if params is empty.
     * @link http://oauth.net/core/1.0/#rfc.section.9.1.1
     */
    function oauthHttpBuildQuery($params, $excludeOauthParams = false) {
        $query_string = '';
        if (!empty($params)) {
            // rfc3986 encode both keys and values
            $keys = $this->rfc3986Encode(array_keys($params));
            $values = $this->rfc3986Encode(array_values($params));
            $params = array_combine($keys, $values);
            // Parameters are sorted by name, using lexicographical byte value ordering.
            // http://oauth.net/core/1.0/#rfc.section.9.1.1
            uksort($params, 'strcmp');
            // Turn params array into an array of "key=value" strings
            $kvpairs = array();
            foreach ($params as $k => $v) {
                if ($excludeOauthParams && substr($k, 0, 5) == 'oauth') {
                    continue;
                }
                if (is_array($v)) {
                    // If two or more parameters share the same name,
                    // they are sorted by their value. OAuth Spec: 9.1.1 (1)
                    natsort($v);
                    foreach ($v as $value_for_same_key) {
                        array_push($kvpairs, ($k . '=' . $value_for_same_key));
                    }
                } else {
                    // For each parameter, the name is separated from the corresponding
                    // value by an '=' character (ASCII code 61). OAuth Spec: 9.1.1 (2)
                    array_push($kvpairs, ($k . '=' . $v));
                }
            }
            // Each name-value pair is separated by an '&' character, ASCII code 38.
            // OAuth Spec: 9.1.1 (2)
            $query_string = implode('&', $kvpairs);
        }
        return $query_string;
    }

    /**
     * Parse a query string into an array.
     * @param string $query_string an OAuth query parameter string
     * @return array an array of query parameters
     * @link http://oauth.net/core/1.0/#rfc.section.9.1.1
     */
    private function oauthParseString($query_string) {
        $query_array = array();
        if (isset($query_string)) {
            // Separate single string into an array of "key=value" strings
            $kvpairs = explode('&', $query_string);
            // Separate each "key=value" string into an array[key] = value
            foreach ($kvpairs as $pair) {
                list($k, $v) = \explode('=', $pair, 2);
                // Handle the case where multiple values map to the same key
                // by pulling those values into an array themselves
                if (isset($query_array[$k])) {
                    // If the existing value is a scalar, turn it into an array
                    if (is_scalar($query_array[$k])) {
                        $query_array[$k] = array($query_array[$k]);
                    }
                    array_push($query_array[$k], $v);
                } else {
                    $query_array[$k] = $v;
                }
            }
        }
        return $query_array;
    }

    /**
     * Build an OAuth header for API calls
     * @param array $params an array of query parameters
     * @return string encoded for insertion into HTTP header of API call
     */
    private function buildOAuthHeader($params, $realm = '') {
        $header = 'Authorization: OAuth realm="' . $realm . '"';
        foreach ($params as $k => $v) {
            if (substr($k, 0, 5) == 'oauth') {
                $header .= ',' . $this->rfc3986Encode($k) 
                        . '="' . $this->rfc3986Encode($v) . '"';
            }
        }
        return $header;
    }

    /**
     * Compute an OAuth PLAINTEXT signature
     * @param string $consumer_secret
     * @param string $token_secret
     */
    private function omputePlaintextSignature($consumer_secret, $token_secret) {
        return ($consumer_secret . '&' . $token_secret);
    }

    /**
     * Compute an OAuth HMAC-SHA1 signature
     * @param string $http_method GET, POST, etc.
     * @param string $url
     * @param array $params an array of query parameters for the request
     * @param string $consumer_secret
     * @param string $token_secret
     * @return string a base64_encoded hmac-sha1 signature
     * @see http://oauth.net/core/1.0/#rfc.section.A.5.1
     */
    private function computeHmacSignature($http_method, $url, $params, $consumer_secret, $token_secret) {
        $base_string = $this->signatureBaseString($http_method, $url, $params);
        $signature_key = $this->rfc3986Encode($consumer_secret) . '&' . $this->rfc3986Encode($token_secret);
        $sig = $this->base64_encode(hash_hmac('sha1', $base_string, $signature_key, true));
        return $sig;
    }

    /**
     * Make the URL conform to the format scheme://host/path
     * @param string $url
     * @return string the url in the form of scheme://host/path
     */
    private function normalizeUrl($url) {
        $parts = parse_url($url);
        $scheme = $parts['scheme'];
        $host = $parts['host'];
        $port = array_key_exists('port', $parts) ? $parts['port'] : null;
        $path = $parts['path'];
        if (!$port) {
            $port = ($scheme == 'https') ? '443' : '80';
        }
        if (($scheme == 'https' && $port != '443') || ($scheme == 'http' && $port != '80')) {
            $host = "$host:$port";
        }
        return "$scheme://$host$path";
    }

    /**
     * Returns the normalized signature base string of this request.
     * 
     * @param string $http_method
     * @param string $url
     * @param array $params
     * @see http://oauth.net/core/1.0/#rfc.section.A.5.1
     */
    private function signatureBaseString($http_method, $url, $params) {
        // Decompose and pull query params out of the url
        $query_str = parse_url($url, PHP_URL_QUERY);
        if ($query_str) {
            $parsed_query = $this->oauthParseString($query_str);
            // merge params from the url with params array from caller
            $params = array_merge($params, $parsed_query);
        }
        // Remove oauth_signature from params array if present
        if (isset($params['oauth_signature'])) {
            unset($params['oauth_signature']);
        }
        // Create the signature base string. Yes, the $params are double encoded.
        $base_string = $this->rfc3986Encode(strtoupper($http_method)) . '&' .
                $this->rfc3986Encode($this->normalizeUrl($url)) . '&' .
                $this->rfc3986Encode($this->oauthHttpBuildQuery($params));
        return $base_string;
    }

    /**
     * Encode input per RFC 3986
     * 
     * @param string|array $raw_input
     * @return string|array properly rfc3986 encoded raw_input
     * @link http://oauth.net/core/1.0/#encoding_parameters
     */
    private function rfc3986Encode($raw_input) {
        if (is_array($raw_input)) {
            return array_map('rfc3986Encode', $raw_input);
        } else if (is_scalar($raw_input)) {
            return str_replace('%7E', '~', rawurlencode($raw_input));
        } else {
            return '';
        }
    }

    /**
     * Decode the raw input string using the rawurldecode function.
     * 
     * @param String $raw_input
     * @return String
     */
    private function rfc3986Decode($raw_input) {
        return rawurldecode($raw_input);
    }

}
