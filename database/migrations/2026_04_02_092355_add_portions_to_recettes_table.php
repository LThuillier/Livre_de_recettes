<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('recettes', function (Blueprint $table) {
        $table->integer('portions')->default(2)->after('description');
    });
}

public function down()
{
    Schema::table('recettes', function (Blueprint $table) {
        $table->dropColumn('portions');
    });
}
};
