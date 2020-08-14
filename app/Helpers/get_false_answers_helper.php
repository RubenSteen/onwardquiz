<?php

use Illuminate\Database\Eloquent\Collection;

if (! function_exists('get_false_answers_helper')) {
    function get_false_answers_helper($question, $amountOfAnswers)
    {
        $collectedAnswers = new Collection([]);
        // Goes through all given relations
        foreach ($question->getRelations() as $answers) {
            // Checks if the relation is not empty, then pushes the answers to the new collectedAnswers collection.
            if (! $answers->isEmpty()) {
                foreach ($answers as $answer) {
                    $collectedAnswers->push($answer);
                }
            }
        }

        // Makes sure there are NO duplicates in the collectedAnswers
        $collectedAnswers = $collectedAnswers->unique('callout');
        /**
         * If the collected answers is greater than the amount of wanted answers then pick the amount of wanted answers randomly
         * Else query new questions related to the map and fill them up to the collectedAnswers till there is the right amount.
         */
        if ($collectedAnswers->count() > $amountOfAnswers) {
            $collectedAnswers = $collectedAnswers->random($amountOfAnswers);
        } elseif ($collectedAnswers->count() < $amountOfAnswers) {
            $answersToFill = $question->map->questions->where('callout', '!=', $question->callout)->whereNotIn('callout', $collectedAnswers->pluck('callout')->toArray())->random(($amountOfAnswers - $collectedAnswers->count()));
            $collectedAnswers = $collectedAnswers->merge($answersToFill);
        }

        return $collectedAnswers;
    }
}
