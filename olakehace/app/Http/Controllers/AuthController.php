<?php

namespace App\Http\Controllers;

use App\Models\AppUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $credentials = $request->only('email', 'password');
    
        $user = AppUser::where('email', $credentials['email'])->first();
    
        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);
    
            // Guardar datos en la sesión
            session()->put([
                'user_id' => $user->id,
                'user_role' => $user->role_id,
                'user_name' => $user->name,
            ]);
    
            // Redirigir según el rol
            switch ($user->role_id) {
                case 1: // Administrador
                    return redirect()->route('dashboard')->with('success', 'Bienvenido, Admin');
                case 2: // Publicador
                    return redirect()->route('publisher')->with('success', 'Bienvenido, Publicador');
                case 3: // Registrado
                    return redirect()->route('home')->with('success', 'Bienvenido');
                default:
                    return redirect('/')->with('error', 'Rol de usuario no válido.');
            }
        }
    
        return redirect()->back()->withErrors(['email' => 'Las credenciales proporcionadas no son correctas.'])->withInput();
    }
    

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Validar datos del formulario
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:app_users,email',
            'password' => 'required|string|confirmed',
            'role_id' => 'required|integer|exists:roles,id', // Valida que el rol existe en la tabla roles
        ]);

        if ($validator->fails()) {
            dd($validator->errors());
            return redirect()->back()->withErrors($validator)->withInput();
        }
        // Crear el usuario con `post_aprvd` inicializado a 0
        $user = AppUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role_id' => $request->role_id,
            'status_id' => 1, // Asigna un estado predeterminado (por ejemplo, Activo)
            'post_aprvd' => 0, // Inicializa `post_aprvd` en 0 para nuevos registros
        ]);
        // Redireccionar al login con mensaje de éxito
        return redirect()->route('login')->with('success', 'Usuario registrado con éxito. Ahora puedes iniciar sesión.');
    }

    
    public function logout(Request $request)
    {

        $request->session()->flush();
        return redirect()->route('login')->with('success', 'Has cerrado sesión correctamente.');
    }
    

}


