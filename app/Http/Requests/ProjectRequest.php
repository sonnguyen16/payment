<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $project = $this->route('project');
        
        if ($this->isMethod('POST')) {
            return $this->user()->can('create', \App\Models\Project::class);
        }
        
        return $this->user()->can('update', $project);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $projectId = $this->route('project')?->id;
        
        return [
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('projects', 'code')->ignore($projectId),
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'budget' => 'required|numeric|min:0|max:999999999.99',
            'status' => 'required|in:planning,active,completed,cancelled',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Mã dự án là bắt buộc',
            'code.unique' => 'Mã dự án đã tồn tại',
            'code.max' => 'Mã dự án không được vượt quá 20 ký tự',
            'name.required' => 'Tên dự án là bắt buộc',
            'name.max' => 'Tên dự án không được vượt quá 255 ký tự',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự',
            'budget.required' => 'Ngân sách là bắt buộc',
            'budget.numeric' => 'Ngân sách phải là số',
            'budget.min' => 'Ngân sách phải lớn hơn 0',
            'budget.max' => 'Ngân sách quá lớn',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ',
            'start_date.required' => 'Ngày bắt đầu là bắt buộc',
            'start_date.date' => 'Ngày bắt đầu không hợp lệ',
            'end_date.required' => 'Ngày kết thúc là bắt buộc',
            'end_date.date' => 'Ngày kết thúc không hợp lệ',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu',
        ];
    }
}
