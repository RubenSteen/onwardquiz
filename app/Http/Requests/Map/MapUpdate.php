<?php

namespace App\Http\Requests\Map;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Map\MapCreate;
use App\Rules\MoreThan4PublishedQuestionsNeeded;

class MapUpdate extends FormRequest
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
    public static function getRules($id = null, $map)
    {
        return array_merge(MapCreate::getRules($id), [
            'image' => 'sometimes|required|image|max:15000',
            'description' => 'nullable',
            'published' => [
                'required',
                'boolean',
                new MoreThan4PublishedQuestionsNeeded($map),
            ],
        ]);
    }
}
