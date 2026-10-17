<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private UserService $service) {}

    public function index(Request $request)
    {
        $q = User::with(['studentProfile', 'lecturerProfile', 'internships.lecturer']);

        if ($s = trim((string) $request->q)) {
            $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")
                    ->orWhereHas('studentProfile', fn ($x) => $x->where('nim', 'like', "%{$s}%"))
                    ->orWhereHas('lecturerProfile', fn ($x) => $x->where('nidn', 'like', "%{$s}%"));
            });
        }
        if (in_array($request->role, ['admin', 'dosen', 'mahasiswa'], true)) {
            $q->where('role', $request->role);
        }
        if (in_array($request->status, ['aktif', 'nonaktif'], true)) {
            $q->where('is_active', $request->status === 'aktif');
        }

        $users = $q->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.form', ['user' => new User, 'dosens' => $this->lecturers(), 'internship' => null]);
    }

    public function store(UserRequest $request)
    {
        $user = $this->service->create($request->user(), $request->validated());

        return redirect()->route('admin.users.index')->with('success', "Akun {$user->name} berhasil dibuat.");
    }

    public function edit(User $user)
    {
        $user->load('studentProfile', 'lecturerProfile');
        $internship = $user->isMahasiswa() ? $user->currentInternship()?->load('lecturer') : null;

        return view('admin.users.form', ['user' => $user, 'dosens' => $this->lecturers(), 'internship' => $internship]);
    }

    public function update(UserRequest $request, User $user)
    {
        if ($user->is($request->user()) && ! $request->boolean('is_active')) {
            return back()->withInput()->withErrors(['is_active' => 'Anda tidak dapat menonaktifkan akun sendiri.']);
        }

        $this->service->update($request->user(), $user, $request->validated());

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna diperbarui.');
    }

    public function password(Request $request, User $user)
    {
        $data = $request->validateWithBag('reset', ['password' => ['required', 'string', 'min:8', 'confirmed']]);
        $this->service->resetPassword($request->user(), $user, $data['password']);

        return back()->with('success', "Password {$user->name} berhasil direset.");
    }

    public function toggle(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }
        $this->service->toggleActive($request->user(), $user);

        return back()->with('success', $user->is_active ? 'Akun diaktifkan.' : 'Akun dinonaktifkan.');
    }

    public function destroy(Request $request, User $user)
    {
        $error = $this->service->delete($request->user(), $user);

        return $error
            ? back()->with('error', $error)
            : redirect()->route('admin.users.index')->with('success', 'Akun pengguna dihapus.');
    }

    private function lecturers()
    {
        return User::where('role', 'dosen')->where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }
}
