<?php

namespace Database\Factories;

use App\Models\PatientInfo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\PatientInfo>
 */
class PatientInfoFactory extends Factory
{
    protected $model = PatientInfo::class;

    public function definition(): array
    {
        $sex = $this->faker->randomElement(['Male','Female']);
        $yearLevels = ['1st Year','2nd Year','3rd Year','4th Year'];
        $courses = ['BSCS','BSIT','BSEd','BSBA','BSN'];
        $departments = ['BED - JHS','BED - SHS','HED - BSOA','HED - BSCPE','HED - BSP','HED - BSA/MA','FACULTY','NTS'];
        $status = $this->faker->randomElement(['pending','approved','rejected']);

        return [
            // Identity & demographics
            'first_name' => $this->faker->firstName($sex === 'Male' ? 'male' : 'female'),
            'middle_name' => $this->faker->optional()->firstName(),
            'last_name' => $this->faker->lastName(),
            'sex' => $sex,
            'birthdate' => $this->faker->optional()->dateTimeBetween('-26 years', '-17 years'),
            'nationality' => $this->faker->optional()->randomElement(['Filipino','American','Japanese','Korean','Chinese']),
            'religion' => $this->faker->optional()->randomElement(['Roman Catholic','Christian','Islam','Iglesia ni Cristo','Buddhist']),

            // Contact & address
            'contact_no' => $this->faker->optional()->numerify('09#########'),
            'address' => $this->faker->optional()->address(),

            // School-related
            'department' => $this->faker->optional()->randomElement($departments),
            'course' => $this->faker->randomElement($courses),
            'year_level' => $this->faker->optional()->randomElement($yearLevels),

            // Emergency / guardian
            'father_name' => $this->faker->optional()->name('male'),
            'mother_name' => $this->faker->optional()->name('female'),
            'guardian_name' => $this->faker->optional()->name(),
            'guardian_relationship' => $this->faker->optional()->randomElement(['Father','Mother','Sibling','Relative','Guardian']),
            'father_contact_no' => $this->faker->optional()->numerify('09#########'),
            'mother_contact_no' => $this->faker->optional()->numerify('09#########'),
            'guardian_contact_no' => $this->faker->optional()->numerify('09#########'),
            'guardian_address' => $this->faker->optional()->address(),

            // Medical info
            'allergies' => $this->faker->optional()->randomElement(['None','Dust','Pollen','Shellfish','Peanuts']),
            'other_allergies' => $this->faker->optional()->sentence(3),
            'treatments' => $this->faker->optional()->sentence(6),
            'covid' => $this->faker->optional()->randomElement(['Yes','No']),
            'flu_vaccine' => $this->faker->optional()->randomElement(['Yes','No']),
            'other_vaccine' => $this->faker->optional()->randomElement(['Hepatitis B','MMR','Tetanus','Varicella']),
            'medical_history' => $this->faker->optional()->paragraph(),
            'medication' => $this->faker->optional()->sentence(4),
            'lasthospitalization' => $this->faker->optional()->date('Y-m-d'),
            'consent' => $this->faker->optional()->randomElement(['Yes','No']),
            'consent_by' => $this->faker->optional()->name(),

            // Status
            'status' => $status,
        ];
    }
}
