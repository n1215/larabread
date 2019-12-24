<?php
declare(strict_types=1);

namespace N1215\Larabread;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * Class BreadcrumbList
 * @package N1215\Larabread
 */
final class BreadcrumbList implements IteratorAggregate, Countable, JsonSerializable
{
    /**
     * @var Breadcrumb[]
     */
    private $breadcrumbs;

    /**
     * @param Breadcrumb[] $breadcrumbs
     */
    public function __construct(Breadcrumb ...$breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * @param string $title
     * @param string|null $url
     * @param array $attributes
     * @return BreadcrumbList
     */
    public function add(string $title, ?string $url = null, array $attributes = []): BreadcrumbList
    {
        $newBreadcrumbs = array_merge($this->breadcrumbs, [new Breadcrumb($title, $url, $attributes)]);
        return new self(...$newBreadcrumbs);
    }

    /**
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->breadcrumbs);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->breadcrumbs);
    }

    /**
     * @param int $index
     * @return Breadcrumb|null
     */
    public function get(int $index): ?Breadcrumb
    {
        return $this->breadcrumbs[$index] ?? null;
    }

    /**
     * @return Breadcrumb|null
     */
    public function first(): ?Breadcrumb
    {
        return $this->breadcrumbs[0] ?? null;
    }

    /**
     * @return Breadcrumb|null
     */
    public function last(): ?Breadcrumb
    {
        $count = count($this->breadcrumbs);
        return $this->breadcrumbs[$count - 1];
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
       return count($this->breadcrumbs) === 0;
    }

    /**
     * @return Breadcrumb[]
     */
    public function toArray(): array
    {
        return $this->breadcrumbs;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return array_map(function (Breadcrumb $breadcrumb) {
            return $breadcrumb->jsonSerialize();
        }, $this->breadcrumbs);
    }
}
