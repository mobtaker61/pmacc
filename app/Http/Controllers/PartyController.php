<?php

namespace App\Http\Controllers;

use App\Models\Party;
use App\Models\PartyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PartyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parties = Party::with('partyGroup')->paginate(10);
        $partyGroups = PartyGroup::all();
        return view('parties.index', compact('parties', 'partyGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $partyGroups = PartyGroup::all();
        return view('parties.create', compact('partyGroups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'party_group_id' => 'nullable|exists:party_groups,id',
        ]);

        Party::create($validated);

        return redirect()->route('parties.index')
            ->with('success', __('parties.party_created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Party $party)
    {
        $transactions = $party->transactions()
            ->with('pettyCashBox')
            ->latest()
            ->paginate(10);

        return view('parties.show', compact('party', 'transactions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Party $party)
    {
        $partyGroups = PartyGroup::all();
        return view('parties.edit', compact('party', 'partyGroups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Party $party)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'party_group_id' => 'nullable|exists:party_groups,id',
        ]);

        $party->update($validated);

        return redirect()->route('parties.index')
            ->with('success', __('parties.party_updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Party $party)
    {
        $party->delete();

        return redirect()->route('parties.index')
            ->with('success', __('parties.party_deleted'));
    }
}
