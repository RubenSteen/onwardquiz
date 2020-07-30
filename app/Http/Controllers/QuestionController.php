<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Question;
use App\Map;
use App\QuestionPicture;
use Inertia\Inertia;
use App\Http\Requests\Question\QuestionCreate;
use App\Http\Requests\Question\QuestionUpdate;
use DB;
use Illuminate\Support\Facades\Gate;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // abort_if(Gate::denies('index-question'), 403);
        
        abort_if(! $map->image, 403, "The map '$map->name' needs a template");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Map $map)
    {
        // abort_if(Gate::denies('create-question'), 403);

        abort_if(! $map->image, 403, "The map '$map->name' needs a template");

        $map = Map::with('image')->find($map->id);

        $data = [
            'id' => $map->id,
            'description' => $map->description,
            'name' => $map->name,
            'published' => $map->published,
            'deleted_at' => $map->deleted_at,
            'created_at' => $map->created_at,
            'updated_at' => $map->updated_at,
            'image' => [
                'id' => $map->image->id,
                'name' => $map->image->name,
                'file_name' => $map->image->file_name,
                'location' => $map->image->getAssetFolderWithFile(),
                'created_at' => $map->image->created_at,
                'updated_at' => $map->image->updated_at,
            ],
        ];

        return Inertia::render('Question/Create', [
            'map' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Map $map)
    {
        // abort_if(Gate::denies('create-question'), 403);

        abort_if(! $map->image, 403, "The map '$map->name' needs a template");
        
        $validatedData = \Validator::make($request->all(), QuestionCreate::getRules($map, 'map_id', new Question()))->validate();

        $validatedData['published'] = false;

        DB::beginTransaction();

        $newQuestion = $map->questions()->create($validatedData);

        // Save the file to the database with this helper.
        $newUpload = save_upload_to_database_HELPER($validatedData['template'], $newQuestion->template());

        DB::commit();

        // When the data is saved with success and commited to the database. then move the file...
        move_upload_to_storage_HELPER($validatedData['template'], $newUpload->fresh());

        return redirect()->route('question.edit', ['map' => $map->id, 'question' => $newQuestion->id])->with('success', "Question was successfully created for the map {$map->name}!");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // abort_if(Gate::denies('view-question'), 403); // OR/AND // abort_if(Gate::denies('viewAny-question'), 403);

        abort_if(! $map->image, 403, "The map '$map->name' needs a template");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Map  $map
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function edit(Map $map, Question $question)
    {
        // abort_if(Gate::denies('update-question'), 403);

        abort_if(! $map->image, 403, "The map '$map->name' needs a template");
        $data = [
            'id' => $question->id,
            'callout' => $question->callout,
            'published' => $question->published ? true : false,
            'created_at' => $question->created_at,
            'updated_at' => $question->updated_at,
            'template' => [
                'id' => $question->template->id,
                'name' => $question->template->name,
                'file_name' => $question->template->file_name,
                'location' => $question->template->getAssetFolderWithFile(),
                'created_at' => $question->template->created_at,
                'updated_at' => $question->template->updated_at,
            ],
            'pictures' => [],
            'map' => [
                'id' => $map->id,
                'name' => $map->name,
                'description' => $map->description,
                'published' => $map->published,
                'deleted_at' => $map->deleted_at,
                'created_at' => $map->created_at,
                'updated_at' => $map->updated_at,
                'image' => [
                    'id' => $map->image->id,
                    'name' => $map->image->name,
                    'file_name' => $map->image->file_name,
                    'location' => $map->image->getAssetFolderWithFile(),
                    'created_at' => $map->image->created_at,
                    'updated_at' => $map->image->updated_at,
                ],
            ],
        ];

        foreach ($question->pictures as $picture) {
            array_push($data['pictures'], [
                'id' => $picture->id,
                'difficulty' => $picture->difficulty,
                'active' => $picture->active,
                'created_at' => $picture->created_at,
                'updated_at' => $picture->updated_at,
                'image' => [
                    'id' => $picture->image->id,
                    'name' => $picture->image->name,
                    'size' => $picture->image->size,
                    'type' => $picture->image->mime_type,
                    'file_name' => $picture->image->file_name,
                    'location' => $picture->image->getAssetFolderWithFile(),
                    'created_at' => $picture->image->created_at,
                    'updated_at' => $picture->image->updated_at,
                ],
            ]);
        }

        return Inertia::render('Question/Edit', [
            'question' => $data
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Map  $map
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Map $map, Question $question)
    {
        // abort_if(Gate::denies('update-question'), 403);

        abort_if(! $map->image, 403, "The map '$map->name' needs a template");

        $request->merge(['published' => $request->published == 'true' ? true : false]);
        
        $validatedData = \Validator::make($request->all(), QuestionUpdate::getRules($map, 'map_id', $question))->validate();

        if (isset($validatedData['template'])) {
            DB::beginTransaction();

            // Method in extended controller
            $this->deleteAllOtherUploads($question);

            $question->update($validatedData);

            // Save the file to the database with this helper.
            $newUpload = save_upload_to_database_HELPER($validatedData['template'], $question->template());

            DB::commit();

            // When the data is saved with success and commited to the database. then move the file...
            move_upload_to_storage_HELPER($validatedData['template'], $newUpload->fresh());
        } else {
            $question->update($validatedData);
        }


        return redirect()->back()->with('success', 'Question was successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // abort_if(Gate::denies('delete-question'), 403); // OR/AND // abort_if(Gate::denies('forceDelete-question'), 403);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        // abort_if(Gate::denies('restore-question'), 403);

        abort_if(! $map->image, 403, "The map '$map->name' needs a template");
    }

    // Anything else than default methods is below here

    public function imageValidation($data, $editing = false)
    {
        $rules = [
            'newOrEditPicture.difficulty' => 'required|integer',
            'newOrEditPicture.active' => 'required|boolean',
            'newOrEditPicture.picture' => 'required|image|max:15000',
        ];

        if ($editing === true) {
            unset($rules['newOrEditPicture.picture']);
        }

        return \Validator::make($data, $rules)->validate();
    }

    /**
     * Store a newly created image in the uploads table.
     * Then attach it to the question picture.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Map  $map
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function storeImage(Request $request, Map $map, Question $question)
    {
        // abort_if(Gate::denies('create-question'), 403);
        
        $rawData = [
            'newOrEditPicture' => array_merge($request->newOrEditPicture, ['active' => $request->newOrEditPicture['active'] == 'true' ? true : false])
        ];
        
        $validatedData = $this->imageValidation($rawData);

        $validatedData = $validatedData['newOrEditPicture'];

        DB::beginTransaction();

        $newOrEditPicture = $question->pictures()->create($validatedData);

        // Save the file to the database with this helper.
        $newUpload = save_upload_to_database_HELPER($validatedData['picture'], $newOrEditPicture->image());

        DB::commit();

        // When the data is saved with success and commited to the database. then move the file...
        move_upload_to_storage_HELPER($validatedData['picture'], $newUpload->fresh());


        return redirect()->back()->with('success', 'Question picture was successfully added!');
    }

    /**
     * Edit image in the uploads table if needed.
     * Then edit the question picture if needed.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Map  $map
     * @param  \App\Question  $question
     * @param  \App\QuestionPicture  $picture
     * @return \Illuminate\Http\Response
     */
    public function editImage(Request $request, Map $map, Question $question, QuestionPicture $picture)
    {
        // abort_if(Gate::denies('create-question'), 403);
        
        $rawData = [
            'newOrEditPicture' => array_merge($request->newOrEditPicture, ['active' => $request->newOrEditPicture['active'] == 'true' ? true : false])
        ];

        $validatedData = $this->imageValidation($rawData, true);

        $validatedData = $validatedData['newOrEditPicture'];

        DB::beginTransaction();

        $picture->update($validatedData);

        DB::commit();


        return redirect()->back()->with('success', 'Question picture was successfully updated!');
    }

    /**
     * Edit image in the uploads table if needed.
     * Then edit the question picture if needed.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Map  $map
     * @param  \App\Question  $question
     * @param  \App\QuestionPicture  $picture
     * @return \Illuminate\Http\Response
     */
    public function deleteImage(Request $request, Map $map, Question $question, QuestionPicture $picture)
    {
        // abort_if(Gate::denies('create-question'), 403);

        $picture->delete();

        return redirect()->back()->with('error', 'Question picture was deleted!');
    }
}
