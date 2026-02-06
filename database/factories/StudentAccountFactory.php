<?php

namespace Database\Factories;

use App\Models\StudentAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StudentAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StudentAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $email = $this->faker->unique()->safeEmail();
        return [
            'email' => $email,
            // Use a simple default password for seeding. It's hashed by the model cast.
            'password' => 'password',
            'remember_token' => Str::random(10),
        ];
    }
}
