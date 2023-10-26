<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NewsFeed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_feed', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('title');
            $table->date('date_released');
            $table->string('created_by');
            $table->text('content');
            $table->string('img')->nullable(); // 'nullable' allows for an optional image
            $table->timestamps(); // Created_at and Updated_at columns
        });
    }

    public function down()
    {
        Schema::dropIfExists('your_table_name');
    }
}
