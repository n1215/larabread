<?php
declare(strict_types=1);


if (! function_exists('breadcrumbs')) {
    /**
     * @return \N1215\Larabread\BreadcrumbManager
     */
    function breadcrumbs(): \N1215\Larabread\BreadcrumbManager
    {
        return \Illuminate\Container\Container::getInstance()
            ->make(\N1215\Larabread\BreadcrumbManager::class);
    }
}
