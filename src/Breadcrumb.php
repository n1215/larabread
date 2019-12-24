<?php
declare(strict_types=1);

namespace N1215\Larabread;

use JsonSerializable;
use stdClass;

/**
 * Class Breadcrumb
 * @package App\Breadcrumb\ClassBased
 */
final class Breadcrumb implements JsonSerializable
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string|null
     */
    private $url;

    /**
     * @var array
     */
    private $attributes;

    /**
     * @param string $title
     * @param string|null $url
     * @param array $attributes
     */
    public function __construct(string $title, ?string $url = null, array $attributes = [])
    {
        $this->title = $title;
        $this->url = $url;
        $this->attributes = $attributes;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function getAttribute(string $key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $attributes = empty($this->attributes) ? new stdClass() : $this->attributes;
        return [
            'title' => $this->title,
            'url' => $this->url,
            'attributes' => $attributes,
        ];
    }
}
