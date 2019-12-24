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
     * @param Breadcrumb ...$breadcrumbs
     * @return BreadcrumbList
     */
    public function make(Breadcrumb ...$breadcrumbs): BreadcrumbList
    {
        return new BreadcrumbList(...$breadcrumbs);
    }
}
