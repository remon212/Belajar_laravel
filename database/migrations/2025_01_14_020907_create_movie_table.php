<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movie', function (Blueprint $table) {
            $table->integer("id")->autoIncrement(); 
            $table->string('title', 100); // Judul film dengan panjang maksimum 100 karakter
            $table->float('vote_average'); // Menggunakan penamaan snake_case untuk konsistensi
            $table->string('overview'); // Deskripsi film
            $table->string('poster_path'); // Menggunakan penamaan snake_case untuk konsistensi
            $table->unsignedBigInteger('category_id'); // Menggunakan unsignedBigInteger untuk foreign key
    
            // Menambahkan foreign key untuk relasi dengan tabel category
            $table->foreign('category_id')->references('id')->on('category')->onDelete('cascade');
    
            $table->timestamps(); // Menambahkan kolom created_at dan updated_at
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie');
    }
};
