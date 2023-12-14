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
        Schema::table('items', function (Blueprint $table) {
            // Ajoutez une colonne pour le chemin du fichier PDF
            $table->string('fichier')->nullable();

            // Ajoutez une colonne pour le lien YouTube
            $table->string('lien_youtube')->nullable();
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            // Inversez les modifications dans la méthode down si nécessaire
            $table->dropColumn('fichier');
            $table->dropColumn('lien_youtube');
        });
    }
};
