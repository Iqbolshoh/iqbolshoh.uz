<?php

/**
 * Finance wording shared by the bot and anything else that speaks to a person.
 *
 * The admin panel is English only and does not read these files; what lives
 * here is what the BOT says, in the four languages it speaks.
 */
return [
    'currency' => "so'm",

    'kind' => [
        'expense' => 'Expense',
        'income' => 'Income',
    ],

    'method' => [
        'cash' => 'Cash',
        'card' => 'Card',
        'transfer' => 'Transfer',
        'other' => 'Other',
    ],

    // Only the categories the installer seeds are translated. One the owner
    // adds themselves is shown exactly as they typed it — see
    // FinanceCategory::displayName().
    'categories' => [
        'food' => 'Food',
        'transport' => 'Transport',
        'housing' => 'Home & bills',
        'connection' => 'Mobile & internet',
        'health' => 'Health',
        'clothes' => 'Clothes',
        'education' => 'Education',
        'entertainment' => 'Entertainment',
        'gifts' => 'Gifts & help',
        'tech' => 'Tech & work',
        'other_expense' => 'Other',
        'salary' => 'Salary',
        'freelance' => 'Freelance & orders',
        'other_income' => 'Other income',
    ],
];
