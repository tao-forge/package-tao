<?php

namespace oat\tao\controller;

class Health extends CommonModule
{
    /**
     * Simple endpoint for health checking the TAO instance.
     *
     * No need authentication.
     * The client only needs a 200 response.
     */
    public function index()
    {
        return;
    }
}
