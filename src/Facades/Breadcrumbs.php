<?php
declare(strict_types=1);

namespace N1215\Larabread\Facades;

use N1215\Larabread\BreadcrumbList;
use N1215\Larabread\BreadcrumbManager;
use Illuminate\Support\Facades\Facade;

/**
 * Class Breadcrumbs
 * @package N1215\Larabread\Facades
 *
 * @method static void set($trailKey, ...$arguments)
 * @method static BreadcrumbList make($trailKey, ...$arguments)
 * @method static BreadcrumbList get()
 *
 * @see \N1215\Larabread\BreadcrumbManager
 */
class Breadcrumbs extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BreadcrumbManager::class;
    }
}
