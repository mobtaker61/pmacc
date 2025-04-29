<?php

namespace App\Http\Controllers;

use App\Models\PartyGroup;
use Illuminate\Http\Request;

class PartyGroupController extends Controller
{
    public function index()
    {
        $partyGroups = PartyGroup::paginate(10);
        return view('party_groups.index', compact('partyGroups'));
    }

    public function create()
    {
        return view('party_groups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        PartyGroup::create($request->all());

        return redirect()->route('party_groups.index')
            ->with('success', __('parties.group_created'));
    }

    public function edit(PartyGroup $partyGroup)
    {
        return view('party_groups.edit', compact('partyGroup'));
    }

    public function update(Request $request, PartyGroup $partyGroup)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $partyGroup->update($request->all());

        return redirect()->route('party_groups.index')
            ->with('success', __('parties.group_updated'));
    }

    public function destroy(PartyGroup $partyGroup)
    {
        $partyGroup->delete();

        return redirect()->route('party_groups.index')
            ->with('success', __('parties.group_deleted'));
    }
} 