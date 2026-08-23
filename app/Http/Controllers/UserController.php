<?php

namespace App\Http\Controllers;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $users = User::when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            });
        })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('users.index', compact('users', 'search'));
    }

    public function exportPDF(Request $request)
    {
        $search = $request->query('search');

        $users = User::when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            });
        })
            ->orderBy('id')
            ->get();

        $totalUsers = $users->count();

        $pdf = Pdf::loadView('reports.users_pdf', compact('users', 'search', 'totalUsers'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => true,
                'defaultFont' => 'Sarabun',
            ]);

        return $pdf->stream('user-report-'.date('Y-m-d').'.pdf');
    }

    public function create()
    {
        return view('users.create', ['roles' => User::ROLES, 'statuses' => User::STATUSES]);
    }

    public function store(Request $request)
    {
        // 1. แปลงค่า input จาก form ให้เป็นตัวพิมพ์เล็กและตัดช่องว่าง
        if ($request->has('role')) {
            $request->merge(['role' => strtolower(trim($request->input('role')))]);
        }
        if ($request->has('status')) {
            $request->merge(['status' => strtolower(trim($request->input('status')))]);
        }

        // 2. Validate โดยใช้ array_keys เพื่อดึงเฉพาะ Key (admin, staff, doctor, patient)
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'status' => ['required', Rule::in(array_keys(User::STATUSES))],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', ['user' => $user, 'roles' => User::ROLES, 'statuses' => User::STATUSES]);
    }

    public function update(Request $request, User $user)
    {
        if ($request->has('role')) {
            $request->merge(['role' => strtolower(trim($request->input('role')))]);
        }
        if ($request->has('status')) {
            $request->merge(['status' => strtolower(trim($request->input('status')))]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'status' => ['required', Rule::in(array_keys(User::STATUSES))],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->status = $validated['status'];

        if ($request->filled('password')) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->withErrors('ไม่สามารถลบบัญชีของตัวเองได้');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}