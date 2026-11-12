<?php

namespace App\Http\Controllers;

use App\Models\Personal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with([
            'personal.datosLaborales.estadoLaboral',
            'personal.datosLaborales.puesto',
        ])->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => [
                'required',
                'in:ti,aux_ti,direccion,almacen,aux_almacen,aux_calidad,aux_contabilidad,aux_estimaciones,aux_finanzas,aux_logistica,aux_rh,calidad,contabilidad,estimaciones,finanzas,logistica,rh,operaciones,aux_operaciones,hse,aux_hse',
            ],
            'signature' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo debe ser una dirección válida.',
            'email.unique' => 'Este correo ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'role.required' => 'Debes seleccionar un rol.',
            'role.in' => 'El rol seleccionado no es válido.',
            'signature.image' => 'La firma debe ser una imagen.',
            'signature.mimes' => 'La firma debe ser jpeg, png, jpg o gif.',
            'signature.max' => 'La firma no debe pesar más de 2 MB.',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ];

        if ($request->hasFile('signature')) {
            $data['signature'] = $request->file('signature')
                ->store('signatures', 'public');
        }

        $user = User::create($data);

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'Usuario creado correctamente. TI puede vincularlo ahora con su expediente de personal.');
    }

    public function show(User $user)
    {
        $user->load([
            'personal.datosLaborales.estadoLaboral',
            'personal.datosLaborales.puesto',
        ]);

        $personalDisponible = Personal::with([
            'datosLaborales.estadoLaboral',
            'datosLaborales.puesto',
        ])
            ->where(function ($query) use ($user) {
                $query->whereDoesntHave('usuario')
                    ->orWhereHas('usuario', function ($usuarioQuery) use ($user) {
                        $usuarioQuery->where('users.id', $user->id);
                    });
            })
            ->orderBy('ape_pat')
            ->orderBy('ape_mat')
            ->orderBy('nombre')
            ->get();

        return view('users.show', compact('user', 'personalDisponible'));
    }

    public function edit(User $user)
    {
        return redirect()->route('users.show', $user);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => [
                'sometimes',
                'required',
                'in:ti,aux_ti,direccion,almacen,aux_almacen,aux_calidad,aux_contabilidad,aux_estimaciones,aux_finanzas,aux_logistica,aux_rh,calidad,contabilidad,estimaciones,finanzas,logistica,rh,operaciones,aux_operaciones,hse,aux_hse',
            ],
            'password' => ['sometimes', 'nullable', 'string', 'min:6', 'confirmed'],
            'signature' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo debe ser una dirección válida.',
            'email.unique' => 'Este correo ya está registrado.',
            'role.required' => 'Debes seleccionar un rol.',
            'role.in' => 'El rol seleccionado no es válido.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'signature.image' => 'La firma debe ser una imagen.',
            'signature.mimes' => 'La firma debe ser jpeg, png, jpg o gif.',
            'signature.max' => 'La firma no debe pesar más de 2 MB.',
        ]);

        $data = [];

        foreach (['name', 'email', 'role'] as $field) {
            if (array_key_exists($field, $validated)) {
                $data[$field] = $validated[$field];
            }
        }

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('signature')) {
            if ($user->signature) {
                Storage::disk('public')->delete($user->signature);
            }

            $data['signature'] = $request->file('signature')
                ->store('signatures', 'public');
        }

        if ($data === []) {
            return back()
                ->withErrors(['update' => 'No se recibieron cambios para guardar.'])
                ->withInput();
        }

        $user->update($data);

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'Información del usuario actualizada correctamente.');
    }

    public function updatePersonal(Request $request, User $user)
    {
        abort_unless(
            $request->user()->role === 'ti',
            403,
            'Solo TI puede vincular usuarios con registros de personal.'
        );

        $validated = $request->validate([
            'personal_id' => [
                'present',
                'nullable',
                'integer',
                Rule::exists('personal', 'id'),
                Rule::unique('users', 'personal_id')->ignore($user->id),
            ],
        ], [
            'personal_id.exists' => 'El registro de personal seleccionado no existe.',
            'personal_id.unique' => 'Ese registro de personal ya está vinculado a otra cuenta ERP.',
        ]);

        $user->update([
            'personal_id' => $validated['personal_id'],
        ]);

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'Vinculación con personal actualizada correctamente.');
    }
}
