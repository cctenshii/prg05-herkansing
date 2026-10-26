<?php

namespace App\Http\Controllers;

use App\Models\Blindbox;
use App\Models\Serie;
use Illuminate\Http\Request;

class BlindboxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Blindbox::query();

        // Only show public blindboxes to non-admins
        if (!auth()->check() || auth()->user()->role->name !== 'Admin') {
            $query->where('public', true);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by genre
        if ($request->filled('serie_id')) {
            $query->where('serie_id', $request->serie_id);
        }

        $blindboxes = $query->get();
        $series = Serie::all();

        return view('blindboxes.index', compact('blindboxes', 'series'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        // Require at least 3 logins before allowing "create"
        if ($user->login_count < 3) {
            return redirect()
                ->route('blindboxes.index')
                ->with('status', '⚠️ You must log in at least 3 times before adding a new Blindbox.');
        }

        $series = \App\Models\Serie::all();
        return view('blindboxes.create', compact('series'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'serie_id' => ['required', 'exists:series,id'],
            'summary' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $blindbox = new Blindbox($validatedData);
        $blindbox->created_by = auth()->id();

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
        return view('blindboxes.show', compact('blindbox'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Blindbox $blindbox)
    {
        $series = Serie::all();
        $users = \App\Models\User::all(); // 👈 add this line

        return view('blindboxes.edit', compact('blindbox', 'series', 'users'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blindbox $blindbox)
    {
        $request->validate([
            'name' => ['required'],
            'serie_id' => ['required', 'exists:series,id'],
            'summary' => ['required'],
            'image' => ['nullable', 'image', 'max:2048'],
            'created_by' => ['nullable', 'exists:users,id'], // 👈 added validation
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
        $blindbox->delete();

        return redirect()
            ->route('blindboxes.index')
            ->with('success', 'Blindbox deleted successfully!');
    }

}
