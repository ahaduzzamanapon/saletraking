<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'email'           => $this->email,
            'phone'           => $this->phone,
            'employee_id'     => $this->employee_id,
            'role'            => $this->role,
            'avatar'          => $this->avatar,
            'territory'       => $this->whenLoaded('territory', fn() => [
                'id'   => $this->territory->id,
                'name' => $this->territory->name,
            ]),
            'is_working'      => (bool) $this->is_working,
            'work_started_at' => $this->work_started_at?->toIso8601String(),
            'work_ended_at'   => $this->work_ended_at?->toIso8601String(),
            'last_seen_at'    => $this->last_seen_at?->toIso8601String(),
        ];
    }
}
