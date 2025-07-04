<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Penerima>
 */
class PenerimaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_cv' => $this->faker->company(),
            'nama_penerima' => $this->faker->name(),
            'id_instansi' => \App\Models\Instansi::factory()->create()->id
        ];
    }
}
