<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('redirect_log', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('redirect_id');
            $table->string('ip');
            $table->text('referer')->nullable();
            $table->text('query_params')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('access')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('redirect_log');
    }
};
