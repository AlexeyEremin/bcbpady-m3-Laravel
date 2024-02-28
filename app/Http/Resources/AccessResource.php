<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $res[] = [
            "fullname" => (
                $this->resource['user']['first_name'] . " " .
                $this->resource['user']['last_name']
            ),
            "email" => $this->resource['user']['email'],
            'type' => 'author',
        ];
        foreach ($this->resource['accesses'] as $access) {
            $res[] = [
                "fullname" => (
                    $access->user->first_name . " " .
                    $access->user->last_name
                ),
                "email" => $access->user->email,
                'type' => 'co-author',
            ];
        }
        return $res;
    }
}
