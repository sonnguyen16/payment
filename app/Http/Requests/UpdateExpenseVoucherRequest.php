<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseVoucherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('expense_voucher'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'expense_date' => 'required|date',
            'description' => 'required|string|max:1000',
            'amount' => 'required|numeric|min:0|max:999999999999',
            'category_id' => 'required|exists:categories,id',
            'project_id' => 'nullable|exists:projects,id',
            'recipient' => 'required|string|max:255',
            'update_reason' => 'required|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Expense date
            'expense_date.required' => 'Vui lòng chọn ngày chi',
            'expense_date.date' => 'Ngày chi không hợp lệ',
            
            // Description
            'description.required' => 'Vui lòng nhập nội dung',
            'description.string' => 'Nội dung phải là chuỗi ký tự',
            'description.max' => 'Nội dung không được quá 1000 ký tự',
            
            // Amount
            'amount.required' => 'Vui lòng nhập số tiền',
            'amount.numeric' => 'Số tiền phải là số',
            'amount.min' => 'Số tiền phải lớn hơn hoặc bằng 0',
            'amount.max' => 'Số tiền quá lớn',
            
            // Category
            'category_id.required' => 'Vui lòng chọn danh mục',
            'category_id.exists' => 'Danh mục không tồn tại',
            
            // Project
            'project_id.exists' => 'Dự án không tồn tại',
            
            // Recipient
            'recipient.required' => 'Vui lòng nhập người nhận',
            'recipient.string' => 'Người nhận phải là chuỗi ký tự',
            'recipient.max' => 'Người nhận không được quá 255 ký tự',
            
            // Update reason
            'update_reason.required' => 'Vui lòng nhập lý do chỉnh sửa',
            'update_reason.string' => 'Lý do chỉnh sửa phải là chuỗi ký tự',
            'update_reason.max' => 'Lý do chỉnh sửa không được quá 500 ký tự',
        ];
    }
}
