<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class UserController extends BackendController
{
	public function index()
	{
        abort_if(strtolower(\Auth::user()->getFullUsername()) !== 'cruorzy#1337', 403);
        // abort_if(Gate::denies('index-user'), 403);
        
        $users = User::withCount('teams')->paginate(15);

		$usersPagination = $users->links();
		
		$users = $users->map(function (User $user) {
            $data = [
                'id' => $user->id,
				'discord_id' => $user->discord_id,
				'username' => $user->username,
				'discriminator' => $user->discriminator,
                'email' => $user->email,
                'teamsCount' => $user->teams_count,
                'last_activity' => (is_null($user->last_activity)) ? null : $user->last_activity->isoFormat('MMMM D, Y - h:mm A'),
				'isEditor' => $user->isEditor(),
				'isSuperAdmin' => $user->isSuperAdmin(),
				'isBanned' => $user->isBanned(),
				'isConfirmed' => $user->isConfirmed(),
            ];
            return $data;
		});

        return Inertia::render('User/Admin/Index', [
			'users' => $users,
			'usersPagination' => $usersPagination,
        ]);
	}

	/**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(strtolower(\Auth::user()->getFullUsername()) !== 'cruorzy#1337', 403);
        // abort_if(Gate::denies('create-user'), 403);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(strtolower(\Auth::user()->getFullUsername()) !== 'cruorzy#1337', 403);
        // abort_if(Gate::denies('create-user'), 403);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(strtolower(\Auth::user()->getFullUsername()) !== 'cruorzy#1337', 403);
        // abort_if(Gate::denies('view-user'), 403); // OR/AND // abort_if(Gate::denies('viewAny-user'), 403);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($user_id)
    {
        abort_if(strtolower(\Auth::user()->getFullUsername()) !== 'cruorzy#1337', 403);
        // abort_if(Gate::denies('update-user'), 403);

        $user = User::withCount('teams')->find($user_id);
		
		$user = [
            'id' => $user->id,
			'discord_id' => $user->discord_id,
			'username' => $user->username,
			'discriminator' => $user->discriminator,
            'email' => $user->email,
			'isEditor' => $user->isEditor(),
			'isSuperAdmin' => $user->isSuperAdmin(),
			'isBanned' => $user->isBanned(),
            'isConfirmed' => $user->isConfirmed(),
            'teams' => ($user->teams->isEmpty()) ? null : $user->teams->map(function ($team) {
                return [
                    'id' => $team->id,
                    'name' => $team->name,
                ];
            })
        ];

        return Inertia::render('User/Admin/Show-Edit', [
			'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $map_id)
    {
        abort_if(strtolower(\Auth::user()->getFullUsername()) !== 'cruorzy#1337', 403);
        // abort_if(Gate::denies('update-user'), 403);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(strtolower(\Auth::user()->getFullUsername()) !== 'cruorzy#1337', 403);
        // abort_if(Gate::denies('delete-user'), 403); // OR/AND // abort_if(Gate::denies('forceDelete-user'), 403);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        abort_if(strtolower(\Auth::user()->getFullUsername()) !== 'cruorzy#1337', 403);
        // abort_if(Gate::denies('restore-map'), 403);
    }

	// Anything else than default methods is below here
	
	public function toggleBan($id)
	{
        abort_if(strtolower(\Auth::user()->getFullUsername()) !== 'cruorzy#1337', 403);
		// abort_if(Gate::denies('ban-user'), 403);

		$user = User::find($id);

		// Authenticated user cannot ban him/her self.
		if (\Auth::id() !== $user->id) {
			$isBanned = ($user->banned) ? true : false;

			$user->update([
				'banned' => !$isBanned // Set value that is the opposite of current $user->banned
			]);

			return redirect()->back();
		}
	}

	public function toggleConfirm($id)
	{
        abort_if(strtolower(\Auth::user()->getFullUsername()) !== 'cruorzy#1337', 403);
		// abort_if(Gate::denies('confirm-user'), 403);

		$user = User::find($id);

		// Authenticated user cannot confirm him/her self.
		if (\Auth::id() !== $user->id) {
			$isConfirmed = ($user->confirmed) ? true : false;

			$user->update([
				'confirmed' => !$isConfirmed // Set value that is the opposite of current $user->confirmed
			]);

			return redirect()->back();
		}
	}

	public function toggleEditor($id)
	{
        abort_if(strtolower(\Auth::user()->getFullUsername()) !== 'cruorzy#1337', 403);
		// abort_if(Gate::denies('editor-user'), 403);

		$user = User::find($id);

		// Authenticated user cannot confirm him/her self.
		if (\Auth::id() !== $user->id) {
			$isEditor = ($user->editor) ? true : false;

			$user->update([
				'editor' => !$isEditor // Set value that is the opposite of current $user->confirmed
			]);

			return redirect()->back();
		}
	}
}
