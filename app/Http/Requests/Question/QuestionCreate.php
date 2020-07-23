<?php

namespace App\Http\Requests\Question;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Map;
use App\Question;
use App\Rules\UniqueInRelation;

class QuestionCreate extends FormRequest
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
	public static function getRules(Map $map, String $column, Question $question)
	{
		$rules = [
			'callout' => [
				'required',
				new UniqueInRelation($map, $column, $question),
			],
			'template' => 'sometimes|required|image|max:15000',
		];

		// Makes sure the image is required if the question id does not exist, the question id will never exist when creating a NEW question.
		if (!isset($question->id)) {
			$rules['template'] = 'required|image|max:15000';
		}

		return $rules;
	}
}
