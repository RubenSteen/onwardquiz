<?php

namespace App\Http\Requests\FakeAnswer;

use Illuminate\Foundation\Http\FormRequest;

class FakeAnswerCreate extends FormRequest
{
    /**
     * Gets the info if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Authorization is handled in the controllers them self.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Call the static method below
        return self::getRules();
    }
    
    /**
     * Set the validation rules that apply to the request.
     * The reason for creating a static method is that it can be called from anywhere quite elegantly
     * Also keeping the old rules() method so laravel does not break behind the scenes.
     * @return array
     */
    public static function getRules($id = null)
    {
        return [
            // !Have to make it so, that this callout cannot be created if its the same to the question callout or AT ALL exists in the questions from the chosen map
            // !Also when a new question gets made with a callout that exists in FakeAnswers then automaticly delete that one
            'callout' => 'required|string',
        ];
    }
}
