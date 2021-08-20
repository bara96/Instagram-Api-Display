<?php

namespace InstagramApiModels;

class CookieManager
{
    /**
     * Set a cookie value
     *
     * @param $name
     * @param $value
     * @param float|int $expires
     */
    public static function setCookie($name, $value, $expires = (60*60*24*30)) { //default 30 days
        setcookie($name, $value, time() + $expires, "/");
    }

    /**
     * Get a cookie value
     *
     * @param $name
     * @return mixed|null
     */
    public static function getCookie($name) { //default 30 days
        if(isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        } else {
            return null;
        }
    }

    /**
     * Unset a cookie value
     *
     * @param $name
     * @return void|null
     */
    public static function unsetCookie($name) {
        setcookie($name, null, time() - 3600, "/");    // set the expiration date to one hour ago
    }
}