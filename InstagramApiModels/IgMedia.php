<?php

namespace InstagramApiModels;

class IgMedia
{
    protected string $id, $caption, $media_type, $media_url, $permalink, $thumbnail_url, $timestamp, $username;
    public const FIELDS = 'id,caption,media_type,media_url,permalink,thumbnail_url,timestamp,username';
    public const TYPE_IMAGE = "IMAGE";
    public const TYPE_VIDEO = "VIDEO";
    public const TYPE_CAROUSEL_ALBUM = "CAROUSEL_ALBUM";

    public function __construct($igMedia) {
        if(!empty($igMedia)) {
            $this->id = $igMedia->id;
            $this->caption = !empty($igMedia->caption) ? $igMedia->caption : "";
            $this->media_type = $igMedia->media_type;
            $this->media_url = $igMedia->media_url;
            $this->permalink = !empty($igMedia->permalink) ? $igMedia->permalink : "";
            $this->thumbnail_url = !empty($igMedia->thumbnail_url) ? $igMedia->thumbnail_url : "";
            $this->timestamp = $igMedia->timestamp;
            $this->username = $igMedia->username;
        }
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getCaption(): string
    {
        return $this->caption;
    }

    /**
     * @param string $caption
     */
    public function setCaption(string $caption): void
    {
        $this->caption = $caption;
    }

    /**
     * @return string
     */
    public function getMediaType(): string
    {
        return $this->media_type;
    }

    /**
     * @param string $media_type
     */
    public function setMediaType(string $media_type): void
    {
        $this->media_type = $media_type;
    }

    /**
     * @return string
     */
    public function getMediaUrl(): string
    {
        return $this->media_url;
    }

    /**
     * @param string $media_url
     */
    public function setMediaUrl(string $media_url): void
    {
        $this->media_url = $media_url;
    }

    /**
     * @return string
     */
    public function getPermalink(): string
    {
        return $this->permalink;
    }

    /**
     * @param string $permalink
     */
    public function setPermalink(string $permalink): void
    {
        $this->permalink = $permalink;
    }

    /**
     * @return string
     */
    public function getThumbnailUrl(): string
    {
        return $this->thumbnail_url;
    }

    /**
     * @param string $thumbnail_url
     */
    public function setThumbnailUrl(string $thumbnail_url): void
    {
        $this->thumbnail_url = $thumbnail_url;
    }

    /**
     * @return string
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * @param string $timestamp
     */
    public function setTimestamp(string $timestamp): void
    {
        $this->timestamp = $timestamp;
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