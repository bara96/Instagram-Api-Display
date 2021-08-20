<?php

/**
 * https://developers.facebook.com/docs/instagram-basic-display-api
 */

//autoload files
spl_autoload_register(function ($class) {
    include_once  $class . '.php';
});

use InstagramApiModels\CookieManager;
use InstagramApiModels\Request;
use InstagramApiModels\IgMedia;
use InstagramApiModels\IgUser;
use InstagramApiModels\InstagramApiException;


class InstagramApi
{

    protected string $clientId;
    protected string $clientSecret;
    protected string $code;
    protected string $access_token;

    protected array $scopes = array('user_profile', 'user_media');  //add instagram scopes here
    protected string $redirectUrl;

    //ACCESS TOKEN
    protected const ACCESS_TOKEN = "access_token";
    protected const EXPIRES_ON = "expires_on";

    //API URLS
    protected const API_BASEURL = "https://api.instagram.com/";
    protected const GRAPH_API_BASEURL = "https://graph.instagram.com/";

    /**
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUrl
     */
    public function __construct(string $clientId, string $clientSecret, string $redirectUrl = "")
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     */
    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * @param string $clientSecret
     */
    public function setClientSecret(string $clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return array|string[]
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * @param array|string[] $scopes
     */
    public function setScopes(array $scopes): void
    {
        $this->scopes = $scopes;
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    /**
     * @param string $redirectUrl
     */
    public function setRedirectUrl(string $redirectUrl): void
    {
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * Redirect to ig to get an authorization
     */
    public function getInstagramLoginUrl(): string
    {
        $redirectUrl = $this->redirectUrl;
        return InstagramApi::API_BASEURL . "oauth/authorize?client_id=". $this->clientId ."&redirect_uri=". $redirectUrl ."&scope=". implode(",", $this->scopes) ."&response_type=code";
    }

    /**
     * Check if user is logged in
     *
     * @return bool
     * @throws InstagramApiException
     */
    public function isLoggedIn() {
        if($this->getAccessToken() !== false)
            return true;
        else
            return false;
    }

    /**
     * Perform an authentication and set an access token
     *
     * @return bool
     */
    public function performInstagramAuthentication(): bool
    {
        try{
            //get instagram code
            $this->getAuthenticationCode();

            $this->revokeAccessToken();

            //get short token
            $this->getAuthenticationShortToken();

            //get long token
            $this->getAuthenticationLongToken();

            return true;
        }
        catch (InstagramApiException $e) {
            echo $e->getMessage();
        }
        return false;
    }

    /**
     * Perform a logout
     *
     * @return bool
     */
    public function performInstagramLogout(): bool
    {
        $this->revokeAccessToken();
        return true;
    }

    /**
     * Get the instagram authentication code
     *
     * @throws InstagramApiException
     */
    protected function getAuthenticationCode() {
        if(isset($_GET["code"])) {
            $this->code = $_GET["code"];
        }
        else
            throw new InstagramApiException("Invalid Instagram Code");
    }

    /**
     * Get a short-lived access token
     *
     * @throws InstagramApiException
     */
    protected function getAuthenticationShortToken() {
        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUrl,
            'code' => $this->code,
        ];

        $request = Request::post(InstagramApi::API_BASEURL . "oauth/access_token", $data);
        if(!empty($request)) {
            //request error
            if(isset($request->error_message)){
                throw new InstagramApiException($request->error_message);
            }

            if(!empty($request->access_token)) {
                $this->access_token = $request->access_token;
                $this->setAccessToken($this->access_token, true);
            }
            else {
                throw new InstagramApiException("Unknown Response on short-lived token request");
            }
        }
        else {
            throw new InstagramApiException("Couldn't parse the response on short-lived token request");
        }
    }

    /**
     * Get a long-lived access token
     *
     * @throws InstagramApiException
     */
    protected function getAuthenticationLongToken() {
        $data = [
            'client_secret' => $this->clientSecret,
            'grant_type' => 'ig_exchange_token',
            'access_token' => $this->access_token
        ];

        $request = Request::get(InstagramApi::GRAPH_API_BASEURL . "access_token", $data);
        if(!empty($request)) {
            //request error
            if(isset($request->error)){
                if($request->error->code == 400) {  //token already used
                    $this->revokeAccessToken();
                }
                throw new InstagramApiException($request->error->message);
            }

            //request success
            if(!empty($request->access_token)) {
                $this->access_token = $request->access_token;
                $this->setAccessToken($this->access_token);
            }
            else {
                throw new InstagramApiException("Unknown Response on long-lived token request");
            }
        }
        else {
            throw new InstagramApiException("Couldn't parse the response on long-lived token request");
        }
    }

    /**
     * Refresh current access token
     *
     * https://developers.facebook.com/docs/instagram-basic-display-api/reference/refresh_access_token
     *
     * @throws InstagramApiException
     */
    protected function getAuthenticationRefreshedToken()
    {
        $access_token = !empty($this->access_token) ? $this->access_token : CookieManager::getCookie(InstagramApi::ACCESS_TOKEN);
        if(empty($access_token)) {
            throw new InstagramApiException("Access token not set");
        }

        $data = [
            'access_token' => $access_token,
            'grant_type' => 'ig_refresh_token',
        ];

        $request = Request::get(InstagramApi::GRAPH_API_BASEURL . "refresh_access_token", $data);
        if(!empty($request)) {
            //request error
            if(isset($request->error)){
                if($request->error->code == 400) {  //token already used
                    $this->revokeAccessToken();
                }
                throw new InstagramApiException($request->error->message);
            }

            //request success
            if(!empty($request->access_token)) {
                $this->access_token = $request->access_token;
                $this->setAccessToken($this->access_token);
            }
            else {
                throw new InstagramApiException("Unknown Response on refresh token request");
            }
        }
        else {
            throw new InstagramApiException("Couldn't parse the response on refresh token request");
        }

        return $access_token;
    }

    /**
     * Set current access token
     *
     * @param $token
     * @param bool $isShort
     */
    protected function setAccessToken($token, bool $isShort = false) {
        if($isShort) {
            $expire = strtotime("+1 hour", time());
        }
        else {
            $expire = strtotime("+2 month", time());
        }
        CookieManager::setCookie(InstagramApi::EXPIRES_ON, $expire);
        CookieManager::setCookie(InstagramApi::ACCESS_TOKEN, $token);
    }

    /**
     * Get current access_token
     *
     * @return false|mixed
     * @throws InstagramApiException
     */
    protected function getAccessToken() {
        $token = !empty($this->access_token) ? $this->access_token : CookieManager::getCookie(InstagramApi::ACCESS_TOKEN);

        if(!empty($token)) {
            if($this->checkExpiration())
                return $this->getAuthenticationRefreshedToken();
            else
                return $token;
        }
        else
            return false;
    }


    /**
     *  Unset current access token
     */
    //TODO: add revoke request
    protected function revokeAccessToken() {
        $this->access_token = "";
        CookieManager::unsetCookie(InstagramApi::ACCESS_TOKEN);
        CookieManager::unsetCookie(InstagramApi::EXPIRES_ON);
    }

    /**
     * Chek if access token expiring time is less than a week
     *
     * @return bool
     */
    protected function checkExpiration() {
        $today_time = strtotime("+1 week", );
        $expire_time = CookieManager::getCookie(InstagramApi::EXPIRES_ON);
        if (!empty($expire_time) && $today_time >= $expire_time)
            return true;
        else
            return false;
    }

    /**
     * Get user profile data
     *
     * https://developers.facebook.com/docs/instagram-basic-display-api/reference/user
     *
     * @param string $userId
     * @param bool $jsonResponse
     * @return bool|mixed|string
     * @throws InstagramApiException
     */
    public function getUserProfile(string $userId = "me", bool $jsonResponse = false) {
        $access_token = $this->getAccessToken();
        if(empty($access_token)) {
            throw new InstagramApiException("Access token not set");
        }

        $data = [
            'access_token' => $this->getAccessToken(),
            'fields' => IgUser::FIELDS,
        ];

        $request = Request::get(InstagramApi::GRAPH_API_BASEURL . "$userId", $data);
        if($request !== false) {
            if(isset($request->error))
                throw new InstagramApiException($request->error->message);
            if($jsonResponse)
                return $request;
            else {
                return new IgUser($request);
            }
        }
        else {
            throw new InstagramApiException("Couldn't parse the response on refresh token request");
        }
    }

    /**
     * Get user profile data
     *
     * https://developers.facebook.com/docs/instagram-basic-display-api/reference/user/media
     *
     * @param string $userId
     * @param int $limit
     * @param bool $jsonResponse
     * @return bool|mixed|string
     * @throws InstagramApiException
     */
    public function getUserMedia($userId="me", $limit=30, bool $jsonResponse=false) {
        if(empty($this->getAccessToken())) {
            throw new InstagramApiException($this->_("Access token not set"));
        }

        $data = [
            'access_token' => $this->getAccessToken(),
            'fields' => IgMedia::FIELDS,
            'limit' => $limit
        ];

        $request = Request::get(InstagramApi::GRAPH_API_BASEURL . "$userId/media", $data);
        if($request !== false) {
            if(isset($request->error))
                throw new InstagramApiException($request->error->message);
            if($jsonResponse)
                return $request;
            else {
                $mediaArray = [];
                foreach ($request->data as $media) {
                    $mediaArray [] = new IgMedia($media);
                }
                return $mediaArray;
            }
        }
        else {
            throw new InstagramApiException("Couldn't parse the response on refresh token request");
        }
    }

    /**
     * Query the Media Children Edge
     *
     * https://developers.facebook.com/docs/instagram-basic-display-api/reference/media
     *
     * @param $id : ID of the album you want to query
     * @param bool $jsonResponse
     * @return bool|mixed|string
     * @throws InstagramApiException
     */
    public function getMedia($id, bool $jsonResponse=false) {
        if(empty($this->getAccessToken())) {
            throw new InstagramApiException($this->_("Access token not set"));
        }

        $data = [
            'access_token' => $this->getAccessToken(),
            'fields' => IgMedia::FIELDS,
        ];

        $request = Request::get(InstagramApi::GRAPH_API_BASEURL . "$id/children", $data);
        if($request !== false) {
            if(isset($request->error))
                throw new InstagramApiException($request->error->message);
            if($jsonResponse)
                return $request;
            else {
                return new IgMedia($request);
            }
        }
        else {
            throw new InstagramApiException("Couldn't parse the response on refresh token request");
        }
    }

}