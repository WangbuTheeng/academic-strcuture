<?php

namespace App\View\Composers;

use App\Services\SchoolContextService;
use Illuminate\View\View;

class SchoolContextComposer
{
    protected $schoolContextService;

    public function __construct(SchoolContextService $schoolContextService)
    {
        $this->schoolContextService = $schoolContextService;
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        $schoolContext = $this->schoolContextService->getSchoolContextInfo();
        $schoolBranding = $this->schoolContextService->getSchoolBranding();
        
        $view->with([
            'schoolContext' => $schoolContext,
            'schoolBranding' => $schoolBranding,
            'currentSchool' => $this->schoolContextService->getCurrentSchool(),
            'isSuperAdmin' => $this->schoolContextService->isSuperAdmin(),
        ]);
    }
}
