<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\Browser;

class DuskServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Browser::macro('storeResponseInDatabase', function () {
            $response = $this->driver->getPageSource();
            $data = json_decode($response, true);

            if (isset($data['yield'])) {
                \App\Models\FundYield::create([
                    'fund_id' => $data['fund_id'] ?? 'unknown',
                    'yield_value' => $data['yield'],
                    'date' => now(),
                    'raw_data' => $data
                ]);
            }

            return $this;
        });
    }
} 