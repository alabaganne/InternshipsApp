<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Http\Resources\InternshipResource;
use App\Models\City;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Companies/Index', [
            'companies' => Company::withCount('internships', 'companySupervisors')
				->with('city')
				->paginate(12)
				->through(function ($company) {
					return [
						'id' => $company->id,
						'name' => $company->user->name,
						'email' => $company->user->email,
						'phone_number' => $company->user->phone_number,
						'internships_count' => $company->internships_count,
						'company_supervisors_count' => $company->company_supervisors_count,
						'city' => [
							'name' => $company->city->name,
						]
					];
				}),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Companies/Edit', [
            'cities' => City::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyRequest $request)
    {
        Company::create($request->only('about', 'website', 'city_id'))->user()->save(
			User::create($request->only('name', 'email', 'phone_number'))
		);

        return Redirect::route('companies.index')->with('toast', [
            'action' => 'store',
            'message' => 'Company created successfully.'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        return Inertia::render('Companies/Show', [
            'company' => [
				'id' => $company->id,
				'name' => $company->user->name,
				'email' => $company->user->email,
				'phone_number' => $company->user->phone_number,
				'about' => $company->about,
				'website' => $company->website,
				'city' => [
					'name' => $company->city->name,
				],
				'internships' => InternshipResource::collection($company->internships()->latest()->paginate(10))
			]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
        return Inertia::render('Companies/Edit', [
            'company' => [
				'id' => $company->user->id,
				'name' => $company->user->name,
				'email' => $company->user->email,
				'phone_number' => $company->user->phone_number,
				'website' => $company->website,
				'about' => $company->about,
				'city_id' => $company->city_id,
			],
            'cities' => City::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyRequest $request, Company $company)
    {
        $company->update($request->only('about', 'website', 'city_id'));

        $company->user()->update($request->only('name', 'email', 'phone_number'));

        return Redirect::route('companies.show', $company)->with('toast', [
            'action' => 'update',
            'message' => 'Company updated successfully.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        $company->user()->delete();
        $company->delete();

        return Redirect::route('companies.index')->with('toast', [
            'action' => 'destroy',
            'message' => 'Company deleted successfully.'
        ]);
    }
}
