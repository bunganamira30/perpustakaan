<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminBookController extends Controller
{
    /**
     * Display listing of all books for administrator (including archived).
     */
    public function index(Request $request): View
    {
        $query = Book::query();

        if ($request->filled('q')) {
            $searchTerm = '%' . $request->input('q') . '%';
            $query->where(function ($sub) use ($searchTerm) {
                $sub->where('title', 'like', $searchTerm)
                    ->orWhere('author', 'like', $searchTerm)
                    ->orWhere('isbn', 'like', $searchTerm);
            });
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'archived') {
                $query->where('is_archived', true);
            } elseif ($request->input('status') === 'active') {
                $query->where('is_archived', false);
            }
        }

        $books = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('admin.books.index', [
            'title' => 'Manajemen Koleksi Buku (Admin)',
            'books' => $books,
            'searchQuery' => $request->input('q', ''),
            'statusFilter' => $request->input('status', ''),
        ]);
    }

    /**
     * Show form for creating a new book.
     */
    public function create(): View
    {
        return view('admin.books.create', [
            'title' => 'Tambah Buku Baru',
        ]);
    }

    /**
     * Store a newly created book.
     */
    public function store(StoreBookRequest $request): RedirectResponse
    {
        $book = Book::create([
            'title' => $request->input('title'),
            'author' => $request->input('author'),
            'isbn' => $request->input('isbn'),
            'category' => $request->input('category'),
            'stock' => (int) $request->input('stock'),
            'is_archived' => false,
        ]);

        return redirect()->route('admin.books.index')->with(
            'success',
            "Buku '{$book->title}' berhasil ditambahkan ke katalog perpustakaan."
        );
    }

    /**
     * Show form for editing an existing book.
     */
    public function edit(int $id): View
    {
        $book = Book::findOrFail($id);

        return view('admin.books.edit', [
            'title' => 'Edit Buku: ' . $book->title,
            'book' => $book,
        ]);
    }

    /**
     * Update the specified book in database.
     */
    public function update(UpdateBookRequest $request, int $id): RedirectResponse
    {
        $book = Book::findOrFail($id);

        $book->update([
            'title' => $request->input('title'),
            'author' => $request->input('author'),
            'isbn' => $request->input('isbn'),
            'category' => $request->input('category'),
            'stock' => (int) $request->input('stock'),
            'is_archived' => $request->boolean('is_archived'),
        ]);

        return redirect()->route('admin.books.index')->with(
            'success',
            "Data buku '{$book->title}' berhasil diperbarui."
        );
    }

    /**
     * Toggle archiving status for a book (BR-05 Soft Archiving).
     */
    public function archive(int $id): RedirectResponse
    {
        $book = Book::findOrFail($id);

        $newStatus = !$book->is_archived;
        $book->update(['is_archived' => $newStatus]);

        $actionText = $newStatus ? 'diarsipkan' : 'diaktifkan kembali';

        return redirect()->route('admin.books.index')->with(
            'success',
            "Buku '{$book->title}' berhasil {$actionText}."
        );
    }
}
