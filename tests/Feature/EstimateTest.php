<?php

namespace Tests\Feature;

use App\Mail\EstimateLink;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use App\Models\Estimate;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EstimateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_a_guest_cannot_list_estimates()
    {
        $this->expectException(AuthenticationException::class);

        Estimate::factory(5)->create();

        $this->get(route('estimates.index'));
    }

    public function test_a_user_can_list_estimates()
    {
        $this->signIn();
        $estimates = Estimate::factory(5)->create();

        $response = $this->get(route('estimates.index'));

        $response
            ->assertStatus(200)
            ->assertSee($estimates[0]->name);
    }

    public function test_a_user_can_search_estimates()
    {
        $this->signIn();
        $estimates = Estimate::factory(20)->create();

        $response = $this->get(route('estimates.index', ['search' => $estimates[0]->name]));

        $response
            ->assertStatus(200)
            ->assertSee($estimates[0]->name)
            ->assertDontSee($estimates[1]->name);
    }

    public function test_a_user_can_update_estimates()
    {
        $this->signIn();

        $estimate = Estimate::factory()->create();

        $response = $this->put(route('estimates.update', $estimate), [
            'name' => $estimate->name.' Edited',
            'sections_positions' => []
        ]);

        $this->assertEquals($estimate->name.' Edited', $estimate->fresh()->name);
    }

    public function test_a_user_can_share_estimate(): void
    {
        Mail::fake();

        $this->signIn();

        $estimate = Estimate::factory()->create();

        $response = $this->post(route('estimates.share', $estimate), [
            'email' => $email = $this->faker->safeEmail(),

        ]);

        $response->assertOk();

        // assert mail has sent to $email
        Mail::assertSent(EstimateLink::class, function ($mail) use ($email, $estimate) {
            return $mail->hasTo($email) && $mail->estimate->is($estimate);
        });
    }
}
