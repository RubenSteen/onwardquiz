<?php

namespace App\Observers;

use App\Question;
use Illuminate\Support\Facades\Session;

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
        //
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

        $question->fakeAnswers()->each(function ($instance) {
            $instance->delete();
        });

        $question->template()->each(function ($instance) {
            $instance->delete();
        });
    }

    /**
     * Handle the map "restoring" event.
     *
     * @param  \App\Question  $question
     * @return void
     */
    public function restoring(Question $question)
    {
        $max_seconds_between_timestamps = 5;

        $max_restore_time = $question->deleted_at->addSeconds($max_seconds_between_timestamps)->toDateTimeString();

        $template = $question->template()->onlyTrashed()->orderByDesc('deleted_at')->where('deleted_at', '<=', $max_restore_time);

        $pictures = $question->pictures()->onlyTrashed()->orderByDesc('deleted_at')->where('deleted_at', '<=', $max_restore_time);

        // Only restore ONE and the NEWEST template that is within x seconds of the map->deleted_at timestamp!
        if ($template->count() > 0) {
            $template->first()
                ->restore();
        }

        // Only restore questions that are within x seconds of the map->deleted_at timestamp!
        if ($pictures->count() > 0) {
            $pictures->each(function ($instance) {
                $instance->restore();
            });
        }
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
