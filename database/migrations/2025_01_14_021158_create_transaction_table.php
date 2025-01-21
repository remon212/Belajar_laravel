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
        Schema::create('transaction_detail', function (Blueprint $table) {
            $table->integer("id")->autoIncrement(); 
            $table->unsignedBigInteger('transaction_id'); // Menggunakan unsignedBigInteger untuk foreign key
            $table->unsignedBigInteger('movie_id'); // Menggunakan unsignedBigInteger untuk foreign key
            $table->integer('quantity'); // Jumlah item dalam transaksi
            $table->timestamps(); // Menambahkan kolom created_at dan updated_at
    
            // Menambahkan foreign key untuk relasi dengan tabel transaction
            $table->foreign('transaction_id')->references('id')->on('transaction')->onDelete('cascade');
    
            // Menambahkan foreign key untuk relasi dengan tabel movie
            $table->foreign('movie_id')->references('id')->on('movie')->onDelete('cascade');
        });
    }
    
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};
