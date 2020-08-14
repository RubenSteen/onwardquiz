<?php

namespace App\Http\Requests\QuestionPicture;

use Illuminate\Foundation\Http\FormRequest;

class QuestionPictureCreate extends FormRequest
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
     * Custom message for validation.
     *
     * @return array
     */
    public function messages()
    {
        // Call the static method below
        return self::getMessages();
    }

    /**
     * Custom message for validation.
     *
     * @return array
     */
    public function attributes()
    {
        // Call the static method below
        return self::getAttributes();
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
            'picture.difficulty' => 'required|numeric|between:1,5',
            'picture.active' => 'required|boolean',
            'picture.image' => 'required|image|max:15000',
        ];
    }

    /**
     * Set the messages for the validation rules that apply to the request.
     * The reason for creating a static method is that it can be called from anywhere quite elegantly
     * Also keeping the old messages() method so laravel does not break behind the scenes.
     * @return array
     */
    public static function getMessages()
    {
        return [
            'picture.image.image' => 'The uploaded file must be an image.',
        ];
    }

    /**
     * Set the attributes for the validation rules that apply to the request.
     * The reason for creating a static method is that it can be called from anywhere quite elegantly
     * Also keeping the old attributes() method so laravel does not break behind the scenes.
     * @return array
     */
    public static function getAttributes()
    {
        return [
            'picture.difficulty' => 'difficulty',
            'picture.active' => 'active',
            'picture.image' => 'image',
        ];
    }
}
