<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuspiciousFileCheck
{
    public function handle(Request $request, Closure $next)
    {
        // Check if it's a POST request
        if ($request->isMethod('post')) {
            $files = $request->allFiles();
            foreach ($files as $fileInput) {
                if ($this->isSuspicious($fileInput)) {
                    // Abort the request if a suspicious file is detected
                    abort(403, 'Suspicious file detected.');
                }
            }
        }

        return $next($request);
    }

    private function isSuspicious($file)
    {
        // Implement your file checking logic here
        // You can check file type, size, content, etc.
        // Return true if the file is suspicious, false if not.
        // Example: Check for file extension
        $extension = $file->getClientOriginalExtension();
        return in_array($extension, ['exe', 'php', 'js']);
    }
}
