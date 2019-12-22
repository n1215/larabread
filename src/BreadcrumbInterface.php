<?php
declare(strict_types=1);

namespace N1215\Larabread;

use JsonSerializable;

/**
 * Interface BreadcrumbInterface
 * @package N1215\Larabread
 */
interface BreadcrumbInterface extends JsonSerializable
{
    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @return string|null
     */
    public function getUrl(): ?string;

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function getAttribute(string $key, $default = null);
}
