<?php

namespace Azuriom\Plugin\Vote\Requests;

use Azuriom\Http\Requests\Traits\ConvertCheckbox;
use Illuminate\Foundation\Http\FormRequest;

class SiteRequest extends FormRequest
{
    use ConvertCheckbox;

    /**
     * The checkboxes attributes.
     *
     * @var array
     */
    protected $checkboxes = [
        'is_enabled',
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'url' => ['required', 'string', 'url', 'max:150'],
            'vote_delay' => ['required', 'integer', 'min:0'],
            'is_enabled' => ['filled', 'boolean'],
        ];
    }
}
