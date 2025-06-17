<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Spatie\Permission\Models\Role;
use App\Services\SystemLogService;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:usuarios.index')->only('index');
        $this->middleware('can:usuarios.create')->only(['create', 'store']);
        $this->middleware('can:usuarios.edit')->only(['edit', 'update']);
        $this->middleware('can:usuarios.destroy')->only('destroy');
    }

    public function index()
    {
        $users = User::with('roles')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array'
        ]);

        $user = User::create([
            'nombre' => $request->name,
            'correo' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->roles);

        // Log de creación de usuario
        SystemLogService::userCreated($user->id, $user->nombre, $user->correo);

        // Trigger verification email
        event(new Registered($user));

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'roles' => 'required|array'
        ]);

        $data = [
            'nombre' => $request->name,
            'correo' => $request->email,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->syncRoles($request->roles);

        // Log de actualización de usuario
        SystemLogService::userUpdated($user->id, $user->nombre, $user->correo);

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'No puedes eliminar tu propio usuario.');
        }

        // Log de eliminación de usuario (antes de eliminar)
        SystemLogService::userDeleted($user->id, $user->nombre, $user->correo);

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }

    /** Create user for registration process */
    public function createForRegistration(array $data): User
    {
        try {
            $user = User::create([
                'nombre' => $data['nombre'],
                'correo' => $data['correo'],
                'rfc' => $data['rfc'],
                'password' => Hash::make($data['password']),
                'estado' => $data['estado'] ?? 'pendiente',
                'verification_token' => Str::random(64),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Usuario creado exitosamente', ['user_id' => $user->id]);
            return $user;

        } catch (\Exception $e) {
            Log::error('Error al crear usuario', ['error' => $e->getMessage()]);
            throw new \Exception('Error al crear el usuario: ' . $e->getMessage());
        }
    }

    /** Update user with array data */
    public function updateWithData(User $user, array $data): User
    {
        try {
            $user->update($data);
            Log::info('Usuario actualizado exitosamente', ['user_id' => $user->id]);
            return $user;
        } catch (\Exception $e) {
            Log::error('Error al actualizar usuario', ['error' => $e->getMessage()]);
            throw new \Exception('Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    /** Find user by RFC */
    public function findByRfc(string $rfc): ?User
    {
        return User::where('rfc', $rfc)->first();
    }

    /** Find user by email */
    public function findByEmail(string $email): ?User
    {
        return User::where('correo', $email)->first();
    }

    /** Validate user registration data */
    public function validateRegistrationData(array $data): array
    {
        return \Illuminate\Support\Facades\Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,correo'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ])->validate();
    }
} 