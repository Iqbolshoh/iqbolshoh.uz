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
        'food' => 'Groceries',
        'cafe' => 'Cafés & restaurants',
        'taxi' => 'Taxi',
        'transport' => 'Public transport',
        'fuel' => 'Fuel & car',
        'rent' => 'Rent',
        'utilities' => 'Utilities',
        'home' => 'Home & household',
        'connection' => 'Mobile & internet',
        'subscriptions' => 'Subscriptions',
        'pharmacy' => 'Pharmacy',
        'doctor' => 'Doctor & tests',
        'sport' => 'Sports & gym',
        'clothes' => 'Clothes & shoes',
        'beauty' => 'Beauty & care',
        'education' => 'Education & courses',
        'kids' => 'Kids',
        'entertainment' => 'Entertainment',
        'travel' => 'Travel',
        'gifts' => 'Gifts & celebrations',
        'charity' => 'Charity & help',
        'tech' => 'Tech & gadgets',
        'work' => 'Work expenses',
        'taxes' => 'Taxes & fees',
        'debt_payment' => 'Debt & loan',
        'savings' => 'Savings',
        'pets' => 'Pets',
        'other_expense' => 'Other',

        'salary' => 'Salary',
        'freelance' => 'Freelance & orders',
        'business' => 'Business income',
        'investment' => 'Investments & interest',
        'rent_income' => 'Rental income',
        'gift_income' => 'Gifts received',
        'debt_return' => 'Debt repaid to me',
        'other_income' => 'Other income',
    ],
];
