<?php

namespace InfinityBrackets\Core;

use InfinityBrackets\Services\MasterService;
use InfinityBrackets\Services\GoogleService;

class ServiceProvider
{
    public MasterService $masterService;
    public GoogleService $googleService;

    public function __construct() {
        $this->masterService = new MasterService();
        $this->googleService = new GoogleService();
    }
}