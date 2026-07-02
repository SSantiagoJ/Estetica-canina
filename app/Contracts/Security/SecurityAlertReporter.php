<?php

namespace App\Contracts\Security;

use App\Models\Usuario;
use Illuminate\Http\Request;

interface SecurityAlertReporter
{
    public function reportUnauthorizedApiAccess(Request $request, string $reason, ?Usuario $actor = null): void;
}
