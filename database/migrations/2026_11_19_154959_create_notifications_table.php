<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->jsonb('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // main query: notifications of one user, unread ones, ordered by time
            $table->index(
                ['notifiable_type', 'notifiable_id', 'read_at', 'created_at'],
                'notifications_unread_idx'
            );
        });

        // filter by data->>'type'
        DB::statement("CREATE INDEX notifications_type_idx ON notifications ((data->>'type'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
