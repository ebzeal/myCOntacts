<?php

namespace App\Http\Controllers;

use App\Contact;
use Illuminate\Http\Request;

class ContactsController extends Controller
{
    public function store() {
        $data = request()->validate([
            'contact_name' => 'required',
            'email' => 'required',
            'birthday' => '',
            'company' => '',
        ]);
        Contact::create($data);
    }
}
