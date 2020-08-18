<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionPicture\QuestionPictureCreate;
use App\Http\Requests\QuestionPicture\QuestionPictureUpdate;
use App\Map;
use App\Question;
use App\QuestionPicture;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionPictureController extends Controller
{
    /**
     * Check if the specified question is linked to the specified map.
     * Store a newly created question picture in storage for the specified question.
     * Then upload the image and save it in the database linked to the question picture.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Map  $map
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Map $map, Question $question)
    {
        $this->authorize('create-question-picture');

        $validatedData = Validator::make($request->all(), QuestionPictureCreate::getRules(), QuestionPictureCreate::getMessages(), QuestionPictureCreate::getAttributes())->validate();

        DB::beginTransaction();

        $newPicture = $question->pictures()->create($validatedData['picture']);

        // Save the file to the database with this helper.
        $newUpload = save_upload_to_database_HELPER($validatedData['picture']['image'], $newPicture->image());

        DB::commit();

        // When the data is saved with success and commited to the database. then move the file...
        move_upload_to_storage_HELPER($validatedData['picture']['image'], $newUpload->fresh());

        return redirect()->route('question.edit', ['map' => $map->id, 'question' => $question->id])->with('success', 'Question picture was successfully added!');
    }

    /**
     * Check if the specified question is linked to the specified map.
     * Update the specified question picture in storage for the specified question.
     * The image cannot be edited, only the question picture data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Map  $map
     * @param  \App\Question  $question
     * @param  \App\QuestionPicture  $picture
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Map $map, Question $question, QuestionPicture $picture)
    {
        $this->authorize('update-question-picture');

        $validatedData = Validator::make($request->all(), QuestionPictureUpdate::getRules($picture), QuestionPictureUpdate::getMessages(), QuestionPictureUpdate::getAttributes())->validate();

        $picture->update($validatedData['picture']);

        return redirect()->route('question.edit', ['map' => $map->id, 'question' => $question->id])->with('success', 'Question picture was successfully updated!');
    }

    /**
     * Check if the specified question is linked to the specified map.
     * Remove the specified question picture in storage for the specified question.
     *
     * @param  \App\Map  $map
     * @param  \App\Question  $question
     * @param  \App\QuestionPicture  $picture
     * @return \Illuminate\Http\Response
     */
    public function destroy(Map $map, Question $question, QuestionPicture $picture)
    {
        $this->authorize('delete-question-picture');  // OR/AND // $this->authorize('forceDelete-question-picture');

        $picture->delete();

        return redirect()->route('question.edit', ['map' => $map->id, 'question' => $question->id])->with('error', 'Question picture was deleted!');
    }

    /**
     * Check if the specified question is linked to the specified map.
     * Restore the specified question picture in storage for the specified question.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Map  $map
     * @param  \App\Question  $question
     * @param  \App\QuestionPicture  $picture
     * @return \Illuminate\Http\Response
     */
    public function restore(Map $map, Question $question, $picture_id)
    {
        $this->authorize('restore-question-picture');

        if ($question->pictures()->onlyTrashed()->where('id', $picture_id)->count() !== 1) {
            return redirect()->route('question.edit', ['map' => $map->id, 'question' => $question->id])->with('error', 'No question picture was found to restore');
        }

        $picture = $question->pictures()->onlyTrashed()->where('id', $picture_id)->first();

        $picture->restore();

        return redirect()->route('question.edit', ['map' => $map->id, 'question' => $question->id])->with('success', 'Question picture was restored!');
    }
}
