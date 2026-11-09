<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('produks')->where('kategori', 'Camilan')->update(['kategori' => 'Snack']);
    }

    public function down(): void {}
};
