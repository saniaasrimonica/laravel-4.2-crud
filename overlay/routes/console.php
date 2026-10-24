<?php
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

Artisan::command('inventory:token {email}', function () {
    $email = $this->argument('email');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $this->error('Invalid email.'); return 1; }
    $user = User::firstOrCreate(['email' => $email], ['name' => 'Inventory developer', 'password' => Hash::make(Str::random(64))]);
    $this->line($user->createToken('local-demo', ['*'], now()->addDay())->plainTextToken);
    $this->warn('Expires in 24 hours. Keep private; never commit tokens.');
})->purpose('Create a local demo API token');
