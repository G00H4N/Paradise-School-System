<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        return inertia('Inventory/Index', [
            'items' => InventoryItem::all(),
            'transactions' => InventoryTransaction::with('item')->latest()->get()
        ]);
    }

    public function storeItem(Request $request)
    {
        InventoryItem::create($request->all());
        return back();
    }

    // Stock Add (Purchase) or Usage
    public function transaction(Request $request)
    {
        $request->validate([
            'inventory_item_id' => 'required',
            'type' => 'required|in:purchase,usage,damage',
            'quantity' => 'required|integer|min:1'
        ]);

        DB::transaction(function () use ($request) {
            $item = InventoryItem::find($request->inventory_item_id);

            // Logic: Purchase hai to stock barhao, Usage hai to kam karo
            if ($request->type == 'purchase') {
                $item->increment('total_quantity', $request->quantity);
            } else {
                if ($item->total_quantity < $request->quantity) {
                    throw new \Exception("Not enough stock!");
                }
                $item->decrement('total_quantity', $request->quantity);
            }

            InventoryTransaction::create([
                'inventory_item_id' => $request->inventory_item_id,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'date' => now(),
                'performed_by' => auth()->id()
            ]);
        });

        return back()->with('success', 'Stock Updated');
    }
}