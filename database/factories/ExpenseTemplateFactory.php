<?php

namespace Database\Factories;

use Crater\Models\ExpenseCategory;
use Crater\Models\ExpenseTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'amount' => $this->faker->numberBetween(0, 50),
            'expense_date' => now(),
            'expense_category_id' => ExpenseCategory::factory()->create()->id,
            'notification' => 1,
            'term' => ExpenseTemplate::TERM_MONTLY,
        ];
    }
}
