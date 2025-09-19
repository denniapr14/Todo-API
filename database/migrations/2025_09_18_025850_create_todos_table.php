<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('assignee');
            $table->text('description')->nullable();
            $table->dateTime('due_date');
            $table->string('status')->default('pending'); // pending, in_progress, completed
            $table->string('priority')->default('medium'); // low, medium, high
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('todos');
    }
};
