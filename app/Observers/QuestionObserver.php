<?php

namespace App\Observers;

use App\Question;
use Session;

class QuestionObserver
{
    /**
     * Handle the question "created" event.
     *
     * @param  \App\Question  $question
     * @return void
     */
    public function created(Question $question)
    {
        //
    }

    /**
     * Handle the question "updated" event.
     *
     * @param  \App\Question  $question
     * @return void
     */
    public function updated(Question $question)
    {
//        if ($question->map->questions->where('published', true)->count() < 4 && $question->map->published == true) {
//            $question->map->update(['published' => false]);
//            Session::flash('warning', "{$question->map->name} has been unpublished due it not having the right amount of questions published");
//        }
    }

    /**
     * Handle the question "deleted" event.
     *
     * @param  \App\Question  $question
     * @return void
     */
    public function deleted(Question $question)
    {
        if ($question->isForceDeleting()) {
            return $this->forceDeleted($question);
        }

        $question->pictures()->each(function ($instance) {
            $instance->delete();
        });

        $question->template()->each(function ($instance) {
            $instance->delete();
        });
    }

    /**
     * Handle the question "restored" event.
     *
     * @param  \App\Question  $question
     * @return void
     */
    public function restored(Question $question)
    {
        //
    }

    /**
     * Handle the question "force deleted" event.
     *
     * @param  \App\Question  $question
     * @return void
     */
    public function forceDeleted(Question $question)
    {
        // Seems like this method doesn't get called by $item->forceDelete() in controllers. So the observer method above has a if statement...
        dd('Force deleting in observer dd');
    }
}
