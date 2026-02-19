<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

   public function store(Request $request)
{
    $messages = [
        'name.required' => 'El nombre es obligatorio',
        'email.required' => 'El correo electrónico es obligatorio',
        'email.email' => 'El correo debe ser una dirección válida',
        'email.unique' => 'Este correo ya está registrado',
        'password.required' => 'La contraseña es obligatoria',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres',
        'password.confirmed' => 'Las contraseñas no coinciden',
        'role.required' => 'Debes seleccionar un rol',
        'role.in' => 'El rol seleccionado no es válido',
        'num_empleado.required' => 'El número de empleado es obligatorio',
        'num_empleado.unique' => 'Este número de empleado ya está registrado',
        'signature.image' => 'La firma debe ser una imagen',
        'signature.mimes' => 'La firma debe ser un archivo jpeg, png, jpg o gif',
        'signature.max' => 'La firma no debe pesar más de 2MB',
        'profile_photo.image' => 'La foto de perfil debe ser una imagen',
        'profile_photo.mimes' => 'La foto debe ser un archivo jpeg, png, jpg o gif',
        'profile_photo.max' => 'La foto no debe pesar más de 2MB',
    ];

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6|confirmed',
        'role' => 'required|in:ti,aux_ti,direccion,almacen,aux_almacen,aux_calidad,aux_contabilidad,aux_estimaciones,aux_finanzas,aux_logistica,aux_rh,calidad,contabilidad,estimaciones,finanzas,logistica,rh,operaciones,aux_operaciones,hse,aux_hse',
        'num_empleado' => 'required|string|max:50|unique:users,num_empleado',
        'signature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ], $messages);

    // ✅ Preparar datos
    $data = [
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role,
        'num_empleado' => $request->num_empleado,
    ];

    // ✅ Manejar firma
    if ($request->hasFile('signature')) {
        $data['signature'] = $request->file('signature')->store('signatures', 'public');
    }

    // ✅ Manejar foto de perfil
    if ($request->hasFile('profile_photo')) {
        $data['profile_photo'] = $request->file('profile_photo')->store('profile_photos', 'public');
    }

    // ✅ Crear el usuario
    User::create($data);

    return redirect()->route('users.index')->with('success', 'Usuario creado correctamente');
}




    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
{
    // Preparar reglas de validación dinámicamente
    $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,'.$user->id,
        'role' => 'required|in:ti,aux_ti,direccion,almacen,aux_almacen,aux_calidad,aux_contabilidad,aux_estimaciones,aux_finanzas,aux_logistica,aux_rh,calidad,contabilidad,estimaciones,finanzas,logistica,rh,operaciones,aux_operaciones,hse,aux_hse',
        'num_empleado' => 'required|string|max:50|unique:users,num_empleado,'.$user->id,
        'signature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ];

    // Solo validar password si se proporcionó uno nuevo
    if ($request->filled('password')) {
        $rules['password'] = 'required|string|min:6|confirmed';
    }

    // Mensajes personalizados en español
    $messages = [
        'name.required' => 'El nombre es obligatorio',
        'email.required' => 'El correo electrónico es obligatorio',
        'email.email' => 'El correo debe ser una dirección válida',
        'email.unique' => 'Este correo ya está registrado',
        'role.required' => 'Debes seleccionar un rol',
        'role.in' => 'El rol seleccionado no es válido',
        'num_empleado.required' => 'El número de empleado es obligatorio',
        'num_empleado.unique' => 'Este número de empleado ya está registrado',
        'password.required' => 'La contraseña es obligatoria',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres',
        'password.confirmed' => 'Las contraseñas no coinciden',
        'signature.image' => 'La firma debe ser una imagen',
        'signature.mimes' => 'La firma debe ser un archivo jpeg, png, jpg o gif',
        'signature.max' => 'La firma no debe pesar más de 2MB',
        'profile_photo.image' => 'La foto de perfil debe ser una imagen',
        'profile_photo.mimes' => 'La foto debe ser un archivo jpeg, png, jpg o gif',
        'profile_photo.max' => 'La foto no debe pesar más de 2MB',
    ];

    $request->validate($rules, $messages);

    $data = [
        'name' => $request->name,
        'email' => $request->email,
        'role' => $request->role,
        'num_empleado' => $request->num_empleado,
    ];

    // Actualizar password solo si se proporcionó
    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }

    // Manejar la firma
    if ($request->hasFile('signature')) {
        if ($user->signature) {
            Storage::disk('public')->delete($user->signature);
        }
        $signaturePath = $request->file('signature')->store('signatures', 'public');
        $data['signature'] = $signaturePath;
    }

    // Manejar la foto de perfil
    if ($request->hasFile('profile_photo')) {
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }
        $photoPath = $request->file('profile_photo')->store('profile_photos', 'public');
        $data['profile_photo'] = $photoPath;
    }

    $user->update($data);

    return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente');
}
}