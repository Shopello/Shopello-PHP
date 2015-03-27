<?php
namespace Shopello\API;

class SignUri
{
    /**
     * Sign a URI
     *
     * @param string $uri URI to Sign
     * @param string $secret Private Secret
     * @param string $paramName Name of URI Parameter to store the signature
     * @param array $params Variables to store inside of the signature
     * @return string Signed URI
     */
    public function signUri($uri, $secret, $paramName, $params)
    {
        $join = (bool) parse_url($uri, PHP_URL_QUERY) ? '&' : '?';

        return $uri.$join.$paramName.'='.$this->createSignature($secret, $params);
    }



    /**
     * Verify Signature and get Signed Data
     *
     * @param string $uri URI to Verify and Get Data from
     * @param string $secret Private Secret
     * @param string $paramName Name of URI Parameter to verify
     * @return array with valid data or false
     */
    public function verifySignature($uri, $secret, $paramName)
    {
        $getParams = array();
        parse_str(parse_url($uri, PHP_URL_QUERY), $getParams);

        if (!isset($getParams[$paramName])) {
            return false;
        }

        // Explode hash and data from eachother
        $parts = explode('.', $getParams[$paramName]);

        $data = array_pop($parts);

        // Decode the data
        $data = json_decode($this->base64UrlDecode($data));

        // Create a new signature based on the secret and data and compare it to the data
        // we're verifying, if it is correct, return the signed data.
        if ($this->createSignature($secret, $data) === $getParams[$paramName]) {
            return $data;
        }

        return false;
    }



    /**
     * Create Signature
     *
     * @param string $secret Private Secret
     * @param array $params Variables to store inside of the signature
     * @return string URI Signature
     */
    private function createSignature($secret, $params)
    {
        $tokenData = $this->base64UrlEncode(json_encode($params));

        // Get 10 last characters from the hash
        $hash = substr(hash('sha256', $secret.$tokenData), -10);

        return $hash.'.'.$tokenData;
    }



    /**
     * URL Safe base64 encode
     */
    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }



    /**
     * URL Safe base64 decode
     */
    private function base64UrlDecode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
