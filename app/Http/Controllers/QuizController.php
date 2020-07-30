<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use App\Map;
use App\Question;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // abort_if(Gate::denies('index-quiz'), 403);

        $maps = Map::with(['image'])
            ->where('published', '=', true)
            ->get();

        $maps = $maps->map(function ($map) {
            return [
                'id' => $map->id,
                'name' => $map->name,
                'description' => $map->description,
                'questions_count' => $map->questions->where('published', true)->count(),
                'image' => $map->image->getAssetFolderWithFile(),
            ];
        });

        return Inertia::render('Quiz/Index', [
            'maps' => $maps,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Map $map)
    {
        // abort_if(Gate::denies('view-quiz'), 403);

        return Inertia::render('Quiz/Show', [
            'map' => $map,
        ]);
    }

    // Anything else than default methods is below here

    /**
     * DGet a random question for the specific map
     *
     * @param  \App\Map  $map
     */
    public function getQuestion(Map $map)
    {
        // abort_if(Gate::denies('view-quiz'), 403);

        $data = [];

        $possible_question_images = collect();

        if (request()->wantsJson()) {
            $question = $map->questions->random(1)->load('template', 'pictures', 'pictures.image')->first();

            $answers = $map->questions->where('id', '!=', $question->id)->where('published', true)->random(3);

            $possible_question_images->push($question->template->getAssetFolderWithFile()); // Feed the template image to the collection

            // Add all other pictures to the collection
            foreach ($question->pictures->where('active', true) as $picture) {
                $possible_question_images->push($picture->image->getAssetFolderWithFile());
            }

            $data['question'] = [
                'id' => $question->id,
                'image' => $possible_question_images->random(1)->first(), // Get one random picure to be shown
            ];

            $data['answers'][] = [
                'id' => $question->id,
                'callout' => $question->callout,
            ];

            foreach ($answers as $answer) {
                $data['answers'][] = [
                    'id' => $answer->id,
                    'callout' => $answer->callout,
                ];
            }

            shuffle($data['answers']);

            return response()->json($data);
        }

        return abort(500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkAnswer(Map $map, Question $question, Request $request)
    {
        // abort_if(Gate::denies('view-quiz'), 403);

        if (request()->wantsJson()) {
            return $question->id;
        }

        return abort(500);
    }
}
