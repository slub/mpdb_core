<?php

namespace Slub\MpdbCore\Common;

use Illuminate\Support\Collection as IlluminateCollection;

class Collection extends IlluminateCollection
{
    public function recursive(): Collection
    {
	return $this->map( function($item) {
	    if (is_array($item)) {
		return Collection::wrap($item)->recursive();
	    }
	    return $item;
	});
    }
}
