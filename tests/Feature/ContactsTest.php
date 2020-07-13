<?php

namespace Tests\Feature;

use App\Contact;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;

class ContactsTest extends TestCase
{
    use RefreshDatabase;  // this refreshes the DB to get a newly created Model

    /** @test */
    public function canAddContact() {
        // $this->withoutExceptionHandling(); // this allows Laravel to check if our uri exist

        $data = $this->data();
        $this->post('/api/contacts', $data);

        $contact = Contact::first();

        $this->assertEquals('Test Name', $contact->contact_name);
        $this->assertEquals('test@email.com', $contact->email);
        $this->assertEquals('01/06/1990', $contact->birthday);
        $this->assertEquals('Welcome Inc.', $contact->company);
    }
 /** @test */
 public function requiredField() {

    collect(['contact_name', 'email', 'birthday', 'company'])->each(function($field) {
        $data= array_merge($this->data(), [ $field=> '']); 
        $response = $this->post('/api/contacts', $data);
    
    
        $response->assertSessionHasErrors($field);
        $this->assertCount(0, Contact::all());
    });
    
}

 /** @test */
 public function emailMustBeValid() {

    $data= array_merge($this->data(), ['email'=> 'mymail.com']); 

        $response = $this->post('/api/contacts', $data);


        $response->assertSessionHasErrors ('email');
        $this->assertCount(0, Contact::all());
    
}


 /** @test */
 public function birthdaysMustBeValid() {

    $this->withoutExceptionHandling();
    $data= array_merge($this->data());
        $response = $this->post('/api/contacts', $data);

        $this->assertCount(1, Contact::all());
        $this->assertInstanceOf(Carbon::class, Contact::first()->birthday);
        $this->assertEquals('01-06-1990', Contact::first()->birthday->format('m-d-Y')); 
    
}

    // /** @test */
    // public function NameIsRequired() {

    //     $data= array_merge($this->data(), ['contact_name'=> '']); 
    //     $response = $this->post('/api/contacts', $data);


    //     $response->assertSessionHasErrors ('contact_name');
    //     $this->assertCount(0, Contact::all());
    // }


    // /** @test */
    // public function EmailIsRequired() {
    //     $data= array_merge($this->data(), ['email'=> '']); 

    //     $response = $this->post('/api/contacts', $data);


    //     $response->assertSessionHasErrors ('email');
    //     $this->assertCount(0, Contact::all());
    // }

    private function data() {
        return  ['contact_name' => 'Test Name',
        'email'=>'test@email.com',
        'birthday'=>'01/06/1990',
        'company' => 'Welcome Inc.'];
    }
}
