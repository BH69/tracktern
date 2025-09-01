<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LogbookEntry;

class RecalculateHoursLogged extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logbook:recalculate-hours {--entry-id= : Specific entry ID to recalculate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate hours_logged for logbook entries based on time_in and time_out';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $entryId = $this->option('entry-id');
        
        if ($entryId) {
            // Recalculate specific entry
            $entry = LogbookEntry::find($entryId);
            if (!$entry) {
                $this->error("Entry with ID {$entryId} not found.");
                return 1;
            }
            
            $this->recalculateEntry($entry);
            $this->info("Entry ID {$entryId} recalculated successfully.");
        } else {
            // Recalculate all entries
            $this->info('Recalculating hours for all logbook entries...');
            
            $entries = LogbookEntry::whereNotNull('time_in')
                ->whereNotNull('time_out')
                ->get();
            
            $updated = 0;
            $bar = $this->output->createProgressBar($entries->count());
            
            foreach ($entries as $entry) {
                if ($this->recalculateEntry($entry)) {
                    $updated++;
                }
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine();
            $this->info("Recalculation complete! Updated {$updated} entries.");
        }
        
        return 0;
    }
    
    private function recalculateEntry(LogbookEntry $entry): bool
    {
        $calculatedHours = $entry->calculateHours();
        
        // Only update if there's a significant difference (avoid floating point precision issues)
        if (abs($entry->hours_logged - $calculatedHours) > 0.01) {
            $oldHours = $entry->hours_logged;
            $entry->update(['hours_logged' => round($calculatedHours, 2)]);
            
            $this->line("Entry ID {$entry->id}: {$oldHours} → {$calculatedHours} hours");
            return true;
        }
        
        return false;
    }
}
