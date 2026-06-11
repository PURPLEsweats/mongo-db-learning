<?php

declare(strict_types=1);

namespace App\Actions;

use Doctrine\ODM\MongoDB\DocumentManager;
use Illuminate\Http\Response;

class TestingAction
{
    public function __construct(private readonly DocumentManager $dm) {}

    public function handle(): Response
    {
        // Mess around here — $this->dm has the full DocumentManager
        $output = '';

        return response($output ?: '<p>Nothing to show yet — add code to TestingAction::handle()</p>');
    }
}
