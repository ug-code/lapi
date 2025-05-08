<?php

namespace Tests\Browser;

use App\Models\FundYield;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FundsYieldTest extends DuskTestCase
{
    public function test_fetch_and_store_funds_yield()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://api.fintables.com/funds/yield/')
                ->waitFor('body')
                ->assertSee('yield')
                ->screenshot('funds-yield-response')
                ->storeResponseInDatabase();
        });
    }
} 