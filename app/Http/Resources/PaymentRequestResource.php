<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentRequestResource extends JsonResource
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
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'amount' => $this->amount,
            'description' => $this->description,
            'reason' => $this->reason,
            'expected_date' => $this->expected_date?->format('Y-m-d'),
            'priority' => $this->priority->value,
            'priority_label' => $this->priority->label(),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'rejection_reason' => $this->rejection_reason,
            'payment_code' => $this->payment_code,
            'paid_at' => $this->paid_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            
            // Relationships
            'user' => new UserResource($this->whenLoaded('user')),
            'project' => $this->whenLoaded('project', fn() => [
                'id' => $this->project->id,
                'code' => $this->project->code,
                'name' => $this->project->name,
            ]),
            'current_approver' => new UserResource($this->whenLoaded('currentApprover')),
            'documents' => $this->whenLoaded('documents'),
            'approval_histories' => $this->whenLoaded('approvalHistories', function() {
                return $this->approvalHistories->map(function($history) {
                    return [
                        'id' => $history->id,
                        'action' => $history->action->value,
                        'action_label' => $history->action->label(),
                        'from_status' => $history->from_status,
                        'to_status' => $history->to_status,
                        'reason' => $history->reason,
                        'changes' => $history->changes,
                        'created_at' => $history->created_at->format('Y-m-d H:i:s'),
                        'user' => [
                            'id' => $history->user->id,
                            'name' => $history->user->name,
                        ],
                    ];
                });
            }),
        ];
    }
}
