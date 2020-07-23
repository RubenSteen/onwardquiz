<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Team;
use Inertia\Inertia;
use App\Http\Requests\Team\TeamCreate;
use App\Http\Requests\Team\TeamUpdate;
use DB;
use Illuminate\Support\Facades\Gate;

class TeamController extends BackendController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // abort_if(Gate::denies('index-team'), 403);

        $teams = Team::with('image')->withCount('users')->paginate();

        $teamsPagination = $teams->links();

		$teams = $teams->map(function (Team $team) {
            $data = [
                'id' => $team->id,
                'name' => $team->name,
                'membersCount' => $team->users_count,
                'created_at' => $team->created_at,
                'updated_at' => $team->updated_at,
                'hasImage' => false,
            ];

            if($team->image !== null) {
                $data['image'] = [
                    'id' => $team->image->id,
                    'name' => $team->image->name,
                    'file_name' => $team->image->file_name,
                    'location' => $team->image->getAssetFolderWithFile(),
                    'created_at' => $team->image->created_at,
                    'updated_at' => $team->image->updated_at,
                ];
                $humanReadableSize = format_bytes_HELPER($team->image->size);
                $data['hasTemplate'] = "Yes ($humanReadableSize)";
            }
            return $data;
        });

        return Inertia::render('Team/Admin/Index', [
            'teams' => $teams,
            'teamsPagination' => $teamsPagination,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // abort_if(Gate::denies('create-team'), 403);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // abort_if(Gate::denies('create-team'), 403);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // abort_if(Gate::denies('view-team'), 403); // OR/AND // abort_if(Gate::denies('viewAny-team'), 403);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($team_id)
    {
        // abort_if(Gate::denies('update-team'), 403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $team_id)
    {
        // abort_if(Gate::denies('update-team'), 403);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // abort_if(Gate::denies('delete-team'), 403); // OR/AND // abort_if(Gate::denies('forceDelete-team'), 403);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        // abort_if(Gate::denies('restore-team'), 403);
    }

    // Anything else than default methods is below here
}
