<?php

namespace App\Http\Controllers;

use App\Map;
use App\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SimilarQuestionController extends Controller
{
    /**
     * Check if the specified question is linked to the specified map.
     * Attach the given request question ID to the question
     * And Attach the question ID to the request question ID
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Map  $map
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function attach(Request $request, Map $map, Question $question)
    {
        $this->authorize('attach-similar-question');

        $validatedData = $this->validateData($request, $map, $question);

        // Attaches the questions both ways
        $question->similar_questions()->attach($validatedData['similar_question']['id']);
        $question->question_is_similar_to()->attach($validatedData['similar_question']['id']);

        return redirect()->route('question.edit', ['map' => $map->id, 'question' => $question->id])->with('success', 'Similar question was successfully attached!');
    }

    /**
     * Check if the specified question is linked to the specified map.
     * Update the specified question picture in storage for the specified question.
     * The image cannot be edited, only the question picture data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Map  $map
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function detach(Request $request, Map $map, Question $question)
    {
        $this->authorize('detach-similar-question');

        $validatedData = $this->validateData($request, $map, $question, true);

        // Detaches the questions both ways
        $question->similar_questions()->detach($validatedData['similar_question']['id']);
        $question->question_is_similar_to()->detach($validatedData['similar_question']['id']);

        return redirect()->route('question.edit', ['map' => $map->id, 'question' => $question->id])->with('success', 'Similar question was successfully detached!');
    }

    /**
     * Checks if the given question ID is part of the map
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Map  $map
     * @throws \Illuminate\Validation\ValidationException
     * @return array
     */
    private function validateData(Request $request, Map $map, Question $question, $detach = false)
    {
       $rules = [
            'similar_question.id' => [
                'required',
                'integer',
            ],
        ];

        $messages = [
            'similar_question.id.not_in' => 'The selected question is already registered as similar question.',
            'similar_question.id.required' => 'The similar question id field is required.',
            'similar_question.id.integer' => 'The similar question id must be an integer.',
        ];

        if ($detach === true) {
            $rules['similar_question.id'][] = Rule::in($question->similar_questions->pluck('id')->toArray());
            $messages['similar_question.id.in'] = 'The selected similar question is registered as similar to the given question.';
        } else {
            $rules['similar_question.id'][] = Rule::in($map->questions->pluck('id')->toArray());
            $messages['similar_question.id.in'] = 'The selected question is not part of the given map.';

            $rules['similar_question.id'][] = Rule::notIn($question->similar_questions->pluck('id'));
        }



        return Validator::make($request->all(), $rules, $messages)->validate();
    }
}
