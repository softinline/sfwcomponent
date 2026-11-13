<?php

namespace Softinline\SfwComponent;

use Closure;

class SfwProtectedMiddleware
{
    public static function setupFile(): string
    {
        return storage_path('app/sfw/setup.json');
    }

    public static function isSetupComplete(): bool
    {
        $file = self::setupFile();
        if (!is_file($file)) {
            return false;
        }
        $data = json_decode((string) @file_get_contents($file), true);
        return is_array($data) && !empty($data['password_hash']);
    }

    public function handle($request, Closure $next)
    {
        $prefix = SfwComponent::ROUTE_PREFIX;
        $isSetupRoute = $request->is($prefix . '/setup') || $request->is($prefix . '/setup/*');

        // First-time setup gate
        if (!self::isSetupComplete()) {
            if ($isSetupRoute) {
                return $next($request);
            }
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'sfw_setup_required', 'setup_url' => url($prefix . '/setup')], 428);
            }
            return redirect()->to('/' . $prefix . '/setup');
        }

        if (\Session::get('sfw-user-logged')) {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response('Unauthorized.', 401);
        }

        return redirect()->to('/' . $prefix . '/auth/login');
    }
}