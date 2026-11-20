<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:150'],
            'isbn' => ['required', 'string', 'max:20', 'unique:books,isbn'],
            'category' => ['required', 'string', 'max:50'],
            'stock' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul buku wajib diisi.',
            'author.required' => 'Nama penulis wajib diisi.',
            'isbn.required' => 'Nomor ISBN wajib diisi.',
            'isbn.unique' => 'Nomor ISBN sudah terdaftar pada katalog.',
            'category.required' => 'Kategori buku wajib diisi.',
            'stock.required' => 'Jumlah stok buku wajib diisi.',
            'stock.integer' => 'Stok buku harus berupa angka bulat.',
            'stock.min' => 'Stok buku tidak boleh bernilai negatif.',
        ];
    }
}
