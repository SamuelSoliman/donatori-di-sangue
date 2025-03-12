<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       // return parent::toArray($request);
       $role = $this->admin == 1 ?"admin":"user";
       return [
        'id'=> $this->id,
        'name'=> $this->name,
        'lastname'=> $this->lastname,
        'email'=> $this->email,
        'role'=> $role,
        'center'=>$this->center
       ];
    }
}
