<?php
declare(strict_types=1);

namespace N1215\Larabread;

use Countable;
use JsonSerializable;
use Traversable;

/**
 * Interface BreadcrumbListInterface
 * @package N1215\Larabread
 */
interface BreadcrumbListInterface extends Traversable, Countable, JsonSerializable
{
    /**
     * @param string $title
     * @param string|null $url
     * @param array $attributes
     * @return BreadcrumbListInterface
     */
    public function add(string $title, ?string $url = null, array $attributes = []): BreadcrumbListInterface;

    /**
     * @param int $index
     * @return BreadcrumbInterface|null
     */
    public function get(int $index): ?BreadcrumbInterface;

    /**
     * @return BreadcrumbInterface|null
     */
    public function first(): ?BreadcrumbInterface;

    /**
     * @return BreadcrumbInterface|null
     */
    public function last(): ?BreadcrumbInterface;

    /**
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * @return array
     */
    public function toArray(): array;
}
