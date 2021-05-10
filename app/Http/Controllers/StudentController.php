<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Models\User;
use App\Models\Student;
use App\Models\Field;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Students/Index', [
            'students' => Student::with('field')
                    ->latest()
                    ->where(function ($query) {
                        if (auth()->user()->isStudent()) {
                            $query->where('id', '<>', auth()->user()->userable->id);
                        }
                    })
                    ->paginate(12)
					->through(function ($student) {
						return [
							'id' => $student->id,
							'user_id' => $student->user->id,
							'name' => $student->user->name,
							'email' => $student->user->email,
							'image' => $student->user->image,
							'field' => [
								'name' => $student->field->name
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
        return Inertia::render('Students/Edit', [
            'fields' => Field::select('id', 'name')->get(),
            'cities' => City::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StudentRequest $request)
    {
        Student::create($request->only('about', 'field_id', 'city_id'))->user()->save(
            User::create($request->only('name', 'email', 'phone_number'))
        );

        return Redirect::route('students.index')->with('toast', [
            'action' => 'store',
            'message' => 'Student created successfully.'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        return Inertia::render('Students/Show', [
            'student' => [
				'id' => $student->id,
				'about' => $student->about,
				'linkedin_profile_url' => $student->linkedin_profile_url,
				'user_id' => $student->user->id,
				'name' => $student->user->name,
				'email' => $student->user->email,
				'phone_number' => $student->user->phone_number,
				'image' => $student->user->image,
				'field' => [
					'name' => $student->field->name,
				],
				'city' => [
					'name' => $student->city->name
				]
			]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit(Student $student)
    {
        return Inertia::render('Students/Edit', [
            'student' => [
				'id' => $student->id,
				'name' => $student->user->name,
				'email' => $student->user->email,
				'phone_number' => $student->user->phone_number,
				'about' => $student->about,
				'field_id' => $student->field_id,
				'city_id' => $student->city_id,
			],
            'fields' => Field::select('id', 'name')->get(),
            'cities' => City::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(StudentRequest $request, Student $student)
    {
        $student->update($request->only('about', 'field_id', 'city_id'));

        $student->user()->update($request->only('name', 'email', 'phone_number'));

        return Redirect::route('students.show', $student)->with('toast', [
            'action' => 'update',
            'message' => 'Student updated successfully.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Student $student)
    {
        $student->user()->delete();
        $student->delete();

        return Redirect::route('students.index')->with('toast', [
            'action' => 'destroy',
            'message' => 'Student deleted successfully.'
        ]);
    }
}
