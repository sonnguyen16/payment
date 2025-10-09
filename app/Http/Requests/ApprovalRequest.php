<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $paymentRequest = $this->route('paymentRequest');
        
        // Check if approving or rejecting
        if ($this->routeIs('approvals.approve')) {
            return $this->user()->can('approve', $paymentRequest);
        }
        
        if ($this->routeIs('approvals.reject')) {
            return $this->user()->can('reject', $paymentRequest);
        }
        
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->routeIs('approvals.approve')) {
            return [
                'note' => 'nullable|string|max:500',
            ];
        }
        
        if ($this->routeIs('approvals.reject')) {
            return [
                'reason' => 'required|string|max:500',
            ];
        }
        
        return [];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'reason.required' => 'Vui lòng nhập lý do từ chối',
            'reason.max' => 'Lý do không được quá 500 ký tự',
            'note.max' => 'Ghi chú không được quá 500 ký tự',
        ];
    }
}
