<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Framekit\Contracts\Config as FramekitConfig;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $config = app()->make(FramekitConfig::class);

        Schema::create($config->get('tables.eventstore'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('stream_type');
            $table->uuid('stream_id')->index();
            $table->string('event');
            $table->jsonb('payload');
            $table->unsignedSmallInteger('version');
            $table->unsignedInteger('sequence_no')->index()->default(0);
            $table->jsonb('meta');
            $table->timestamp('committed_at', 6);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $config = app()->make(FramekitConfig::class);

        Schema::dropIfExists($config->get('tables.eventstore'));
    }
};
