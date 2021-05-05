<?php

namespace App\Http\Resources;

use App\Models\Application;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class InternshipResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
		$user = auth()->user();
		$student = $user->isStudent() ? $user->userable : null;

		$application = $student ? Application::where('student_id', $student->id)
									->where('internship_id', $this->id)
									->first() : null;

        return [
			'id' => $this->id,
			'title' => $this->title,
			'description' => $this->description,
			'closing_at' => $this->closing_at->format('F d, Y'),
			'created_at' => $this->created_at->diffForHumans(),
			'company' => [
				'name' => $this->company->user->name
			],
			'city' => [
				'name' => $this->city->name,
			],
			'field' => [
				'name' => $this->field->name,
			],
			'application' => $application ? [
				'created_at' => $application->created_at->format('F d, Y')
			] : null,
			'liked' => $student ? $student->likes->contains($this->id) : null,
		];
    }
}
