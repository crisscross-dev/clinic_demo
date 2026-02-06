<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\StockTransaction;

class StockTransactionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'type' => 'required|in:restock,deduct,lost,expired',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = InventoryItem::findOrFail($request->item_id);

        // Adjust stock
        if ($request->type === 'restock') {
            $item->total_stock += $request->quantity;
        } elseif (in_array($request->type, ['deduct', 'lost', 'expired'])) {
            if ($item->total_stock < $request->quantity) {
                return back()->with('error', 'Not enough stock to deduct!');
            }
            $item->total_stock -= $request->quantity;
        }

        // Update status
        $item->status = $item->total_stock <= 0
            ? 'Out of Stock'
            : ($item->total_stock < ($item->low_stock_reminder ?? 5) ? 'Low Stock' : 'In Stock');
        $item->save();

        // Record the transaction
        StockTransaction::create([
            'item_id'  => $item->id,
            'type'     => $request->type,
            'quantity' => $request->quantity,
            'admin_id' => session('admin_id'), // or auth()->id() if using auth
        ]);

        return back()->with('success', 'Stock updated successfully.');
    }
}
