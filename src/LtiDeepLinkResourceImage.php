<?php

namespace BNSoftware\Lti1p3;

class LtiDeepLinkResourceImage
{
    private $url;
    private $width;
    private $height;

    /**
     * @param string $url
     * @param int    $width
     * @param int    $height
     */
    public function __construct(string $url, int $width, int $height)
    {
        $this->url = $url;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @param string $url
     * @param int    $width
     * @param int    $height
     * @return LtiDeepLinkResourceImage
     */
    public static function new(string $url, int $width, int $height): LtiDeepLinkResourceImage
    {
        return new LtiDeepLinkResourceImage($url, $width, $height);
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url): LtiDeepLinkResourceImage
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param int $width
     * @return $this
     */
    public function setWidth(int $width): LtiDeepLinkResourceImage
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @param int $height
     * @return $this
     */
    public function setHeight(int $height): LtiDeepLinkResourceImage
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'url'    => $this->url,
            'width'  => $this->width,
            'height' => $this->height,
        ];
    }
}
