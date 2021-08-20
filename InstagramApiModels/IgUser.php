<?php

namespace InstagramApiModels;

class IgUser
{
    protected string $username, $account_type, $media_count;
    public const FIELDS = 'username,account_type,media_count';

    public function __construct($igMedia) {
        if(!empty($igMedia)) {
            $this->username = $igMedia->username;
            $this->account_type = !empty($igMedia->caption) ? $igMedia->caption : "";
            $this->media_count = !empty($igMedia->media_count) ? $igMedia->media_count : "";
        }
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }
}