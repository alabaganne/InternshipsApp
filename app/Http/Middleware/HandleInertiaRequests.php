<?php

namespace App\Http\Middleware;

use App\Http\Resources\NotificationResource;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        $user = auth()->user();

        $shared = $user ? [
            'auth' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'image' => $user->image,
					'is_admin' => $user->is_admin,
                    'userable_id' => $user->userable_id,
                    'userable_type' => $user->userable_type,
                    'likes_count' => $user->isStudent() ? $user->userable->likes->count() : null,
                    'notifications' => ($user->isStudent() || $user->isCompany()) ? [
                        'data' => NotificationResource::collection($user->notifications()->take(10)->get()),
                        'unread_count' => $user->unreadNotifications->count(),
                    ] : null,
					'messages' => User::whereHas('sentMessages', function ($query) use ($user) {
						$query->where('to_id', $user->id);
					})->orWhereHas('receivedMessages', function ($query) use ($user) {
						$query->where('from_id', $user->id);
					})
						->take(8)
						->get()
						->transform(function ($contact) use ($user) {
							$message = Message::where(function ($query) use ($contact, $user) {
								$query->where('from_id', $contact->id)
									->where('to_id', $user->id);
							})
							->orWhere(function ($query) use ($contact, $user) {
								$query->where('from_id', $user->id)
									->where('to_id', $contact->id);
							})
							->latest()
							->first();

							return [
								'id' => $message->id,
								'user_id' => $contact->id,
								'name' => $contact->name,
								'image' => $contact->image,
								'text' => $message->text,
								'created_at' => $message->created_at->calendar()
							];
						})
                ],
            ],

            'toast' => Session::has('toast') ? Session::get('toast') : null
        ] : [];

        return array_merge(parent::share($request), $shared);
    }
}
