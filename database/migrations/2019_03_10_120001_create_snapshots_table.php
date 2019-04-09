<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Framekit\Contracts\Config;

class CreateSnapshotsTable extends Migration
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
        Schema::create($this->config->get('tables.snapshots'), function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('stream_id');
            $table->unsignedInteger('event_id');
            $table->text('state');
            $table->unsignedInteger('commited')->default(0);
            $table->timestamp('created_at');

            $table->foreign('event_id')
                  ->references('id')
                  ->on($this->config->get('tables.eventstore'))
                  ->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists($this->config->get('tables.snapshots'));
    }
}
