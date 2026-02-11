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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:ti,aux_ti,direccion,almacen,aux_almacen,aux_calidad,aux_contabilidad,aux_estimaciones,aux_finanzas,aux_logistica,aux_rh,calidad,contabilidad,estimaciones,finanzas,logistica,rh,operaciones,aux_operaciones,hse,aux_hse',
            'num_empleado' => 'required|string|max:50|unique:users,num_empleado',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'num_empleado' => $request->num_empleado,
        ];

        // Manejar la firma
        if ($request->hasFile('signature')) {
            $signaturePath = $request->file('signature')->store('signatures', 'public');
            $data['signature'] = $signaturePath;
        }

        // Manejar la foto de perfil
        if ($request->hasFile('profile_photo')) {
            $photoPath = $request->file('profile_photo')->store('profile_photos', 'public');
            $data['profile_photo'] = $photoPath;
        }

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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:ti,aux_ti,direccion,almacen,aux_almacen,aux_calidad,aux_contabilidad,aux_estimaciones,aux_finanzas,aux_logistica,aux_rh,calidad,contabilidad,estimaciones,finanzas,logistica,rh,operaciones,aux_operaciones,hse,aux_hse',
            'num_empleado' => 'required|string|max:50|unique:users,num_empleado,'.$user->id,
            'signature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'num_empleado' => $request->num_empleado,
        ];

        // Manejar la firma
        if ($request->hasFile('signature')) {
            // Eliminar la firma anterior si existe
            if ($user->signature) {
                Storage::disk('public')->delete($user->signature);
            }
            $signaturePath = $request->file('signature')->store('signatures', 'public');
            $data['signature'] = $signaturePath;
        }

        // Manejar la foto de perfil
        if ($request->hasFile('profile_photo')) {
            // Eliminar la foto anterior si existe
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $photoPath = $request->file('profile_photo')->store('profile_photos', 'public');
            $data['profile_photo'] = $photoPath;
        }

        $user->update($data);

        if ($request->password) {
            $request->validate(['password' => 'string|min:6|confirmed']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'No puedes eliminar tu propio usuario']);
        }

        // Eliminar las imágenes antes de eliminar el usuario
        if ($user->signature) {
            Storage::disk('public')->delete($user->signature);
        }
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado');
    }
}