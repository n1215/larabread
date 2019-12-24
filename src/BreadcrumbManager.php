<?php
declare(strict_types=1);

namespace N1215\Larabread;

use Psr\Container\ContainerInterface;
use InvalidArgumentException;

/**
 * Class BreadcrumbManager
 * @package N1215\Larabread
 */
class BreadcrumbManager
{
    /**
     * @var BreadcrumbList
     */
    private $breadcrumbs;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return BreadcrumbList|null
     */
    public function get(): ?BreadcrumbList
    {
        return $this->breadcrumbs;
    }

    /**
     * @param BreadcrumbList|array|string|callable $trailKey
     * @param mixed ...$arguments
     * @return void
     */
    public function set($trailKey, ...$arguments): void
    {
        $this->breadcrumbs = $this->make($trailKey, ...$arguments);
    }

    /**
     * @param array|string|callable $trailKey
     * @param mixed ...$arguments
     * @return BreadcrumbList
     */
    public function make($trailKey, ...$arguments): BreadcrumbList
    {
        if ($trailKey instanceof BreadcrumbList) {
            return $trailKey;
        }

        // callable class
        if (is_string($trailKey)) {
            $trail = $this->container->get($trailKey);

            if (!is_callable($trail)) {
                throw new InvalidArgumentException();
            }
            return $trail(...$arguments);
        }

        if (is_array($trailKey)) {
            $trailClassKey = $trailKey[0];
            $methodName = $trailKey[1];
            $trail = $this->container->get($trailClassKey);
            return $trail->{$methodName}(...$arguments);
        }

        if (is_callable($trailKey)) {
            return $trailKey(...$arguments);
        }

        throw new InvalidArgumentException();
    }
}
