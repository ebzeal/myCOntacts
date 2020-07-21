<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Contact;
use App\User;
use Faker\Generator as Faker;

$factory->define(Contact::class, function (Faker $faker) {
    return [
        'user_id'=> factory(User::class),
        'contact_name' => $faker->name,
        'email'=>$faker->email,
        'birthday'=> $faker->date,
        'company' => $faker->company
    ];
});
