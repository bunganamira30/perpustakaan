<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $bookId = $this->route('id');

        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:150'],
            'isbn' => ['required', 'string', 'max:20', Rule::unique('books', 'isbn')->ignore($bookId)],
            'category' => ['required', 'string', 'max:50'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_archived' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul buku wajib diisi.',
            'author.required' => 'Nama penulis wajib diisi.',
            'isbn.required' => 'Nomor ISBN wajib diisi.',
            'isbn.unique' => 'Nomor ISBN sudah terdaftar pada buku lain.',
            'category.required' => 'Kategori buku wajib diisi.',
            'stock.required' => 'Jumlah stok buku wajib diisi.',
            'stock.min' => 'Stok buku tidak boleh bernilai negatif.',
        ];
    }
}
