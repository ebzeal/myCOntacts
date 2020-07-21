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
public function authUsersCanFetchContacts() {
    $this->withoutExceptionHandling();

    $userOne = factory(User::class)->create();
    $userTwo = factory(User::class)->create();

    $contactOne = factory(Contact::class)->create(['user_id'=>$userOne->id]);
    $contactTwo = factory(Contact::class)->create(['user_id'=>$userTwo->id]);

    $response = $this->get('/api/contacts?api_token='.$userOne->api_token);
    $response->assertJsonCount(1)
            ->assertJson([
                'data' => [
                    [
                        "data" => [
                            'contact_id' => $contactOne->id
                        ]
                    ]
                ]
            ]);
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
    $contact = factory(Contact::class)->create(['user_id'=>$this->user->id]);
    $response =$this->get('/api/contacts/'.$contact->id . '?api_token=' . $this->user->api_token);
    // dd(json_decode($response->getContent())); 
    $response->assertJsonFragment([
        'contact_id' => $contact->id,
        'contact_name'=> $contact->contact_name,
        'email'=> $contact->email,
        'birthday'=> $contact->birthday->format('m/d/Y'),
        'company'=> $contact->company,
        'last_updated' => $contact->updated_at->diffForHumans(),
    ]);
    
}

/** @test */
public function authUsersCanRetrieveTheirCOntacts()
{
    $contact = factory(Contact::class)->create(['user_id'=>$this->user->id]);
    $newUser = factory(User::class)->create();
    $response =$this->get('/api/contacts/'.$contact->id . '?api_token=' . $newUser->api_token);
    $response->assertStatus(403);
}

 /** @test */
 public function canPatchAContact() {

    $this->withoutExceptionHandling();

    $contact = factory(Contact::class)->create(['user_id' => $this->user->id]);
    $response =$this->patch('/api/contacts/'.$contact->id, $this->data());

    $contact = $contact->fresh();

    $this->assertEquals('Test Name', $contact->contact_name);
        $this->assertEquals('test@email.com', $contact->email);
        $this->assertEquals('01/06/1990', $contact->birthday->format('m/d/Y'));
        $this->assertEquals('Welcome Inc.', $contact->company);
    
}

/** @test */
public function authUserCanEditTheirContact(){
    // $this->withoutExceptionHandling();

    $contact = factory(Contact::class)->create();

    $anotherUser = factory(User::class)->create();


    $response = $this->patch('/api/contacts/' . $contact->id,
        array_merge($this->data(), ['api_token' => $anotherUser->api_token]));

    $response->assertStatus(403);
}

 /** @test */
 public function canDeleteAContact() {

    $contact = factory(Contact::class)->create(['user_id' => $this->user->id]);
    $response =$this->delete('/api/contacts/'.$contact->id, ['api_token' => $this->user->api_token]);

    $this->assertCount(0, Contact::all());
    
}

 /** @test */
public function authUserCanDeleteTheirContact(){
    // $this->withoutExceptionHandling();

    $contact = factory(Contact::class)->create();

    $anotherUser = factory(User::class)->create();


    $response =$this->delete('/api/contacts/'.$contact->id, ['api_token' => $this->user->api_token]);


    $response->assertStatus(403);
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
