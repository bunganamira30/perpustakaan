<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * Display a listing of public catalog books with parameterized search (SEC-08 Safe).
     */
    public function index(Request $request): View
    {
        $query = Book::query()->where('is_archived', false);

        // SEC-08 Safe: 100% Parameterized query via Eloquent binding
        if ($request->filled('q')) {
            $searchTerm = '%' . $request->input('q') . '%';
            $query->where(function ($sub) use ($searchTerm) {
                $sub->where('title', 'like', $searchTerm)
                    ->orWhere('author', 'like', $searchTerm)
                    ->orWhere('isbn', 'like', $searchTerm)
                    ->orWhere('category', 'like', $searchTerm);
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Allowlist sorting parameter validation
        $allowedSorts = ['title', 'author', 'stock', 'created_at'];
        $sort = in_array($request->input('sort'), $allowedSorts, true) ? $request->input('sort') : 'created_at';
        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';

        $books = $query->orderBy($sort, $order)->paginate(9)->withQueryString();

        // Get distinct categories for filter
        $categories = Book::where('is_archived', false)->distinct()->pluck('category');

        return view('books.index', [
            'title' => 'Katalog Pustaka',
            'books' => $books,
            'categories' => $categories,
            'searchQuery' => $request->input('q', ''),
            'selectedCategory' => $request->input('category', ''),
        ]);
    }

    /**
     * Display the specified book with reviews and details.
     */
    public function show(int $id): View
    {
        $book = Book::with(['reviews.user'])->findOrFail($id);

        return view('books.show', [
            'title' => $book->title,
            'book' => $book,
        ]);
    }
}
