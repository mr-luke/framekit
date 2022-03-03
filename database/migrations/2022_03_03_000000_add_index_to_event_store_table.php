<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Framekit\Contracts\Config as FramekitConfig;

class AddIndexToEventStoreTable extends Migration
{
    /**
     * Instance of EventStore.
     *
     * @var \Framekit\Contracts\Config
     */
    protected $config;

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct()
    {
        $this->config = app()->make(FramekitConfig::class);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->config->get('tables.eventstore'), function (Blueprint $table) {
            $table->index('stream_id');
        });
        Schema::table($this->config->get('tables.snapshots'), function (Blueprint $table) {
            $table->index('stream_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->config->get('tables.eventstore'), function (Blueprint $table) {
            $table->dropIndex('stream_id');
        });
        Schema::table($this->config->get('tables.snapshots'), function (Blueprint $table) {
            $table->dropIndex('stream_id');
        });
    }
}
