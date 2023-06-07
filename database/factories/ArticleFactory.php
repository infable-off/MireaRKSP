<?php


namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();
        return [
            'title' => fake()->sentence(),
            'file_dir' => fake()->word(),
            'indexability' => "РИНЦ",
            'udc' => "00".fake()->randomDigitNotNull().".".fake()->randomDigitNotNull(),
            'scientific_adviser' => fake('ru_RU')->lastName().' '.fake('ru_RU')->randomLetter().'. '.fake('ru_RU')->randomLetter().'.',
            'publication_place' => fake('ru_RU')->sentence(),
            'verification_status' => 'accepted',
            'user_id' => $user->id
        ];
    }
}
