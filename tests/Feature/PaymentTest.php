<?php

namespace Tests\Feature;

use App\Models\Payment;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;



    /** @test */
    public function it_can_create_transaction()
    {
        $response = $this->get(route('createTransaction'));

        $response->assertStatus(200);
    }



    /** @test */
    public function it_can_handle_cancelled_transaction()
    {
        $response = $this->get(route('cancelTransaction'));

        $response->assertRedirect(route('createTransaction'));
    }

}
