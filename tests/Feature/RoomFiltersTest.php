<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_room_filter_form_accepts_any_numeric_price_input(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get('/rooms?max_price=1000000');

        $response->assertOk();
        $response->assertSee('name="max_price"', false);
        $response->assertSee('step="1"', false);
        $response->assertDontSee('step="100000"', false);
    }
}
