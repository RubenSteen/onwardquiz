<?php

namespace App\Http\Requests\QuestionImage;

use Illuminate\Foundation\Http\FormRequest;

class QuestionImageCreate extends FormRequest
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
    public static function getRules()
    {
        return [
            'type' => 'required|string',
            'difficulty' => 'required|integer|between:1,5',
            'image' => 'required|image|max:15000',
        ];
    }
}
