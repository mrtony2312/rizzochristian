<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        Schema::create('site_texts', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 12);
            $table->string('key');
            $table->text('value');
            $table->timestamps();
            $table->unique(['locale', 'key']);
        });

        User::query()->updateOrCreate(
            ['email' => 'admin@rizzochristian.com'],
            [
                'name' => 'Amministratore',
                'password' => 'admin@23',
                'is_admin' => true,
                'email_verified_at' => now(),
            ],
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('site_texts');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
