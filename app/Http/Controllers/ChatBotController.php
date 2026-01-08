<?php

namespace App\Http\Controllers;

use App\Models\book;
use App\Models\category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatBotController extends Controller
{
    /**
     * Show the chatbot interface
     */
    public function index()
    {
        // Only allow students to access chatbot
        $user = Auth::user();
        if (!in_array($user->role, ['Student', 'Teacher'])) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Chatbot is only available for students.']);
        }

        return view('chatbot.index');
    }

    /**
     * Handle chatbot messages
     */
    public function chat(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['Student', 'Teacher'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message = trim($request->input('message', ''));
        
        if (empty($message)) {
            return response()->json([
                'reply' => 'Please enter a message.',
                'type' => 'error'
            ]);
        }

        // Check if message is asking about a book (prioritize book search)
        $bookKeywords = ['book', 'available', 'have', 'find', 'search', 'is', 'are', 'can i get', 'do you have', 'show me'];
        $messageLower = strtolower($message);
        $isBookQuery = false;
        
        // Check if message contains book-related keywords or looks like a book name
        foreach ($bookKeywords as $keyword) {
            if (strpos($messageLower, $keyword) !== false) {
                $isBookQuery = true;
                break;
            }
        }
        
        // If message is longer than 3 words and doesn't start with common question words, likely a book name
        $words = explode(' ', trim($message));
        if (count($words) > 2 && !in_array(strtolower($words[0]), ['what', 'how', 'when', 'where', 'why', 'who', 'can', 'do', 'does', 'is', 'are'])) {
            $isBookQuery = true;
        }

        // If it's likely a book query, search books first
        if ($isBookQuery) {
            $bookResult = $this->searchCSEBooks($message);
            
            if ($bookResult && is_array($bookResult) && !empty($bookResult['books'])) {
                return response()->json([
                    'reply' => $bookResult['message'],
                    'type' => 'book_search',
                    'books' => $bookResult['books'] ?? []
                ]);
            } elseif ($bookResult && is_string($bookResult) && strpos($bookResult, "I found") !== false) {
                return response()->json([
                    'reply' => $bookResult,
                    'type' => 'book_search',
                    'books' => []
                ]);
            }
        }

        // Load Q&A from CSV file
        $qaData = $this->loadQAFromCSV();
        
        // Check if message matches any Q&A
        $reply = $this->checkQA($message, $qaData);
        
        if ($reply) {
            return response()->json([
                'reply' => $reply,
                'type' => 'qa'
            ]);
        }

        // If book query didn't find results, try book search again with original message
        if (!$isBookQuery) {
            $bookResult = $this->searchCSEBooks($message);
            
            if ($bookResult && is_array($bookResult) && !empty($bookResult['books'])) {
                return response()->json([
                    'reply' => $bookResult['message'],
                    'type' => 'book_search',
                    'books' => $bookResult['books'] ?? []
                ]);
            } elseif ($bookResult && is_string($bookResult) && strpos($bookResult, "I found") !== false) {
                return response()->json([
                    'reply' => $bookResult,
                    'type' => 'book_search',
                    'books' => []
                ]);
            }
        }

        // Default reply
        return response()->json([
            'reply' => "I'm sorry, I couldn't find any information about that. You can:\n- Ask about CSE department books by name\n- Ask general library questions\n- Try: 'Is [book name] available?' or 'Show me [book name]'",
            'type' => 'default'
        ]);
    }

    /**
     * Load Q&A data from CSV file
     */
    private function loadQAFromCSV()
    {
        $csvPath = storage_path('app/chatbot_qa.csv');
        
        // Create CSV file if it doesn't exist
        if (!file_exists($csvPath)) {
            $this->createDefaultCSV($csvPath);
        }

        $qaData = [];
        if (($handle = fopen($csvPath, "r")) !== FALSE) {
            // Skip header row
            fgetcsv($handle);
            
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) >= 2) {
                    $qaData[] = [
                        'question' => trim($data[0]),
                        'answer' => trim($data[1])
                    ];
                }
            }
            fclose($handle);
        }

        return $qaData;
    }

    /**
     * Create default CSV file with sample Q&A
     */
    private function createDefaultCSV($path)
    {
        $defaultQA = [
            ['Question', 'Answer'],
            ['What is the library timing?', 'The library is open from 9:00 AM to 6:00 PM, Monday to Friday.'],
            ['How many books can I borrow?', 'Students can borrow up to 5 books at a time.'],
            ['What is the fine for late return?', 'The fine for late return is $1 per day per book.'],
            ['How do I renew a book?', 'You can renew a book by visiting the library or through the online system before the due date.'],
            ['Can I reserve a book?', 'Yes, you can reserve a book if it is currently unavailable. You will be notified when it becomes available.'],
            ['What books are available in CSE department?', 'You can search for CSE department books using the chatbot. Just type the book name or author.'],
            ['How do I return a book?', 'You can return books at the library counter during library hours.'],
            ['What if I lose a book?', 'Please contact the librarian immediately. You may need to pay for the replacement cost.'],
        ];

        $file = fopen($path, 'w');
        foreach ($defaultQA as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
    }

    /**
     * Check if message matches any Q&A
     */
    private function checkQA($message, $qaData)
    {
        $messageLower = strtolower($message);
        
        foreach ($qaData as $qa) {
            $questionLower = strtolower($qa['question']);
            
            // Check if message contains keywords from question
            $questionWords = explode(' ', $questionLower);
            $matchedWords = 0;
            
            foreach ($questionWords as $word) {
                if (strlen($word) > 3 && strpos($messageLower, $word) !== false) {
                    $matchedWords++;
                }
            }
            
            // If 2 or more significant words match, return the answer
            if ($matchedWords >= 2 || strpos($messageLower, $questionLower) !== false) {
                return $qa['answer'];
            }
        }
        
        return null;
    }

    /**
     * Search for CSE department books with fuzzy matching
     */
    private function searchCSEBooks($query)
    {
        // Get all CSE-related categories dynamically
        $cseCategoryKeywords = [
            'Computer Science', 'Computer Sciences', 'Programming', 'Software Engineering',
            'Data Structures', 'Algorithms', 'Database', 'Web Development', 'Machine Learning',
            'Artificial Intelligence', 'Programming Languages', 'Data Structures & Algorithms',
            'Database Systems', 'Computer Networks', 'Operating Systems', 'Mobile Development',
            'Cybersecurity', 'Cloud Computing', 'Computer Architecture', 'Software Testing',
            'Project Management'
        ];

        // Get all categories that match CSE keywords
        $cseCategories = category::where(function($q) use ($cseCategoryKeywords) {
            foreach ($cseCategoryKeywords as $keyword) {
                $q->orWhere('name', 'like', "%{$keyword}%");
            }
        })->pluck('id');

        if ($cseCategories->isEmpty()) {
            // Fallback: get all categories if no CSE categories found
            $cseCategories = category::pluck('id');
        }

        // Clean and prepare search query
        $query = trim($query);
        $queryWords = explode(' ', $query);
        $queryWords = array_filter($queryWords, function($word) {
            return strlen(trim($word)) > 2; // Filter out very short words
        });

        // First, try exact and partial matches
        $books = book::with(['auther', 'category', 'publisher'])
            ->whereIn('category_id', $cseCategories)
            ->where(function($q) use ($query, $queryWords) {
                // Exact match on book name
                $q->where('name', 'like', "%{$query}%")
                  // Match on individual words
                  ->orWhere(function($q) use ($queryWords) {
                      foreach ($queryWords as $word) {
                          $q->where('name', 'like', "%{$word}%");
                      }
                  })
                  // Match on ISBN
                  ->orWhere('isbn', 'like', "%{$query}%")
                  // Match on description
                  ->orWhere('description', 'like', "%{$query}%")
                  // Match on author name
                  ->orWhereHas('auther', function($q) use ($query, $queryWords) {
                      $q->where('name', 'like', "%{$query}%");
                      foreach ($queryWords as $word) {
                          $q->orWhere('name', 'like', "%{$word}%");
                      }
                  });
            })
            ->get();

        // If no results, try fuzzy matching with similarity
        if ($books->isEmpty() && !empty($queryWords)) {
            $allCSEBooks = book::with(['auther', 'category', 'publisher'])
                ->whereIn('category_id', $cseCategories)
                ->get();

            $matchedBooks = [];
            foreach ($allCSEBooks as $book) {
                $bookNameLower = strtolower($book->name);
                $queryLower = strtolower($query);
                
                // Calculate similarity percentage
                similar_text($bookNameLower, $queryLower, $similarity);
                
                // Check if query is contained in book name (exact substring match)
                $containsQuery = stripos($bookNameLower, $queryLower) !== false;
                
                // Check if any query words appear in book name
                $wordMatches = 0;
                $totalWordScore = 0;
                foreach ($queryWords as $word) {
                    $wordLower = strtolower(trim($word));
                    if (strlen($wordLower) > 2) {
                        if (stripos($bookNameLower, $wordLower) !== false) {
                            $wordMatches++;
                            // Longer words get higher score
                            $totalWordScore += strlen($wordLower);
                        }
                    }
                }
                
                // Check author name too
                $authorMatch = false;
                if ($book->auther) {
                    $authorNameLower = strtolower($book->auther->name);
                    foreach ($queryWords as $word) {
                        if (stripos($authorNameLower, strtolower(trim($word))) !== false) {
                            $authorMatch = true;
                            break;
                        }
                    }
                }
                
                // Scoring: exact match > word matches > similarity > author match
                $score = 0;
                if ($containsQuery) {
                    $score += 1000; // Highest priority for exact substring
                }
                $score += ($wordMatches * 100) + ($totalWordScore * 10);
                $score += $similarity;
                if ($authorMatch) {
                    $score += 50;
                }
                
                // If score is significant, include it
                if ($score > 50 || $wordMatches > 0 || $similarity > 30) {
                    $matchedBooks[] = [
                        'book' => $book,
                        'score' => $score,
                        'similarity' => $similarity,
                        'word_matches' => $wordMatches,
                        'contains_query' => $containsQuery
                    ];
                }
            }

            // Sort by score (highest first)
            usort($matchedBooks, function($a, $b) {
                return $b['score'] <=> $a['score'];
            });

            // Get top 5 matches
            $books = collect(array_slice($matchedBooks, 0, 5))->pluck('book');
        }

        if ($books->isEmpty()) {
            return "Sorry, I couldn't find any CSE department books matching '{$query}'. Please try:\n- A different book name\n- Author name\n- ISBN number\n- Or ask: 'What CSE books are available?'";
        }

        // Sort books: available first, then by name
        $books = $books->sortBy(function($book) {
            return [$book->available_quantity == 0, $book->name];
        });

        $response = "I found " . $books->count() . " CSE department book(s) for '{$query}':\n\n";
        
        $booksData = [];
        foreach ($books as $book) {
            $availability = $book->available_quantity > 0 ? 'Available' : 'Not Available';
            $availabilityClass = $book->available_quantity > 0 ? 'success' : 'danger';
            $availabilityIcon = $book->available_quantity > 0 ? '✅' : '❌';
            
            $response .= "{$availabilityIcon} <strong>" . $book->name . "</strong>\n";
            $response .= "   👤 Author: " . ($book->auther ? $book->auther->name : 'N/A') . "\n";
            $response .= "   📖 ISBN: " . ($book->isbn ?? 'N/A') . "\n";
            $response .= "   📚 Category: " . ($book->category ? $book->category->name : 'N/A') . "\n";
            $response .= "   📊 Status: <strong>" . $availability . "</strong>\n";
            $response .= "   📦 Available: " . $book->available_quantity . " of " . $book->total_quantity . " copies\n";
            if ($book->available_quantity > 0) {
                $response .= "   ✅ You can request this book!\n";
            } else {
                $response .= "   ⚠️ Currently unavailable - You can reserve it\n";
            }
            $response .= "\n";
            
            $booksData[] = [
                'id' => $book->id,
                'name' => $book->name,
                'author' => $book->auther ? $book->auther->name : 'N/A',
                'isbn' => $book->isbn ?? 'N/A',
                'category' => $book->category ? $book->category->name : 'N/A',
                'available_quantity' => $book->available_quantity,
                'total_quantity' => $book->total_quantity,
                'status' => $availability,
                'status_class' => $availabilityClass
            ];
        }

        return [
            'message' => $response,
            'books' => $booksData
        ];
    }
}
