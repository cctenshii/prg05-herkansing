<?php

namespace App\Http\Controllers;

use App\Models\Blindbox;
use Illuminate\Http\Request;

class AdminBlindboxController extends Controller
{
    public function index()
    {
        $blindboxes = Blindbox::with(['serie', 'creator'])->get();
        return view('admin.blindboxes.index', compact('blindboxes'));
    }

    public function togglePublic(Request $request, Blindbox $blindbox)
    {
        $blindbox->public = $request->boolean('public');
        $blindbox->save();

        return back()->with('status', $blindbox->public ? 'Blindbox is now public.' : 'Blindbox is now private.');
    }
    
}
