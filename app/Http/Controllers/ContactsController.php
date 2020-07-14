<?php

namespace App\Http\Controllers;

use App\Contact;
use Illuminate\Http\Request;

class ContactsController extends Controller
{
    public function store() {
        Contact::create($this->validateInput());
    }

    public function show(Contact $contact) {
        return $contact;
    }

    public function update(Contact $contact) {
        $contact->update($this->validateInput());
    }

    private function validateInput() {
        $data = request()->validate([
            'contact_name' => 'required',
            'email' => 'required|email',
            'birthday' => 'required',
            'company' => 'required',
        ]);
        return $data;
    }
}
