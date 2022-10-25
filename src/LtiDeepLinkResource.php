<?php

namespace BNSoftware\Lti1p3;

class LtiDeepLinkResource
{
    private $type = 'ltiResourceLink';
    private $title;
    private $text;
    private $url;
    private $line_item;
    private $icon;
    private $thumbnail;
    private $custom_params = [];
    private $target = 'iframe';
    private $html;
    private $width;
    private $height;

    public static function new(): LtiDeepLinkResource
    {
        return new LtiDeepLinkResource();
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $value): LtiDeepLinkResource
    {
        $this->type = $value;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $value): LtiDeepLinkResource
    {
        $this->title = $value;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $value): LtiDeepLinkResource
    {
        $this->text = $value;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $value): LtiDeepLinkResource
    {
        $this->url = $value;

        return $this;
    }

    public function getLineItem(): ?LtiLineitem
    {
        return $this->line_item;
    }

    public function setLineItem(?LtiLineitem $value): LtiDeepLinkResource
    {
        $this->line_item = $value;

        return $this;
    }

    public function setIcon(?LtiDeepLinkResourceIcon $icon): LtiDeepLinkResource
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): ?LtiDeepLinkResourceIcon
    {
        return $this->icon;
    }

    public function setThumbnail(?LtiDeepLinkResourceIcon $thumbnail): LtiDeepLinkResource
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function getThumbnail(): ?LtiDeepLinkResourceIcon
    {
        return $this->thumbnail;
    }

    public function getCustomParams(): array
    {
        return $this->custom_params;
    }

    public function setCustomParams(array $value): LtiDeepLinkResource
    {
        $this->custom_params = $value;

        return $this;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function setTarget(string $value): LtiDeepLinkResource
    {
        $this->target = $value;

        return $this;
    }

    public function getHtml(): string
    {
        return $this->html;
    }

    public function setHtml(string $value): LtiDeepLinkResource
    {
        $this->html = $value;

        return $this;
    }

    public function getWidth(): string
    {
        return $this->width;
    }

    public function setWidth(string $value): LtiDeepLinkResource
    {
        $this->width = $value;

        return $this;
    }

    public function getHeight(): string
    {
        return $this->height;
    }

    public function setHeight(string $value): LtiDeepLinkResource
    {
        $this->height = $value;

        return $this;
    }

    public function toArray(): array
    {
        $resource = [
            'type' => $this->type,
            'title' => $this->title,
            'text' => $this->text,
            'url' => $this->url,
            'presentation' => [
                'documentTarget' => $this->target,
            ],
        ];
        if (!empty($this->html)) {
            $resource['html'] = $this->html;
        }
        if (!empty($this->custom_params)) {
            $resource['custom'] = $this->custom_params;
        }
        if (isset($this->icon)) {
            $resource['icon'] = $this->icon->toArray();
        }
        if (isset($this->thumbnail)) {
            $resource['thumbnail'] = $this->thumbnail->toArray();
        }
        if ($this->line_item !== null) {
            $resource['lineItem'] = [
                'scoreMaximum' => $this->line_item->getScoreMaximum(),
                'label' => $this->line_item->getLabel(),
            ];
        }

//        return $resource;

        $test = [
            'type' => "link",
            'url' => "https://www.youtube.com/watch?v=corV3-WsIro",
            'embed' => [
                'html' => '<iframe width="560" height="315" src="https://www.youtube.com/embed/corV3-WsIro" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>"',
            ],
            'window' => [
                'targetName' => "youtube-corV3-WsIro",
                'windowFeatures' => "height=315,width=560,menubar=no",
            ],
            'iframe' => [
                'width' => 560,
                'height'=> 315,
                'src' => "https://www.youtube.com/embed/corV3-WsIro",
            ],
        ];

        return $test;
    }
}
