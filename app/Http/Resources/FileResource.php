<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'success' => true,
            'message' => 'Success',
            'name' => $this->resource['fileName'],
            'url' => env('APP_URL') . '/files/' . $this->resource['url'],
            'file_id' => $this->resource['file_id'],
        ];
    }
}
