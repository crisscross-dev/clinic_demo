<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->boot();

echo "=== Stock Transaction Test ===\n";

// Get the current count
$count = App\Models\StockTransaction::count();
echo "Total stock transactions: $count\n";

// Get the latest 3 transactions
echo "\nLatest 3 transactions:\n";
$latest = App\Models\StockTransaction::latest()->take(3)->get();
foreach ($latest as $transaction) {
    echo "ID: {$transaction->id}, Type: {$transaction->type}, Item: {$transaction->item_id}, Qty: {$transaction->quantity}, Consultation: {$transaction->consultation_id}, Created: {$transaction->created_at}\n";
}

// Check for consultation 54 specifically
echo "\nTransactions for consultation 54:\n";
$cons54 = App\Models\StockTransaction::where('consultation_id', 54)->get();
foreach ($cons54 as $transaction) {
    echo "ID: {$transaction->id}, Type: {$transaction->type}, Item: {$transaction->item_id}, Qty: {$transaction->quantity}, Created: {$transaction->created_at}\n";
}

echo "\n=== Test Complete ===\n";
