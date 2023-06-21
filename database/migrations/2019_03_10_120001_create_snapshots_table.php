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

        Schema::create($config->get('tables.snapshots'), function (Blueprint $table) use ($config) {
            $table->increments('id');
            $table->uuid('stream_id')->index();
            $table->unsignedInteger('event_id');
            $table->text('state');
            $table->unsignedInteger('committed')->default(0);
            $table->timestamp('created_at');

            $table->foreign('event_id')
                  ->references('id')
                  ->on($config->get('tables.eventstore'))
                  ->onDelete('RESTRICT');
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

        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists($config->get('tables.snapshots'));
    }
};
