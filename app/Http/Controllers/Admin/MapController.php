<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Map;
use Inertia\Inertia;
use App\Http\Requests\Map\MapCreate;
use App\Http\Requests\Map\MapUpdate;
use DB;
use Illuminate\Support\Facades\Gate;

class MapController extends BackendController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // abort_if(Gate::denies('index-map'), 403);

        $maps = Map::with('image')->withCount('questions')->paginate();

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

            if ($map->image !== null) {
                $data['image'] = [
                    'id' => $map->image->id,
                    'name' => $map->image->name,
                    'file_name' => $map->image->file_name,
                    'location' => $map->image->getAssetFolderWithFile(),
                    'created_at' => $map->image->created_at,
                    'updated_at' => $map->image->updated_at,
                ];
                $humanReadableSize = format_bytes_HELPER($map->image->size);
                $data['hasTemplate'] = $humanReadableSize;
            }
            return $data;
        });

        return Inertia::render('Map/Admin/Index', [
            'maps' => $maps,
            'mapsPagination' => $mapsPagination,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // abort_if(Gate::denies('create-map'), 403);
        return Inertia::render('Map/Admin/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // abort_if(Gate::denies('create-map'), 403);
        
        $validatedData = \Validator::make($request->all(), MapCreate::getRules())->validate();
            
        $newMap = Map::create([
            'name' => $validatedData['name'],
            'published' => 0,
        ]);

        return redirect()->route('admin.map.edit', $newMap->id)->with('success', 'Map was successfully created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // abort_if(Gate::denies('view-map'), 403); // OR/AND // abort_if(Gate::denies('viewAny-map'), 403);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($map_id)
    {
        // abort_if(Gate::denies('update-map'), 403);
        
        $map = Map::with('image', 'questions')->find($map_id);

        $data = [
            'id' => $map->id,
            'name' => $map->name,
            'description' => $map->description,
            'published' => $map->published ? true : false,
        ];

        if ($map->image !== null) {
            $data['image'] = [
                'id' => $map->image->id,
                'name' => $map->image->name,
                'file_name' => $map->image->file_name,
                'location' => $map->image->getAssetFolderWithFile(),
                'created_at' => $map->image->created_at,
                'updated_at' => $map->image->updated_at,
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

        return Inertia::render('Map/Admin/Edit', [
            'map' => $data
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
        // abort_if(Gate::denies('update-map'), 403);

        $request->merge(['published' => $request->published == 'true' ? true : false]);

        $map = Map::find($map_id);
        
        $validatedData = \Validator::make($request->all(), MapUpdate::getRules($map))->validate();

        if (isset($validatedData['image'])) {
            DB::beginTransaction();
            
            // Method in extended controller
            $this->deleteAllOtherUploads($map);

            $map->update($validatedData);

            // Save the file to the database with this helper.
            $newUpload = save_upload_to_database_HELPER($validatedData['image'], $map->image());

            DB::commit();

            // When the data is saved with success and commited to the database. then move the file...
            move_upload_to_storage_HELPER($validatedData['image'], $newUpload->fresh());
        } else {
            $map->update($validatedData);
        }

        return redirect()->back()->with('success', 'Map was successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // abort_if(Gate::denies('delete-map'), 403); // OR/AND // abort_if(Gate::denies('forceDelete-map'), 403);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        // abort_if(Gate::denies('restore-map'), 403);
    }

    // Anything else than default methods is below here
}
