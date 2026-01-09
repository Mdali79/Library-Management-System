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
        if ($user->role !== 'Student') {
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

        if ($user->role !== 'Student') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message = trim($request->input('message', ''));

        if (empty($message)) {
            return response()->json([
                'reply' => 'Please enter a message.',
                'type' => 'error'
            ]);
        }

        $messageLower = strtolower($message);

        // FIRST: Load Q&A from CSV file and check if message matches any Q&A
        $qaData = $this->loadQAFromCSV();
        $reply = $this->checkQA($message, $qaData);

        if ($reply) {
            return response()->json([
                'reply' => $reply,
                'type' => 'qa'
            ]);
        }

        // SECOND: Check if message is asking about a book (only after Q&A check fails)
        // More specific book query detection - only trigger on clear book search patterns
        $bookQueryPatterns = [
            'is.*available',
            'do you have.*book',
            'find.*book',
            'search.*book',
            'show me.*book',
            'can i get.*book',
            'is.*book.*available',
            'book.*available',
            'available.*book',
            'find.*by',
            'search for.*',
            'show.*book',
            'get.*book'
        ];

        $isBookQuery = false;
        $words = explode(' ', trim($message));

        // Check for specific book query patterns
        foreach ($bookQueryPatterns as $pattern) {
            if (preg_match('/' . $pattern . '/i', $message)) {
                $isBookQuery = true;
                break;
            }
        }

        // If message is a single word or short phrase (likely a book name), treat as book query
        if (!$isBookQuery && count($words) <= 3 && count($words) > 0) {
            // Check if it doesn't start with question words
            $firstWord = strtolower($words[0]);
            if (!in_array($firstWord, ['what', 'how', 'when', 'where', 'why', 'who', 'can', 'do', 'does', 'is', 'are', 'tell', 'explain'])) {
                $isBookQuery = true;
            }
        }

        // If it's likely a book query, search books
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

        // Default reply - show available questions
        $availableQuestions = $this->getAvailableQuestions($qaData);
        return response()->json([
            'reply' => $availableQuestions,
            'type' => 'suggestions',
            'questions' => $this->getQuestionsList($qaData)
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
        $messageLower = strtolower(trim($message));
        $messageWords = array_filter(explode(' ', $messageLower), function ($word) {
            return strlen(trim($word)) > 2; // Filter out very short words
        });

        $bestMatch = null;
        $bestScore = 0;

        foreach ($qaData as $qa) {
            $questionLower = strtolower(trim($qa['question']));
            $questionWords = array_filter(explode(' ', $questionLower), function ($word) {
                return strlen(trim($word)) > 2;
            });

            $score = 0;

            // Exact match (highest priority)
            if ($messageLower === $questionLower) {
                return $qa['answer'];
            }

            // Check if message contains the full question
            if (strpos($messageLower, $questionLower) !== false || strpos($questionLower, $messageLower) !== false) {
                $score += 100;
            }

            // Count matching significant words
            $matchedWords = 0;
            foreach ($questionWords as $qWord) {
                if (in_array($qWord, $messageWords)) {
                    $matchedWords++;
                    $score += 10;
                } elseif (strpos($messageLower, $qWord) !== false) {
                    $matchedWords++;
                    $score += 5; // Partial match gets lower score
                }
            }

            // If most words match, it's a good match
            if ($matchedWords > 0 && count($questionWords) > 0) {
                $matchRatio = $matchedWords / count($questionWords);
                $score += $matchRatio * 50;
            }

            // If score is high enough, this is a good match
            if ($score > $bestScore && ($matchedWords >= 2 || $score >= 30)) {
                $bestScore = $score;
                $bestMatch = $qa['answer'];
            }
        }

        return $bestMatch;
    }

    /**
     * Get formatted list of available questions for suggestions
     */
    private function getAvailableQuestions($qaData)
    {
        $message = "I'm sorry, I couldn't find any information about that. 😔\n\n";
        $message .= "Here are some questions you can ask me:\n\n";
        
        $questionNumber = 1;
        foreach ($qaData as $qa) {
            $message .= "{$questionNumber}. " . $qa['question'] . "\n";
            $questionNumber++;
        }
        
        $message .= "\n💡 You can also:\n";
        $message .= "- Search for CSE books by name (e.g., 'Is Introduction to Algorithms available?')\n";
        $message .= "- Search by author name\n";
        $message .= "- Search by ISBN\n";
        $message .= "- Ask about book availability\n\n";
        $message .= "Please select one of the questions above or try a book search! 📚";
        
        return $message;
    }

    /**
     * Get list of questions for frontend display
     */
    private function getQuestionsList($qaData)
    {
        return array_map(function($qa) {
            return $qa['question'];
        }, $qaData);
    }

    /**
     * Search for CSE department books with fuzzy matching
     */
    private function searchCSEBooks($query)
    {
        // Get all CSE-related categories dynamically
        $cseCategoryKeywords = [
            'Computer Science',
            'Computer Sciences',
            'Programming',
            'Software Engineering',
            'Data Structures',
            'Algorithms',
            'Database',
            'Web Development',
            'Machine Learning',
            'Artificial Intelligence',
            'Programming Languages',
            'Data Structures & Algorithms',
            'Database Systems',
            'Computer Networks',
            'Operating Systems',
            'Mobile Development',
            'Cybersecurity',
            'Cloud Computing',
            'Computer Architecture',
            'Software Testing',
            'Project Management'
        ];

        // Get all categories that match CSE keywords
        $cseCategories = category::where(function ($q) use ($cseCategoryKeywords) {
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
        $queryWords = array_filter($queryWords, function ($word) {
            return strlen(trim($word)) > 2; // Filter out very short words
        });

        // First, try exact and partial matches
        $books = book::with(['auther', 'authors', 'category', 'publisher'])
            ->whereIn('category_id', $cseCategories)
            ->where(function ($q) use ($query, $queryWords) {
                // Exact match on book name
                $q->where('name', 'like', "%{$query}%")
                    // Match on individual words
                    ->orWhere(function ($q) use ($queryWords) {
                        foreach ($queryWords as $word) {
                            $q->where('name', 'like', "%{$word}%");
                        }
                    })
                    // Match on ISBN
                    ->orWhere('isbn', 'like', "%{$query}%")
                    // Match on description
                    ->orWhere('description', 'like', "%{$query}%")
                    // Match on old single author (backward compatibility)
                    ->orWhereHas('auther', function ($q) use ($query, $queryWords) {
                        $q->where('name', 'like', "%{$query}%");
                        foreach ($queryWords as $word) {
                            $q->orWhere('name', 'like', "%{$word}%");
                        }
                    })
                    // Match on multiple authors
                    ->orWhereHas('authors', function ($q) use ($query, $queryWords) {
                        $q->where('name', 'like', "%{$query}%");
                        foreach ($queryWords as $word) {
                            $q->orWhere('name', 'like', "%{$word}%");
                        }
                    });
            })
            ->get();

        // If no results, try fuzzy matching with similarity
        if ($books->isEmpty() && !empty($queryWords)) {
            $allCSEBooks = book::with(['auther', 'authors', 'category', 'publisher'])
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

                // Check author name too (check all authors)
                $authorMatch = false;
                $allAuthors = $book->authors ?? collect();
                if ($allAuthors->isEmpty() && $book->auther) {
                    $allAuthors = collect([$book->auther]);
                }
                foreach ($allAuthors as $author) {
                    $authorNameLower = strtolower($author->name);
                    foreach ($queryWords as $word) {
                        if (stripos($authorNameLower, strtolower(trim($word))) !== false) {
                            $authorMatch = true;
                            break 2; // Break both loops
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
            usort($matchedBooks, function ($a, $b) {
                return $b['score'] <=> $a['score'];
            });

            // Get top 5 matches
            $books = collect(array_slice($matchedBooks, 0, 5))->pluck('book');
        }

        if ($books->isEmpty()) {
            return "Sorry, I couldn't find any CSE department books matching '{$query}'. Please try:\n- A different book name\n- Author name\n- ISBN number\n- Or ask: 'What CSE books are available?'";
        }

        // Sort books: available first, then by name
        $books = $books->sortBy(function ($book) {
            return [$book->available_quantity == 0, $book->name];
        });

        $response = "I found " . $books->count() . " CSE department book(s) for '{$query}':\n\n";

        $booksData = [];
        foreach ($books as $book) {
            $availability = $book->available_quantity > 0 ? 'Available' : 'Not Available';
            $availabilityClass = $book->available_quantity > 0 ? 'success' : 'danger';
            $availabilityIcon = $book->available_quantity > 0 ? '✅' : '❌';

            // Get all authors
            $allAuthors = $book->authors ?? collect();
            if ($allAuthors->isEmpty() && $book->auther) {
                $allAuthors = collect([$book->auther]);
            }
            $authorsList = $allAuthors->map(function ($author) {
                $labels = [];
                if ($author->pivot && $author->pivot->is_main_author) {
                    $labels[] = 'Main';
                }
                if ($author->pivot && $author->pivot->is_corresponding_author) {
                    $labels[] = 'Corresponding';
                }
                $label = !empty($labels) ? ' (' . implode(', ', $labels) . ')' : '';
                return $author->name . $label;
            })->join(', ');

            $response .= "{$availabilityIcon} <strong>" . $book->name . "</strong>\n";
            $response .= "   👤 Author(s): " . ($authorsList ?: 'N/A') . "\n";
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
                'author' => $authorsList ?: 'N/A',
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
