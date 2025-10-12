<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên',
            'name.string' => 'Tên phải là chuỗi ký tự',
            'name.max' => 'Tên không được quá 255 ký tự',
            'email.required' => 'Vui lòng nhập email',
            'email.string' => 'Email phải là chuỗi ký tự',
            'email.lowercase' => 'Email phải viết thường',
            'email.email' => 'Email không hợp lệ',
            'email.max' => 'Email không được quá 255 ký tự',
            'email.unique' => 'Email đã được sử dụng',
        ];
    }
}
