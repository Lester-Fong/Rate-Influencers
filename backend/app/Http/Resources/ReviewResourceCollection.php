<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ReviewResourceCollection extends ResourceCollection
{
    public $collects = ReviewResource::class;
}
