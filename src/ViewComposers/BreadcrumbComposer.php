<?php
declare(strict_types=1);

namespace N1215\Larabread\ViewComposers;

use N1215\Larabread\BreadcrumbManager;
use Illuminate\View\View;

/**
 * Class BreadcrumbComposer
 * @package N1215\Larabread\ViewComposers
 */
class BreadcrumbComposer
{
    /**
     * @var string
     */
    private $variableName;

    /**
     * @var BreadcrumbManager
     */
    private $breadcrumbManager;

    /**
     * BreadcrumbComposer constructor.
     * @param BreadcrumbManager $breadcrumbManager
     * @param array $config
     */
    public function __construct(BreadcrumbManager $breadcrumbManager, array $config)
    {
        $this->breadcrumbManager = $breadcrumbManager;
        $this->variableName = $config['variable_name'];
    }

    /**
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (!isset($view[$this->variableName])) {
            $view->with($this->variableName, $this->breadcrumbManager->get());
        }
    }
}
