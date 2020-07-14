<?php

namespace Tests\Feature;

use App\Contact;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ContactsTest extends TestCase
{
    use RefreshDatabase;  // this refreshes the DB to get a newly created Model

    protected $user;

    /** @test */
    protected function setUp(): void {
        parent::setUp();
        $this->user = factory(User::class)->create();

    }

    /** @test */
    public function rejectAuthenticatedUser() {
        $data = $this->data();
        $response = $this->post('/api/contacts', array_merge($this->data(), ['api_token'=>'']));

        $response->assertRedirect('/login');
        $this->assertCount(0, Contact::all());

    }

    /** @test */
    public function authUserCanAddContact() {
        $this->withoutExceptionHandling(); // this allows Laravel to check if our uri exist

        $user = factory(User::class)->create();

        $data = $this->data();
        $this->post('/api/contacts', $this->data());

        $contact = Contact::first();

        $this->assertEquals('Test Name', $contact->contact_name);
        $this->assertEquals('test@email.com', $contact->email);
        $this->assertEquals('01/06/1990', $contact->birthday->format('m/d/Y'));
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

 /** @test */
 public function canRetrieveAContact() { 
    $contact = factory(Contact::class)->create();
    $response =$this->get('/api/contacts/'.$contact->id . '?api_token=' . $this->user->api_token);
    $response->assertJsonFragment([
        'contact_name'=> $contact->contact_name,
        'email'=> $contact->email,
        'birthday'=> $contact->birthday,
        'company'=> $contact->company,
    ]);
    
}


 /** @test */
 public function canPatchAContact() {

    $this->withoutExceptionHandling();

    $contact = factory(Contact::class)->create();
    $response =$this->patch('/api/contacts/'.$contact->id, $this->data());

    $contact = $contact->fresh();

    $this->assertEquals('Test Name', $contact->contact_name);
        $this->assertEquals('test@email.com', $contact->email);
        $this->assertEquals('01/06/1990', $contact->birthday->format('m/d/Y'));
        $this->assertEquals('Welcome Inc.', $contact->company);
    
}


 /** @test */
 public function canDeleteAContact() {

    $contact = factory(Contact::class)->create();
    $response =$this->delete('/api/contacts/'.$contact->id, ['api_token' => $this->user->api_token]);

    $this->assertCount(0, Contact::all());
    
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
        'company' => 'Welcome Inc.',
    'api_token'=> $this->user->api_token];
    }
}
