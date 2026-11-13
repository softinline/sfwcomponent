<?php

namespace Softinline\SfwComponent\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Softinline\SfwComponent\SfwComponent;
use Softinline\SfwComponent\SfwProtectedMiddleware;

class SetupController extends Controller
{
    /**
     * Display the index screen.
     *
     * @return mixed
     */
    public function index()
    {
        $complete = SfwProtectedMiddleware::isSetupComplete();
        $prefix = SfwComponent::ROUTE_PREFIX;
        if ($complete) {
            return redirect('/' . $prefix);
        }

        return view('sfwcomponent::backoffice.setup.index', [
            'error' => session('sfw_setup_error'),
            'success' => session('sfw_setup_success'),
            'output' => session('sfw_setup_output'),
        ]);
    }

    /**
     * Handle form submission.
     *
     * @return mixed
     */
    public function store()
    {
        $prefix = SfwComponent::ROUTE_PREFIX;

        if (SfwProtectedMiddleware::isSetupComplete()) {
            return redirect('/' . $prefix);
        }

        $current = (string) request()->get('current_password', '');
        $password = (string) request()->get('password', '');
        $confirm = (string) request()->get('password_confirmation', '');

        $file = SfwProtectedMiddleware::setupFile();
        $existingHash = null;
        if (is_file($file)) {
            $data = json_decode((string) @file_get_contents($file), true);
            $existingHash = is_array($data) ? ($data['password_hash'] ?? null) : null;
        }

        // If somehow a hash exists, require current password (default blank means none)
        if (!empty($existingHash)) {
            if (!Hash::check($current, $existingHash)) {
                return redirect()->back()->with('sfw_setup_error', 'La contraseña actual no es correcta.');
            }
        }

        if ($password === '') {
            return redirect()->back()->with('sfw_setup_error', 'Debes establecer una contraseña para SFW admin.');
        }
        if (strlen($password) < 4) {
            return redirect()->back()->with('sfw_setup_error', 'La contraseña debe tener al menos 4 caracteres.');
        }
        if ($password !== $confirm) {
            return redirect()->back()->with('sfw_setup_error', 'Las contraseñas no coinciden.');
        }

        $output = [];
        $ok = true;

        // Run migrations and seeders (best effort)
        try {
            Artisan::call('migrate', ['--force' => true]);
            $output[] = trim(Artisan::output());
        } catch (\Throwable $e) {
            $ok = false;
            $output[] = 'Migrate error: ' . $e->getMessage();
        }

        if ($ok) {
            try {
                // Try common seeder name(s) if available
                if (class_exists('Database\\Seeders\\SfwFoundationSeeder')) {
                    Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\SfwFoundationSeeder', '--force' => true]);
                    $output[] = trim(Artisan::output());
                } elseif (class_exists('Database\\Seeders\\DatabaseSeeder')) {
                    // avoid running full DatabaseSeeder automatically if it may be heavy; skip by default
                    $output[] = 'Seeder SfwFoundationSeeder no encontrado (omitido).';
                }
            } catch (\Throwable $e) {
                $output[] = 'Seed warning: ' . $e->getMessage();
            }
        }

        // Save setup password
        try {
            $dir = dirname($file);
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
            $payload = [
                'password_hash' => Hash::make($password),
                'setup_at' => date('c'),
            ];
            file_put_contents($file, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } catch (\Throwable $e) {
            return redirect()->back()->with('sfw_setup_error', 'No se pudo guardar la configuración de setup: ' . $e->getMessage());
        }

        if (!$ok) {
            return redirect()->back()->with('sfw_setup_error', 'Setup guardado, pero hubo errores al ejecutar migraciones.')
                ->with('sfw_setup_output', implode("\n", array_filter($output)));
        }

        return redirect('/' . $prefix . '/auth/login')->with('sfw_setup_success', 'SFW configurado correctamente. Inicia sesión con el usuario admin.')
            ->with('sfw_setup_output', implode("\n", array_filter($output)));
    }
}
