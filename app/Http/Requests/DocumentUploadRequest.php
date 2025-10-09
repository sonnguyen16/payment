<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $paymentRequest = $this->route('paymentRequest');
        return $this->user()->can('uploadDocument', $paymentRequest);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240', // 10MB
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240', // 10MB each
            'type' => 'required|in:invoice,receipt,contract,other',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Vui lòng chọn file',
            'file.file' => 'File không hợp lệ',
            'file.mimes' => 'File phải là: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG',
            'file.max' => 'File không được vượt quá 10MB',
            'type.required' => 'Vui lòng chọn loại tài liệu',
            'type.in' => 'Loại tài liệu không hợp lệ',
        ];
    }
}
