<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Framekit\Contracts\Config;

class CreateEventStoreTable extends Migration
{
    /**
     * Instance of EventStore.
     *
     * @var \Framekit\Contracts\Config
     */
    protected $config;

    public function __construct()
    {
        $this->config = app()->make(Config::class);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->config->get('tables.eventstore'), function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('stream_id');
            $table->string('event');
            $table->jsonb('payload');
            $table->unsignedSmallInteger('version');
            $table->unsignedInteger('sequence_no')->default(0);
            $table->jsonb('meta');
            $table->timestamp('commited_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->config->get('tables.eventstore'));
    }
}
