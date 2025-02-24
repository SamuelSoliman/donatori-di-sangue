<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsertUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'=>'required|alpha:ascii|max:50',
            'lastname'=>'required|alpha:ascii|max:50',
            'email'=>'required|email|unique:users',
            'password'=> 'required|min:8',
            'center'=>'required|alpha|exists:centers,location',
            'admin'=>'boolean'
        ];
    }
}
