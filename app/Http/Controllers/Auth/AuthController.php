<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Estadio;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * AuthController — Maneja el Login, Registro y Logout de usuarios.
 *
 * Flujo de autenticación:
 *   1. Usuario ingresa email + contraseña.
 *   2. Laravel valida credenciales contra la tabla 'usuarios'.
 *   3. Según rol_id, se redirige a un dashboard diferente:
 *      - rol_id=1 → /comite/dashboard
 *      - rol_id=2 → /jefe/dashboard
 *      - rol_id=3 → /tecnico/dashboard
 */
class AuthController extends Controller
{
    // ─── LOGIN ────────────────────────────────────────────────────────────

    /**
     * Muestra el formulario de inicio de sesión.
     *
     * @return View
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Procesa el intento de inicio de sesión.
     *
     * Valida las credenciales del usuario. Si son correctas, regenera la sesión
     * para prevenir ataques de fijación de sesión y redirige al dashboard
     * correspondiente al rol del usuario.
     *
     * @param  Request  $request  Datos del formulario (email, password)
     * @return RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {
        // 1. Validar el formato de los datos recibidos del formulario
        $credenciales = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Intentar autenticar con Auth::attempt()
        //    - Busca en la tabla 'usuarios' (configurado en auth.php)
        //    - Verifica el hash de la contraseña automáticamente
        //    - El segundo parámetro activa "Recuérdame"
        if (Auth::attempt($credenciales, $request->boolean('remember'))) {

            // 3. Regenerar el ID de sesión para prevenir session fixation
            $request->session()->regenerate();

            // 4. Redirigir según el rol del usuario autenticado
            $rolId = (int) Auth::user()->rol_id;

            return match ($rolId) {
                1 => redirect()->route('comite.dashboard'),
                2 => redirect()->route('jefe.dashboard'),
                3 => redirect()->route('tecnico.dashboard'),
                // Si el rol no está mapeado, redirigir al login con error
                default => redirect()->route('login')
                               ->withErrors(['email' => 'Rol de usuario no reconocido.']),
            };
        }

        // 5. Si las credenciales son incorrectas, regresar al login con error
        //    onlyInput('email') preserva el email en el formulario
        return back()
            ->withErrors(['email' => 'Las credenciales no coinciden con nuestros registros.'])
            ->onlyInput('email');
    }

    // ─── REGISTRO ─────────────────────────────────────────────────────────

    /**
     * Muestra el formulario de registro de nuevos usuarios.
     * Carga los roles y estadios disponibles para los dropdowns.
     *
     * @return View
     */
    public function showRegister(): View
    {
        // Obtener todos los roles disponibles para el dropdown
        $roles = Rol::all();

        // Obtener todos los estadios para asignar al usuario
        $estadios = Estadio::orderBy('nombre')->get();

        return view('auth.register', compact('roles', 'estadios'));
    }

    /**
     * Procesa el registro de un nuevo usuario en el sistema.
     *
     * @param  Request  $request  Datos del formulario de registro
     * @return RedirectResponse
     */
    public function register(Request $request): RedirectResponse
    {
        // 1. Validar todos los campos del formulario de registro
        $validado = $request->validate([
            'nombre'                => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', 'unique:usuarios,email'],
            'password'              => ['required', 'min:8', 'confirmed'], // confirmed busca 'password_confirmation'
            'rol_id'                => ['required', 'exists:roles,rol_id'],
            'estadio_id'            => ['required', 'exists:estadios,estadio_id'],
        ]);

        // 2. Crear el nuevo usuario en la base de datos
        //    Hash::make() encripta la contraseña con bcrypt
        $usuario = Usuario::create([
            'nombre'     => $validado['nombre'],
            'email'      => $validado['email'],
            'password'   => Hash::make($validado['password']),
            'rol_id'     => $validado['rol_id'],
            'estadio_id' => $validado['estadio_id'],
        ]);

        // 3. Iniciar sesión automáticamente después del registro
        Auth::login($usuario);

        // 4. Redirigir al dashboard correspondiente al rol
        $rolId = (int) $usuario->rol_id;

        return match ($rolId) {
            1 => redirect()->route('comite.dashboard'),
            2 => redirect()->route('jefe.dashboard'),
            3 => redirect()->route('tecnico.dashboard'),
            default => redirect()->route('login'),
        };
    }

    // ─── LOGOUT ───────────────────────────────────────────────────────────

    /**
     * Cierra la sesión del usuario actual.
     *
     * Limpia la sesión completa y regenera el token CSRF para seguridad.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        // 1. Cerrar la sesión del guard activo ('web')
        Auth::logout();

        // 2. Invalidar la sesión actual (eliminar todos los datos de sesión)
        $request->session()->invalidate();

        // 3. Regenerar el token CSRF para prevenir CSRF token reuse
        $request->session()->regenerateToken();

        // 4. Redirigir al formulario de login
        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }
}