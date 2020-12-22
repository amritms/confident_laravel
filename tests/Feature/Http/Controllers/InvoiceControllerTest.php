<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{
    /** @test */
    public function create_returns_a_view()
    {
        $response = $this->get(route('invoice.create'));

        $response->assertStatus(200)->assertViewIs('invoice.create');
    }
}
