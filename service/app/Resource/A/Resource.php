<?php

namespace Resource\A;

class Resource
{
    protected $request;

    public function setRequest(\Request $request) {
        $this->request = $request;
    }
}
