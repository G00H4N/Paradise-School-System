<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LibraryController extends Controller
{
    // 1. Books List
    public function index()
    {
        return inertia('Library/Books', ['books' => Book::all()]);
    }

    // 2. Add New Book
    public function store(Request $request)
    {
        $request->validate(['title' => 'required', 'quantity' => 'required|integer']);
        Book::create($request->all());
        return back()->with('success', 'Book Added');
    }

    // 3. Issue Book
    public function issueBook(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'student_id' => 'required|exists:students,id',
            'return_date' => 'required|date|after:today'
        ]);

        $book = Book::find($request->book_id);

        // Stock Check
        if ($book->quantity < 1) {
            return back()->withErrors(['error' => 'Book Out of Stock!']);
        }

        BookIssue::create([
            'book_id' => $request->book_id,
            'student_id' => $request->student_id,
            'issue_date' => now(),
            'return_date' => $request->return_date,
            'status' => 'issued'
        ]);

        $book->decrement('quantity'); // Stock kam karo
        return back()->with('success', 'Book Issued!');
    }

    // 4. Return Book & Calc Fine
    public function returnBook(Request $request, $id)
    {
        $issue = BookIssue::findOrFail($id);

        $returnedOn = Carbon::now();
        $fine = 0;

        // Logic: Agar late hua to 10 Rs per day fine
        if ($returnedOn->gt($issue->return_date)) {
            $daysLate = $returnedOn->diffInDays($issue->return_date);
            $fine = $daysLate * 10; // Rs 10 per day
        }

        $issue->update([
            'returned_on' => $returnedOn,
            'fine_amount' => $fine,
            'status' => 'returned'
        ]);

        $issue->book->increment('quantity'); // Stock wapis barhao

        return back()->with('success', "Book Returned. Fine: Rs $fine");
    }
}