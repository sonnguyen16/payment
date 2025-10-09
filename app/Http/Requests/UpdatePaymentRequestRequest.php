<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('payment_request'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0|max:999999999999',
            'description' => 'required|string|max:1000',
            'reason' => 'required|string|max:500',
            'expected_date' => 'required|date|after_or_equal:today',
            'priority' => 'required|in:urgent,normal',
            'project_id' => 'nullable|exists:projects,id',
            'update_reason' => 'required|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Vui lòng chọn loại phiếu',
            'amount.required' => 'Vui lòng nhập số tiền',
            'amount.numeric' => 'Số tiền phải là số',
            'amount.min' => 'Số tiền phải lớn hơn 0',
            'description.required' => 'Vui lòng nhập mô tả',
            'reason.required' => 'Vui lòng nhập lý do',
            'expected_date.required' => 'Vui lòng chọn ngày dự kiến',
            'expected_date.after_or_equal' => 'Ngày dự kiến phải từ hôm nay trở đi',
            'priority.required' => 'Vui lòng chọn mức ưu tiên',
            'project_id.exists' => 'Dự án không tồn tại',
            'update_reason.required' => 'Vui lòng nhập lý do chỉnh sửa',
            'update_reason.max' => 'Lý do chỉnh sửa không được quá 500 ký tự',
        ];
    }
}
