<?php
declare(strict_types=1);

namespace N1215\Larabread;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * Class BreadcrumbList
 * @package N1215\Larabread
 */
final class BreadcrumbList implements IteratorAggregate, BreadcrumbListInterface
{
    /**
     * @var BreadcrumbInterface[]
     */
    private $breadcrumbs;

    /**
     * @param BreadcrumbInterface[] $breadcrumbs
     */
    public function __construct(BreadcrumbInterface ...$breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * @param string $title
     * @param string|null $url
     * @param array $attributes
     * @return BreadcrumbList
     */
    public function add(string $title, ?string $url = null, array $attributes = []): BreadcrumbListInterface
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

    public function get(int $index): ?BreadcrumbInterface
    {
        return $this->breadcrumbs[$index] ?? null;
    }

    /**
     * @return BreadcrumbInterface|null
     */
    public function first(): ?BreadcrumbInterface
    {
        return $this->breadcrumbs[0] ?? null;
    }

    /**
     * @return BreadcrumbInterface|null
     */
    public function last(): ?BreadcrumbInterface
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
     * @return array
     */
    public function toArray(): array
    {
        return $this->breadcrumbs;
    }

    /**
     * @return mixed|void
     */
    public function jsonSerialize()
    {
        return array_map(function (BreadcrumbInterface $breadcrumb) {
            return $breadcrumb->jsonSerialize();
        }, $this->breadcrumbs);
    }
}
