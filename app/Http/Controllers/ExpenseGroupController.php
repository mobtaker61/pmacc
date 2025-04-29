<?php

namespace App\Http\Controllers;

use App\Models\ExpenseGroup;
use Illuminate\Http\Request;

class ExpenseGroupController extends Controller
{
    public function index()
    {
        $expenseGroups = ExpenseGroup::paginate(10);
        return view('expense-groups.index', compact('expenseGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ExpenseGroup::create($request->all());

        return redirect()->route('expense_groups.index')
            ->with('success', __('expenses.group_created'));
    }

    public function update(Request $request, ExpenseGroup $expenseGroup)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $expenseGroup->update($request->all());

        return redirect()->route('expense_groups.index')
            ->with('success', __('expenses.group_updated'));
    }

    public function destroy(ExpenseGroup $expenseGroup)
    {
        $expenseGroup->delete();

        return redirect()->route('expense_groups.index')
            ->with('success', __('expenses.group_deleted'));
    }
} 