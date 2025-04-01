<?php

namespace Database\Factories\Rbac;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rbac\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $companyIds=Company::pluck('id');
        return [
            'company_id'=>$companyIds->random(),
            'name'=>fake()->jobTitle,
            'description'=>fake()->paragraph,
        ];
    }
}
