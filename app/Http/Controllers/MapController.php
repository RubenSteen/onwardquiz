<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Map;
use Inertia\Inertia;
use App\Http\Requests\Map\MapCreate;
use App\Http\Requests\Map\MapUpdate;
use DB;

class MapController extends Controller
{

    /**
     * Display a listing of the maps.
     *
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('viewAny-map');

        $maps = Map::with('template')->withCount('questions')->paginate();

        $mapsPagination = $maps->links();
        //dd($mapsPagination);
        
        $maps = $maps->map(function (Map $map) {
            $data = [
                'id' => $map->id,
                'name' => $map->name,
                'published' => $map->published,
                'created_at' => $map->created_at,
                'updated_at' => $map->updated_at,
                'hasTemplate' => false,
                'questionCount' => $map->questions_count,
            ];

            if ($map->template !== null) {
                $data['template'] = [
                    'id' => $map->template->id,
                    'name' => $map->template->name,
                    'file_name' => $map->template->file_name,
                    'location' => $map->template->getAssetFolderWithFile(),
                    'created_at' => $map->template->created_at,
                    'updated_at' => $map->template->updated_at,
                ];
                $humanReadableSize = format_bytes_HELPER($map->template->size);
                $data['hasTemplate'] = $humanReadableSize;
            }
            return $data;
        });

        return Inertia::render('Map/Index', [
            'maps' => $maps,
            'mapsPagination' => $mapsPagination,
        ]);
    }

    /**
     * Show the form for creating a new map.
     *
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $this->authorize('create-map');
        
        return Inertia::render('Map/Create');
    }

    /**
     * Store a newly created map in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $this->authorize('create-map');
        
        $validatedData = \Validator::make($request->all(), MapCreate::getRules())->validate();
            
        $newMap = Map::create([
            'name' => $validatedData['name'],
            'published' => 0,
        ]);

        return redirect()->route('map.edit', $newMap->id)->with('success', 'Map was successfully created!');
    }

    /**
     * Show the form for editing the map.
     *
     * @param  int  $map_id
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($map_id)
    {
        $this->authorize('update-map');
        
        $map = Map::with('template', 'questions')->find($map_id);

        $data = [
            'id' => $map->id,
            'name' => $map->name,
            'description' => $map->description,
            'published' => $map->published ? true : false,
        ];

        if ($map->template !== null) {
            $data['template'] = [
                'id' => $map->template->id,
                'name' => $map->template->name,
                'file_name' => $map->template->file_name,
                'location' => $map->template->getAssetFolderWithFile(),
                'created_at' => $map->template->created_at,
                'updated_at' => $map->template->updated_at,
            ];
        }

        $data['questions'] = $map->questions->map(function ($question) {
            return [
                'id' => $question->id,
                'callout' => $question->callout,
                'created_at' => $question->created_at,
                'updated_at' => $question->updated_at,
            ];
        });

        return Inertia::render('Map/Edit', [
            'map' => $data
        ]);
    }

    /**
     * Update the specified map in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $map_id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, $map_id)
    {
        $this->authorize('update-map');

        //$request->merge(['published' => $request->published == 'true' ? true : false]);

        $map = Map::find($map_id);
        
        $validatedData = \Validator::make($request->all(), MapUpdate::getRules($map))->validate();

        if (isset($validatedData['template'])) {
            DB::beginTransaction();
            
            // Method in extended controller
            $this->deleteAllOtherUploads($map);

            $map->update($validatedData);

            // Save the file to the database with this helper.
            $newUpload = save_upload_to_database_HELPER($validatedData['template'], $map->template());

            DB::commit();

            // When the data is saved with success and commited to the database. then move the file...
            move_upload_to_storage_HELPER($validatedData['template'], $newUpload->fresh());
        } else {
            $map->update($validatedData);
        }

        return redirect()->back()->with('success', 'Map was successfully updated!');
    }

    /**
     * Remove the specified map from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Map $map)
    {
        $this->authorize('delete-map'); // OR/AND // $this->authorize('forceDelete-map');

        $map->delete();

        return redirect()->route('map.index')->with('success', 'Map was deleted!');
    }

    /**
     * Restore the specified map from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function restore($map)
    {
        $this->authorize('restore-map');

        $map = tap(Map::withTrashed()->find($map))->restore();

        return redirect()->route('map.edit', $map->id)->with('success', 'Map was restored!');
    }
}
