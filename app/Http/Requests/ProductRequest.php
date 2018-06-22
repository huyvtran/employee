<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'         => 'required|max:254',
            '_name'        => 'max:254',
            'description'  => 'max:254',
            'price'        => 'required_if:haveSize,false|numeric|max:7',
            'size.*.price' => 'required_if:haveSize,true|numeric|max:7',
            'haveSize'     => 'required|boolean',
            'haveTopping'  => 'required|boolean',
            'catalogue_id' => 'required|numeric',
            'status_id'    => 'required|numeric'
        ];
    }
}
