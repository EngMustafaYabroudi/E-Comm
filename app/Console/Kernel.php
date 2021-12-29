<?php

namespace App\Console;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $products = Product::all();
            foreach ($products as $product) {
                if ($product->expiry_date <= Carbon::now()->format('Y-m-d')) {
                    $product->delete();
                    return;
                }
                if (Carbon::createFromFormat('Y-m-d', $product->expiry_date)->subDays(30) >= Carbon::now()) {
                    $product->regular_price = $product->regular_price - ($product->regular_price * 30 / 100);
                } elseif (Carbon::createFromFormat('Y-m-d', $product->expiry_date)->subDays(15) >= Carbon::now()) {
                    $product->regular_price = $product->regular_price - ($product->regular_price * 15 / 100);
                } else  $product->regular_price = $product->regular_price - ($product->regular_price * 70 / 100);
                $product->save();
            }
        })->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
