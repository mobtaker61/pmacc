<?php

namespace App\Http\Controllers;

use App\Models\PettyCashBox;
use Illuminate\Http\Request;

class PettyCashBoxController extends Controller
{
    public function index()
    {
        $boxes = PettyCashBox::all();
        return view('petty-cash.boxes.index', compact('boxes'));
    }

    public function create()
    {
        return view('petty-cash.boxes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'currency' => 'required|string|in:TRY,IRR',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $box = PettyCashBox::create($validated);

        return redirect()->route('petty-cash.boxes.index')
            ->with('success', __('Box created successfully.'));
    }

    public function edit(PettyCashBox $box)
    {
        return view('petty-cash.boxes.edit', compact('box'));
    }

    public function update(Request $request, PettyCashBox $box)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'currency' => 'required|string|in:TRY,IRR',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $box->update($validated);

        return redirect()->route('petty-cash.boxes.index')
            ->with('success', __('Box updated successfully.'));
    }
} 