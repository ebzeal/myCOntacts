<?php

namespace Tests\Feature;

use App\Contact;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ContactsTest extends TestCase
{
    use RefreshDatabase;  // this refreshes the DB to get a newly created Model

    /** @test */
    public function canAddContact() {
        $this->withoutExceptionHandling(); // this allows Laravel to check if our uri exist

        $this->post('/api/contacts', ['name' => 'Test Name']);

        $this->assertCount(1, Contact::all());
    }
}
