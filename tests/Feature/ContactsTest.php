<?php

namespace Tests\Feature;

use App\Contact;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;

class ContactsTest extends TestCase
{
    use RefreshDatabase;  // this refreshes the DB to get a newly created Model

    /** @test */
    public function canAddContact() {
        $this->withoutExceptionHandling(); // this allows Laravel to check if our uri exist

        $this->post('/api/contacts', ['contact_name' => 'Test Name',
        'email'=>'test@email.com',
        'birthday'=>'01/06/1990',
        'company' => 'Welcome Inc.']);

        $contact = Contact::first();

        $this->assertEquals('Test Name', $contact->contact_name);
        $this->assertEquals('test@email.com', $contact->email);
        $this->assertEquals('01/06/1990', $contact->birthday);
        $this->assertEquals('Welcome Inc.', $contact->company);
    }

    /** @test */
    public function NameIsRequired() {

        $response = $this->post('/api/contacts', [
        'email'=>'test@email.com',
        'birthday'=>'01/06/1990',
        'company' => 'Welcome Inc.']);


        $response->assertSessionHasErrors ('contact_name');
        $this->assertCount(0, Contact::all());
    }


    /** @test */
    public function EmailIsRequired() {

        $response = $this->post('/api/contacts', [
        'contact_name'=>'Test Name',
        'birthday'=>'01/06/1990',
        'company' => 'Welcome Inc.']);


        $response->assertSessionHasErrors ('email');
        $this->assertCount(0, Contact::all());
    }
}
