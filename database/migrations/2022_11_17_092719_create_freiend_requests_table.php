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
        Schema::create('friend_requests', function (Blueprint $table) {
            $table->foreignId("sender_id")->constrained("users")->cascadeOnDelete();
            $table->foreignId("reciever_id")->constrained("users")->cascadeOnDelete();
            $table->primary(["sender_id", "reciever_id"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('freiend_requests');
    }
};
