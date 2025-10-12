<?php

namespace App\Http\Requests;

use App\Models\PaymentRequest;
use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', PaymentRequest::class);
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
            'reason' => 'required|string|max:500',
            'expected_date' => 'required|date|after_or_equal:today',
            'priority' => 'required|in:urgent,normal',
            'project_id' => 'nullable|exists:projects,id',
            'details' => 'required|array|min:1',
            'details.*.description' => 'required|string|max:1000',
            'details.*.amount_before_tax' => 'required|numeric|min:0|max:999999999999',
            'details.*.tax_amount' => 'nullable|numeric|min:0|max:999999999999',
            'details.*.total_amount' => 'required|numeric|min:0|max:999999999999',
            'details.*.invoice_number' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Category
            'category_id.required' => 'Vui lòng chọn danh mục',
            'category_id.exists' => 'Danh mục không tồn tại',
            
            // Reason
            'reason.required' => 'Vui lòng nhập lý do',
            'reason.string' => 'Lý do phải là chuỗi ký tự',
            'reason.max' => 'Lý do không được quá 500 ký tự',
            
            // Expected date
            'expected_date.required' => 'Vui lòng chọn ngày dự kiến',
            'expected_date.date' => 'Ngày dự kiến không hợp lệ',
            'expected_date.after_or_equal' => 'Ngày dự kiến phải từ hôm nay trở đi',
            
            // Priority
            'priority.required' => 'Vui lòng chọn mức ưu tiên',
            'priority.in' => 'Mức ưu tiên không hợp lệ',
            
            // Project
            'project_id.exists' => 'Dự án không tồn tại',
            
            // Details
            'details.required' => 'Vui lòng thêm ít nhất một chi tiết thanh toán',
            'details.array' => 'Chi tiết thanh toán không hợp lệ',
            'details.min' => 'Phải có ít nhất một chi tiết thanh toán',
            
            // Details - Description
            'details.*.description.required' => 'Vui lòng nhập nội dung chi tiết',
            'details.*.description.string' => 'Nội dung chi tiết phải là chuỗi ký tự',
            'details.*.description.max' => 'Nội dung chi tiết không được quá 1000 ký tự',
            
            // Details - Amount before tax
            'details.*.amount_before_tax.required' => 'Vui lòng nhập số tiền chưa thuế',
            'details.*.amount_before_tax.numeric' => 'Số tiền chưa thuế phải là số',
            'details.*.amount_before_tax.min' => 'Số tiền chưa thuế phải lớn hơn hoặc bằng 0',
            'details.*.amount_before_tax.max' => 'Số tiền chưa thuế quá lớn',
            
            // Details - Tax amount
            'details.*.tax_amount.numeric' => 'Thuế GTGT phải là số',
            'details.*.tax_amount.min' => 'Thuế GTGT phải lớn hơn hoặc bằng 0',
            'details.*.tax_amount.max' => 'Thuế GTGT quá lớn',
            
            // Details - Total amount
            'details.*.total_amount.required' => 'Vui lòng nhập tổng tiền',
            'details.*.total_amount.numeric' => 'Tổng tiền phải là số',
            'details.*.total_amount.min' => 'Tổng tiền phải lớn hơn hoặc bằng 0',
            'details.*.total_amount.max' => 'Tổng tiền quá lớn',
            
            // Details - Invoice number
            'details.*.invoice_number.string' => 'Số hóa đơn phải là chuỗi ký tự',
            'details.*.invoice_number.max' => 'Số hóa đơn không được quá 255 ký tự',
        ];
    }
}
