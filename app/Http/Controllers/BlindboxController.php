<?php

namespace App\Http\Controllers;

use App\Models\Blindbox;
use App\Models\Serie;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BlindboxController extends Controller
{

    public function index(Request $request)
    {
        $query = Blindbox::with(['serie', 'creator']);

        // Only show public blindboxes to guests and non-admins
        if (
            !auth()->check() ||
            optional(auth()->user()->role)->name !== 'Admin'
        ) {
            $query->where('public', true);
        }

        // Search by blindbox name
        if ($request->filled('search')) {
            $query->where(
                'name',
                'like',
                '%' . $request->search . '%'
            );
        }

        // Filter by series
        if ($request->filled('serie_id')) {
            $query->where('serie_id', $request->serie_id);
        }

        $blindboxes = $query->get();
        $series = Serie::all();

        return view(
            'blindboxes.index',
            compact('blindboxes', 'series')
        );
    }

    public function create()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!$this->canCreateBlindbox()) {
            return redirect()->route('blindboxes.index')->with(
                'error',
                'You must favorite at least 5 blindboxes before creating a new blindbox.'
            );
        }

        $series = Serie::all();

        return view('blindboxes.create', compact('series'));
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!$this->canCreateBlindbox()) {
            throw ValidationException::withMessages([
                'general' => ['You must favorite at least 5 blindboxes before creating a new blindbox.'],
            ]);
        }

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'serie_id' => ['required', 'exists:series,id'],
            'summary' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $blindbox = new Blindbox($validatedData);
        $blindbox->created_by = auth()->id();
        $blindbox->public = true;

        if ($request->hasFile('image')) {
            $blindbox->image = $request->file('image')->store('blindboxes', 'public');
        }

        $blindbox->save();

        return redirect()->route('blindboxes.index')->with('success', 'Blindbox created successfully!');
    }

    public function show(Blindbox $blindbox)
    {
        $blindbox->load(['serie', 'creator', 'favoritedBy']);

        return view('blindboxes.show', compact('blindbox'));
    }

    public function favorites(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $query = auth()->user()->favoriteBlindboxes()->with(['serie', 'creator']);

        if ($request->filled('search')) {
            $query->where(function ($queryBuilder) use ($request) {
                $queryBuilder->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('summary', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('serie_id')) {
            $query->where('serie_id', $request->serie_id);
        }

        $blindboxes = $query->get();
        $series = Serie::all();

        return view('blindboxes.favorites', compact('blindboxes', 'series'));
    }

    public function toggleFavorite(Blindbox $blindbox)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->favoriteBlindboxes()->whereKey($blindbox->id)->exists()) {
            $user->favoriteBlindboxes()->detach($blindbox);
            $message = 'Blindbox removed from favorites.';
        } else {
            $user->favoriteBlindboxes()->attach($blindbox);
            $message = 'Blindbox added to favorites.';
        }

        return back()->with('status', $message);
    }

    public function edit(Blindbox $blindbox)
    {
        $this->ensureUserCanManageBlindbox($blindbox);

        $series = Serie::all();
        $users = User::all();

        return view('blindboxes.edit', compact('blindbox', 'series', 'users'));
    }

    public function update(Request $request, Blindbox $blindbox)
    {
        $this->ensureUserCanManageBlindbox($blindbox);

        $request->validate([
            'name' => ['required'],
            'serie_id' => ['required', 'exists:series,id'],
            'summary' => ['required'],
            'image' => ['nullable', 'image', 'max:2048'],
            'created_by' => ['nullable', 'exists:users,id'],
        ]);

        $blindbox->fill($request->only(['name', 'serie_id', 'summary', 'created_by']));

        if ($request->hasFile('image')) {
            if ($blindbox->image && Storage::disk('public')->exists($blindbox->image)) {
                Storage::disk('public')->delete($blindbox->image);
            }
            $blindbox->image = $request->file('image')->store('blindboxes', 'public');
        }

        $blindbox->save();

        return redirect()->route('blindboxes.index')->with('success', 'Blindbox updated successfully!');
    }

    public function destroy(Blindbox $blindbox)
    {
        $this->ensureUserCanManageBlindbox($blindbox);

        $blindbox->delete();

        return redirect()
            ->route('blindboxes.index')
            ->with('success', 'Blindbox deleted successfully!');
    }

    private function canCreateBlindbox(): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        if (optional($user->role)->name === 'Admin') {
            return true;
        }

        return $user->favoriteBlindboxes()->count() >= 5;
    }

    private function ensureUserCanManageBlindbox(Blindbox $blindbox): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'You must be logged in to manage blindboxes.');
        }

        if (optional($user->role)->name === 'Admin') {
            return;
        }

        if ((int)$blindbox->created_by !== (int)$user->id) {
            abort(403, 'You can only manage blindboxes you created.');
        }
    }
}
