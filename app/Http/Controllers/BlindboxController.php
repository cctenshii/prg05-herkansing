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
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Blindbox::with(['serie', 'creator']);

        // Only show public blindboxes to guests and non-admins
        if (
            ! auth()->check() ||
            optional(auth()->user()->role)->name !== 'Admin'
        ) {
            $query->where('public', true);
        }

        // Search by blindbox name
        if ($request->filled('search')) {
            $query->where(
                'name',
                'like',
                '%'.$request->search.'%'
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        if (! $this->canCreateBlindbox()) {
            return redirect()->route('blindboxes.index')->with(
                'error',
                'You must favorite at least 5 blindboxes before creating a new blindbox.'
            );
        }

        $series = Serie::all();

        return view('blindboxes.create', compact('series'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        if (! $this->canCreateBlindbox()) {
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

    /**
     * Display the specified resource.
     */
    public function show(Blindbox $blindbox)
    {
        $blindbox->load(['serie', 'creator', 'favoritedBy']);

        return view('blindboxes.show', compact('blindbox'));
    }

    public function toggleFavorite(Blindbox $blindbox)
    {
        if (! auth()->check()) {
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Blindbox $blindbox)
    {
        $this->ensureUserCanManageBlindbox($blindbox);

        $series = Serie::all();
        $users = User::all();

        return view('blindboxes.edit', compact('blindbox', 'series', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
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

        if (! $user) {
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

        if (! $user) {
            abort(403, 'You must be logged in to manage blindboxes.');
        }

        if (optional($user->role)->name === 'Admin') {
            return;
        }

        if ((int) $blindbox->created_by !== (int) $user->id) {
            abort(403, 'You can only manage blindboxes you created.');
        }
    }
}
