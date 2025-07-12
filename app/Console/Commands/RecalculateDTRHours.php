<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DTR;

class RecalculateDTRHours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dtr:recalculate-hours';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate total hours for all DTR records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting DTR hours recalculation...');

        $dtrs = DTR::whereNotNull('time_in')
                   ->whereNotNull('time_out')
                   ->get();

        $updated = 0;
        $progressBar = $this->output->createProgressBar($dtrs->count());

        foreach ($dtrs as $dtr) {
            $oldHours = $dtr->total_hours;
            $newHours = $dtr->calculateTotalHours();

            if ($oldHours != $newHours) {
                $dtr->update(['total_hours' => $newHours]);
                $updated++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->info("Recalculation completed!");
        $this->info("Total records processed: {$dtrs->count()}");
        $this->info("Records updated: {$updated}");

        return 0;
    }
}
