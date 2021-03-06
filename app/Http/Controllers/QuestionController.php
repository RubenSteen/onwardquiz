<?php

namespace App\Http\Controllers;

use App\Http\Requests\Question\QuestionCreate;
use App\Http\Requests\Question\QuestionUpdate;
use App\Http\Requests\QuestionPicture\QuestionFakeAnswerCreate;
use App\Http\Requests\QuestionPicture\QuestionFakeAnswerUpdate;
use App\Map;
use App\Question;
use App\QuestionPicture;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class QuestionController extends Controller
{
    /**
     * Show the form for creating a new question for the specified map.
     *
     * @param  \App\Map $map
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return \Inertia\Response
     */
    public function create(Map $map)
    {
        $this->authorize('create-question');

        abort_if(! $map->template, 403, "The map '$map->name' needs a template");

        $map = Map::with('template')->find($map->id);

        $data = [
            'id' => $map->id,
            'description' => $map->description,
            'name' => $map->name,
            'published' => $map->published,
            'deleted_at' => $map->deleted_at,
            'created_at' => $map->created_at,
            'updated_at' => $map->updated_at,
            'template' => [
                'id' => $map->template->id,
                'name' => $map->template->name,
                'file_name' => $map->template->file_name,
                'location' => $map->template->getAssetFolderWithFile(),
                'created_at' => $map->template->created_at,
                'updated_at' => $map->template->updated_at,
            ],
        ];

        return Inertia::render('Question/Create', [
            'map' => $data,
        ]);
    }

    /**
     * Store a newly created question in storage for the specified map.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Map $map
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request, Map $map)
    {
        $this->authorize('create-question');

        abort_if(! $map->template, 403, "The map '$map->name' needs a template");

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
     * Check if the specified question is linked to the specified map.
     * Show the form for editing the specified question for the specified map.
     *
     * @param  \App\Map $map
     * @param  \App\Question $question
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return \Inertia\Response
     */
    public function edit(Map $map, Question $question)
    {
        $this->authorize('update-question');

        abort_if(! $map->template, 403, "The map '$map->name' needs a template");

        $data = [
            'id' => $question->id,
            'callout' => $question->callout,
            'published' => $question->published ? 1 : 0,
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
            'similar_question_ids' => [],
            'fakeAnswers' => [],
            'map' => [
                'id' => $map->id,
                'name' => $map->name,
                'description' => $map->description,
                'published' => $map->published,
                'deleted_at' => $map->deleted_at,
                'created_at' => $map->created_at,
                'updated_at' => $map->updated_at,
                'questions' => [],
                'template' => [
                    'id' => $map->template->id,
                    'name' => $map->template->name,
                    'file_name' => $map->template->file_name,
                    'location' => $map->template->getAssetFolderWithFile(),
                    'created_at' => $map->template->created_at,
                    'updated_at' => $map->template->updated_at,
                ],
            ],
        ];

        foreach ($question->similar_questions as $similar_question) {
            $data['similar_question_ids'][] = $similar_question->id;
        }

        foreach ($map->questions as $map_question) {
            array_push($data['map']['questions'], [
                'id' => $map_question->id,
                'callout' => $map_question->callout,
            ]);
        }

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

        foreach ($question->fakeAnswers as $fakeAnswer) {
            array_push($data['fakeAnswers'], [
                'id' => $fakeAnswer->id,
                'callout' => $fakeAnswer->callout,
            ]);
        }

        return Inertia::render('Question/Edit', [
            'question' => $data,
        ]);
    }

    /**
     * Check if the specified question is linked to the specified map.
     * Update the specified question in storage for the specified map.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Map $map
     * @param  \App\Question $question
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, Map $map, Question $question)
    {
        $this->authorize('update-question');

        abort_if(! $map->template, 403, "The map '$map->name' needs a template");

        // $request->merge(['published' => $request->published == 'true' ? true : false]);

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

        $this->unpublishMap($question);

        return redirect()->route('question.edit', ['map' => $map->id, 'question' => $question->id])->with('success', 'Question was successfully updated!');
    }

    /**
     * Check if the specified question is linked to the specified map.
     * Remove the specified question from storage for the specified map.
     *
     * @param  \App\Map $map
     * @param  \App\Question $question
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function destroy(Map $map, Question $question)
    {
        $this->authorize('delete-question'); // OR/AND // $this->authorize('forceDelete-question');

        $question->delete();

        $this->unpublishMap($question);

        return redirect()->route('map.edit', $map->id)->with('success', 'Question was deleted!');
    }

    /**
     * Check if the specified question is linked to the specified map.
     * Restore the specified question from storage for the specified map.
     *
     * @param  \App\Map $map
     * @param  \App\Question $question
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function restore(Map $map, $question_id)
    {
        $this->authorize('restore-question');

        if ($map->questions()->onlyTrashed()->where('id', $question_id)->count() !== 1) {
            return redirect()->route('map.edit', $map->id)->with('error', 'No question was found to restore');
        }

        $question = $map->questions()->onlyTrashed()->where('id', $question_id)->first();

        $question->restore();

        return redirect()->route('question.edit', ['map' => $map->id, 'question' => $question->id])->with('success', 'Question was restored!');
    }

    private function unpublishMap(Question $question)
    {
        if ($question->map->questions->where('published', true)->count() < 4 && $question->map->published == true) {
            $question->map->update(['published' => false]);
            Session::flash('warning', "{$question->map->name} has been unpublished due it not having the right amount of questions published");
        }
    }
}
