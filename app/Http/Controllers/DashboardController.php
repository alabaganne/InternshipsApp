<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApplicationResource;
use App\Models\Application;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $user = auth()->user();

        $props = [
            'internships_count' => \App\Models\Internship::all()->count(),
            'university_supervisors_count' => \App\Models\UniversitySupervisor::all()->count(),
            'companies_count' => \App\Models\Company::all()->count(),
        ]; 

        if($user->isStudent() || $user->isCompany()) {
            $query_field = $user->isStudent() ? 'student_id' : 'company_id';
            $props['applications'] = ApplicationResource::collection(
                Application::with('internship', 'internship.company', 'internship.company.user', 'internship.company.city', 'internship.field')
                    ->where($query_field, $user->userable->id)
                    ->latest()
                    ->take(4)
                    ->get()
            );
            $props['applications_count'] = Application::where($query_field, $user->userable->id)->get()->count();
        }

        return \Inertia\Inertia::render('Dashboard', $props);
    }
}
