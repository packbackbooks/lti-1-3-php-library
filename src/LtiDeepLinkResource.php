<?php

namespace BNSoftware\Lti1p3;

class LtiDeepLinkResource
{
    private string $type = 'ltiResourceLink';
    private string $title;
    private string $text;
    private string $url;
    private ?LtiLineItem $lineItem = null;
    private ?LtiDeepLinkResourceImage $icon = null;
    private ?LtiDeepLinkResourceImage $thumbnail = null;
    private ?array $custom = null;
    private ?string $target = null;
    private ?string $html = null;
    private ?int $width = null;
    private ?int $height = null;

    /**
     * @return LtiDeepLinkResource
     */
    public static function new(): LtiDeepLinkResource
    {
        return new LtiDeepLinkResource();
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $value
     * @return LtiDeepLinkResource
     */
    public function setType(string $value): LtiDeepLinkResource
    {
        $this->type = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string|null $value
     * @return LtiDeepLinkResource
     */
    public function setTitle(string $value): LtiDeepLinkResource
    {
        $this->title = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $value
     * @return LtiDeepLinkResource
     */
    public function setText(string $value): LtiDeepLinkResource
    {
        $this->text = $value;

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
     * @param string $value
     * @return LtiDeepLinkResource
     */
    public function setUrl(string $value): LtiDeepLinkResource
    {
        $this->url = $value;

        return $this;
    }

    /**
     * @return ?LtiLineItem
     */
    public function getLineItem(): ?LtiLineItem
    {
        return $this->lineItem;
    }

    /**
     * @param LtiLineItem $value
     * @return LtiDeepLinkResource
     */
    public function setLineItem(LtiLineItem $value): LtiDeepLinkResource
    {
        $this->lineItem = $value;

        return $this;
    }

    /**
     * @return ?LtiDeepLinkResourceImage
     */
    public function getIcon(): ?LtiDeepLinkResourceImage
    {
        return $this->icon;
    }

    /**
     * @param LtiDeepLinkResourceImage $icon
     * @return LtiDeepLinkResource
     */
    public function setIcon(LtiDeepLinkResourceImage $icon): LtiDeepLinkResource
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return ?LtiDeepLinkResourceImage
     */
    public function getThumbnail(): ?LtiDeepLinkResourceImage
    {
        return $this->thumbnail;
    }

    /**
     * @param LtiDeepLinkResourceImage $thumbnail
     * @return LtiDeepLinkResource
     */
    public function setThumbnail(LtiDeepLinkResourceImage $thumbnail): LtiDeepLinkResource
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * @return ?array
     */
    public function getCustomParams(): ?array
    {
        return $this->custom;
    }

    /**
     * @param array $value
     * @return LtiDeepLinkResource
     */
    public function setCustomParams(array $value): LtiDeepLinkResource
    {
        $this->custom = $value;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getTarget(): ?string
    {
        return $this->target;
    }

    /**
     * @param string $value
     * @return LtiDeepLinkResource
     */
    public function setTarget(string $value): LtiDeepLinkResource
    {
        $this->target = $value;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getHtml(): ?string
    {
        return $this->html;
    }

    /**
     * @param string $value
     * @return LtiDeepLinkResource
     */
    public function setHtml(string $value): LtiDeepLinkResource
    {
        $this->html = $value;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getWidth(): ?string
    {
        return $this->width;
    }

    /**
     * @param string $value
     * @return LtiDeepLinkResource
     */
    public function setWidth(string $value): LtiDeepLinkResource
    {
        $this->width = $value;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getHeight(): ?string
    {
        return $this->height;
    }

    /**
     * @param string $value
     * @return LtiDeepLinkResource
     */
    public function setHeight(string $value): LtiDeepLinkResource
    {
        $this->height = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        switch ($this->type) {
            case 'ltiResourceLink':
                $resource = [
                    'type'  => "ltiResourceLink",
                    'title' => $this->title,
                    'text'  => $this->text,
                    'url'   => $this->url,
                ];
                break;
            case 'link':
                $resource = [
                    'type'  => "link",
                    'title' => $this->title,
                    'text'  => $this->text,
                    'url'   => $this->url,
                ];
                break;
            case 'iframe':
                $resource = [
                    'type'   => "link",
                    'title'  => $this->title,
                    'url'    => $this->url,
                    'embed'  => [
                        'html' => '<iframe width="' . $this->width . '" height="' . $this->height . '" '
                            . 'src="' . $this->url . '" frameborder="0" allow="autoplay; encrypted-media" '
                            . 'allowfullscreen="allowfullscreen"></iframe>"',
                    ],
                    'window' => [
                        'windowFeatures' => "height={$this->height},width={$this->width},menubar=no",
                    ],
                    'iframe' => [
                        'width'  => $this->width,
                        'height' => $this->height,
                        'src'    => $this->url,
                    ],
                ];
                break;
            case 'html':
                $resource = [
                    'type' => "html",
                    'html' => $this->html,
                ];
                break;
            case 'image':
                $resource = [
                    'type' => "image",
                    'url'  => $this->url,
                ];
                break;
            default:
                $resource = [
                    'type'         => $this->type,
                    'title'        => $this->title,
                    'text'         => $this->text,
                    'url'          => $this->url,
                    'presentation' => [
                        'documentTarget' => $this->target,
                    ],
                ];
                if ($this->html) {
                    $resource['html'] = $this->html;
                }
        }
        if ($this->custom) {
            $resource['custom'] = $this->custom;
        }
        if ($this->icon) {
            $resource['icon'] = $this->icon->toArray();
        }
        if ($this->thumbnail) {
            $resource['thumbnail'] = $this->thumbnail->toArray();
        }
        if ($this->lineItem) {
            $resource['lineItem'] = [
                'scoreMaximum' => $this->lineItem->getScoreMaximum(),
                'label'        => $this->lineItem->getLabel(),
            ];
        }
        if ($this->iframe) {
            $resource['iframe'] = $this->iframe;
        }
        if ($this->width) {
            $resource['width'] = $this->width;
        }
        if ($this->height) {
            $resource['height'] = $this->height;
        }

        return $resource;
    }
}
