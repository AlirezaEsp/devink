<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->longText('body');
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
        });

        DB::statement("
            ALTER TABLE articles
            ADD CONSTRAINT articles_status_check
            CHECK (status IN ('draft', 'published'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
