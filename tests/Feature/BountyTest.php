<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Bounty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BountyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_cancelling_bounty_logs_and_deletes(): void
    {
        $jane = User::factory()->create();
        $john = User::factory()->create();

        $bounty = new Bounty();
        $bounty->bounty_on = $jane->id;
        $bounty->placed_by = $john->id;
        $bounty->amount = 1000;
        $bounty->save();

        $this->assertEquals(0, $john->logEntries()->count());

        $initialBalance = $john->wallet()->balance;

        $bounty->cancel();

        $this->assertEquals(1, $john->logEntries()->count());
        $this->assertEquals($initialBalance + 1000, $john->wallet()->balance);
    }

    public function test_collecting_bounty_pays_out_and_deletes(): void
    {
        $jane = User::factory()->create();
        $john = User::factory()->create();
        $jack = User::factory()->create();

        $initialBalance = $jack->wallet()->balance;

        $bounty = new Bounty();
        $bounty->bounty_on = $jane->id;
        $bounty->placed_by = $john->id;
        $bounty->amount = 999;
        $bounty->save();

        $bounty->collect($jack);

        $this->assertEquals($initialBalance + 999, $jack->wallet()->balance);
    }

    public function test_cant_collect_bounty_on_self(): void
    {
        $jane = User::factory()->create();
        $john = User::factory()->create();

        $bounty = new Bounty();
        $bounty->bounty_on = $jane->id;
        $bounty->placed_by = $john->id;
        $bounty->amount = 1000;
        $bounty->save();

        $this->expectException(\Exception::class);
        $bounty->collect($jane);
    }
}
