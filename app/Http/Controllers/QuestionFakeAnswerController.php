<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionFakeAnswer\QuestionFakeAnswerCreate;
use App\Map;
use App\Question;
use App\QuestionFakeAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;

class QuestionFakeAnswerController extends Controller
{
    /**
     * Check if the specified question is linked to the specified map.
     * Store a newly created question fake answer in storage for the specified question.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Map  $map
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Map $map, Question $question)
    {
        $this->authorize('create-question-fake-answer');

        $validatedData = Validator::make($request->all(), QuestionFakeAnswerCreate::getRules($question, 'question_id', new QuestionFakeAnswer()), QuestionFakeAnswerCreate::getMessages(), QuestionFakeAnswerCreate::getAttributes())->validate();

        DB::beginTransaction();

        $question->fakeAnswers()->create($validatedData['fake-answer']);

        DB::commit();

        return redirect()->route('question.edit', ['map' => $map->id, 'question' => $question->id])->with('success', 'Fake answer was successfully added to the question!');
    }
}
