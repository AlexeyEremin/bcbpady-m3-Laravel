<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Mockery\Undefined;

class GetFileResource extends JsonResource
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
            'name' => $this->resource['file']['name'],
            'url' => env('APP_URL') . 'files/' . $this->resource['file']['id'],
            'file_id' => $this->resource['file']['id'],
        ];
        if (isset($this->resource['accesses'])) {
            $res['accesses'] = new AccessResource([
                'accesses' => $this->resource['accesses'],
                'user' => $this->resource['user'],
            ]);
        }
        return $res;
    }
}
