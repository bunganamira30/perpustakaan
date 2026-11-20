<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('author', 150);
            $table->string('isbn', 20)->unique();
            $table->string('category', 50);
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
        });

        // Enforce CHECK constraint on stock >= 0 in SQLite
        DB::unprepared("
            CREATE TRIGGER IF NOT EXISTS check_books_stock_insert
            BEFORE INSERT ON books
            FOR EACH ROW
            WHEN NEW.stock < 0
            BEGIN
                SELECT RAISE(ABORT, 'CHECK constraint failed: stock >= 0');
            END;

            CREATE TRIGGER IF NOT EXISTS check_books_stock_update
            BEFORE UPDATE OF stock ON books
            FOR EACH ROW
            WHEN NEW.stock < 0
            BEGIN
                SELECT RAISE(ABORT, 'CHECK constraint failed: stock >= 0');
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("
            DROP TRIGGER IF EXISTS check_books_stock_insert;
            DROP TRIGGER IF EXISTS check_books_stock_update;
        ");
        Schema::dropIfExists('books');
    }
};
