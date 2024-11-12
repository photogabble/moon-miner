<?php

namespace Tests\Feature\Jobs;

use App\Models\User;
use App\Models\Bounty;
use App\Jobs\TurnsScheduler;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TurnsSchedulerTest extends TestCase
{
    use RefreshDatabase;

    public function test_applies_correct_number_of_turns(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['turns' => 0]);
        $this->assertEquals(0, $user->turns);

        TurnsScheduler::dispatchSync();

        $user->refresh();
        $this->assertEquals(config('game.turns_per_tick'), $user->turns);
    }

    public function test_does_not_add_more_than_max_number_of_turns(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['turns' => config('game.max_turns')]);
        $this->assertEquals(config('game.max_turns'), $user->turns);

        TurnsScheduler::dispatchSync();

        $user->refresh();
        $this->assertEquals(config('game.max_turns'), $user->turns);
    }

    public function test_adds_partial_sum_when_over_max(): void
    {
        $user = User::factory()->create(['turns' => config('game.max_turns') - floor(config('game.turns_per_tick') / 2)]);

        TurnsScheduler::dispatchSync();

        $user->refresh();
        $this->assertEquals(config('game.max_turns'), $user->turns);
    }

    public function test_catches_up_when_running_delayed(): void
    {
        $user = User::factory()->create(['turns' => 0]);
        TurnsScheduler::dispatchSync();

        // Turns scheduler should be run every two minutes, however it will only catch up once
        // therefore if run late, no matter how late, it should only ever increase by
        // a single game.turns_per_tick.

        Carbon::setTestNow(Carbon::now()->addMinutes(60));
        TurnsScheduler::dispatchSync();

        $user->refresh();
        $this->assertEquals(config('game.turns_per_tick') * 2, $user->turns);
    }
}
