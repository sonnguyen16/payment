<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'budget' => $this->budget,
            'spent' => $this->spent,
            'remaining_budget' => $this->remaining_budget,
            'budget_utilization_percentage' => $this->budget_utilization_percentage,
            'is_over_budget' => $this->is_over_budget,
            'status' => $this->status,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'payment_requests_count' => $this->whenCounted('paymentRequests'),
        ];
    }
}
