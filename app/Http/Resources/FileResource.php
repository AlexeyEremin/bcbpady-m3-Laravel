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
        $res = [
            'success' => $this->resource['success'],
            'message' => $this->resource['success'] ? 'Success' : 'File not loaded',
            'name' => $this->resource['file']['name'],
        ];
        if ($this->resource['success']) {
            $res['url'] = env('APP_URL') . 'files/' . $this->resource['file']['id'];
            $res['file_id'] = $this->resource['file']['id'];
        }
        return $res;
    }
}
