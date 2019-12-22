<?php
declare(strict_types=1);

namespace N1215\Larabread;

/**
 * Class BreadcrumbListFactory
 * @package N1215\Larabread
 */
class BreadcrumbListFactory
{
    /**
     * @param BreadcrumbInterface ...$breadcrumbs
     * @return BreadcrumbListInterface
     */
    public function make(BreadcrumbInterface ...$breadcrumbs): BreadcrumbListInterface
    {
        return new BreadcrumbList(...$breadcrumbs);
    }
}
