<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim($request->string('q')->toString());

        $users = User::query()
            ->select(['id', 'name', 'email', 'is_admin', 'email_verified_at', 'created_at'])
            ->when($search, fn ($query) => $query
                ->where(fn ($searchQuery) => $searchQuery
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => [
                'q' => $search,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Users/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        User::create($this->payload($validated));

        return redirect()
            ->route('admin.users.index')
            ->with('message', 'Kullanıcı oluşturuldu.');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
            ],
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate($this->rules($user));

        if ($request->user()->is($user) && ! ($validated['is_admin'] ?? false)) {
            return back()->withErrors([
                'is_admin' => 'Kendi admin yetkini bu ekrandan kaldıramazsın.',
            ]);
        }

        if ($user->is_admin && ! ($validated['is_admin'] ?? false) && $this->isLastAdmin($user)) {
            return back()->withErrors([
                'is_admin' => 'Sistemde en az bir admin kullanıcı kalmalıdır.',
            ]);
        }

        $user->update($this->payload($validated, $user));

        return redirect()
            ->route('admin.users.index')
            ->with('message', 'Kullanıcı güncellendi.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->with('error', 'Kendi hesabını admin kullanıcı yönetiminden silemezsin.');
        }

        if ($user->is_admin && $this->isLastAdmin($user)) {
            return back()->with('error', 'Sistemde en az bir admin kullanıcı kalmalıdır.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('message', 'Kullanıcı silindi.');
    }

    protected function rules(?User $user = null): array
    {
        $passwordRules = $user
            ? ['nullable', 'confirmed', Password::defaults()]
            : ['required', 'confirmed', Password::defaults()];

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => $passwordRules,
            'is_admin' => ['boolean'],
            'email_verified' => ['boolean'],
        ];
    }

    protected function payload(array $validated, ?User $user = null): array
    {
        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_admin' => (bool) ($validated['is_admin'] ?? false),
            'email_verified_at' => ($validated['email_verified'] ?? false) ? ($user?->email_verified_at ?? now()) : null,
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = $validated['password'];
        }

        if ($user && $user->email !== $validated['email'] && ! ($validated['email_verified'] ?? false)) {
            $payload['email_verified_at'] = null;
        }

        return $payload;
    }

    protected function isLastAdmin(User $user): bool
    {
        return User::query()
            ->where('is_admin', true)
            ->whereKeyNot($user->id)
            ->doesntExist();
    }
}
