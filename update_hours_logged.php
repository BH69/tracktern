<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->boot();

use App\Models\LogbookEntry;

echo "=== Updating Hours Logged for All Entries ===\n";

$entries = LogbookEntry::whereNotNull('time_in')
    ->whereNotNull('time_out')
    ->get();

$updated = 0;

foreach ($entries as $entry) {
    $calculatedHours = $entry->calculateHours();
    
    if (abs($entry->hours_logged - $calculatedHours) > 0.01) { // Only update if different
        $oldHours = $entry->hours_logged;
        $entry->update(['hours_logged' => round($calculatedHours, 2)]);
        
        echo "Updated Entry ID {$entry->id}: {$oldHours} → {$calculatedHours} hours\n";
        $updated++;
    }
}

echo "\nTotal entries updated: {$updated}\n";
echo "Update complete!\n";
