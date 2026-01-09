@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="admin-heading">
                        <i class="fas fa-robot"></i> Library Chatbot - CSE Books Assistant
                    </h2>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card" style="height: 600px; display: flex; flex-direction: column;">
                        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <h5 class="mb-0">
                                <i class="fas fa-comments"></i> Chat with Library Assistant
                            </h5>
                            <small>Ask me about CSE department books or general library questions</small>
                        </div>
                        
                        <div class="card-body" style="flex: 1; overflow-y: auto; padding: 1.5rem; background: #f8f9fa;" id="chatMessages">
                            <!-- Welcome message -->
                            <div class="message bot-message mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; margin-right: 10px;">
                                        <i class="fas fa-robot"></i>
                                    </div>
                                    <div class="message-content bg-white p-3 rounded" style="max-width: 80%; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                        <p class="mb-0">Hello! 👋 I'm your Library Assistant. I can help you:</p>
                                        <ul class="mb-0 mt-2">
                                            <li>Search for CSE department books</li>
                                            <li>Check book availability</li>
                                            <li>Answer library-related questions</li>
                                        </ul>
                                        <p class="mb-0 mt-2">Try asking: "Is Introduction to Algorithms available?" or "What is the library timing?"</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer" style="background: white; border-top: 1px solid #dee2e6;">
                            <form id="chatForm" class="d-flex">
                                <input type="text" 
                                       id="messageInput" 
                                       class="form-control" 
                                       placeholder="Type your message here..." 
                                       autocomplete="off"
                                       required>
                                <button type="submit" class="btn btn-primary ml-2" id="sendButton">
                                    <i class="fas fa-paper-plane"></i> Send
                                </button>
                            </form>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Quick tips: Search books by name, author, or ISBN. Ask questions about library services.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .message {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .user-message .message-content {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white;
            margin-left: auto;
        }

        .bot-message .message-content {
            background: white;
            color: #333;
        }

        .message-content {
            word-wrap: break-word;
            line-height: 1.6;
        }

        .message-content ul {
            padding-left: 20px;
        }

        .message-content li {
            margin-bottom: 5px;
        }

        #chatMessages {
            scroll-behavior: smooth;
        }

        .book-result {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }

        .book-result.available {
            border-left-color: #4CAF50;
            background: #e8f5e9;
        }

        .book-result.unavailable {
            border-left-color: #f44336;
            background: #ffebee;
        }
    </style>

    <script>
        const chatForm = document.getElementById('chatForm');
        const messageInput = document.getElementById('messageInput');
        const chatMessages = document.getElementById('chatMessages');
        const sendButton = document.getElementById('sendButton');

        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            sendMessage();
        });

        function sendMessage() {
            const message = messageInput.value.trim();
            
            if (!message) {
                return;
            }

            // Add user message to chat
            addMessage(message, 'user');
            
            // Clear input
            messageInput.value = '';
            sendButton.disabled = true;
            sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

            // Send to server
            fetch('{{ route("chatbot.chat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                sendButton.disabled = false;
                sendButton.innerHTML = '<i class="fas fa-paper-plane"></i> Send';
                
                if (data.error) {
                    addMessage(data.error, 'bot', 'error');
                } else {
                    addMessage(data.reply, 'bot', data.type, data.books, data.questions);
                }
            })
            .catch(error => {
                sendButton.disabled = false;
                sendButton.innerHTML = '<i class="fas fa-paper-plane"></i> Send';
                addMessage('Sorry, there was an error processing your request. Please try again.', 'bot', 'error');
                console.error('Error:', error);
            });
        }

        function addMessage(text, sender, type = 'default', books = null, questions = null) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}-message mb-3`;
            
            const avatar = sender === 'user' 
                ? '<div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; margin-left: 10px; order: 2;"><i class="fas fa-user"></i></div>'
                : '<div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; margin-right: 10px;"><i class="fas fa-robot"></i></div>';
            
            let content = '';
            
            if (type === 'suggestions' && questions && questions.length > 0) {
                // Format suggestions with clickable questions
                const formattedText = text.replace(/\n/g, '<br>');
                content = '<div class="message-content bg-white p-3 rounded" style="max-width: 80%; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-left: 4px solid #ff9800;">';
                content += `<p class="mb-3">${formattedText}</p>`;
                content += '<div class="suggested-questions mt-3" style="border-top: 1px solid #dee2e6; padding-top: 15px;">';
                content += '<p class="mb-2" style="font-weight: bold; color: #667eea;"><i class="fas fa-lightbulb"></i> Click on any question to ask:</p>';
                questions.forEach((question, index) => {
                    const escapedQuestion = question.replace(/'/g, "&#39;").replace(/"/g, "&quot;");
                    content += `<div class="question-item mb-2 p-2 rounded" style="background: #f8f9fa; cursor: pointer; transition: all 0.2s; border: 1px solid #dee2e6;" 
                        onmouseover="this.style.background='#e9ecef'; this.style.borderColor='#667eea';" 
                        onmouseout="this.style.background='#f8f9fa'; this.style.borderColor='#dee2e6';"
                        onclick="askQuestion(${index})">
                        <i class="fas fa-question-circle text-primary"></i> ${question}
                    </div>`;
                });
                
                // Store questions in a global array for the onclick handler
                if (!window.suggestedQuestions) {
                    window.suggestedQuestions = [];
                }
                window.suggestedQuestions = questions;
                content += '</div>';
                content += '</div>';
            } else if (type === 'book_search' && books && books.length > 0) {
                content = '<div class="message-content bg-white p-3 rounded" style="max-width: 80%; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">';
                content += '<p class="mb-3"><strong>📚 Book Search Results:</strong></p>';
                
                books.forEach(book => {
                    const statusClass = book.available_quantity > 0 ? 'available' : 'unavailable';
                    const statusIcon = book.available_quantity > 0 ? '✅' : '❌';
                    content += `<div class="book-result ${statusClass}" style="margin-bottom: 15px; padding: 12px; border-radius: 6px;">`;
                    content += `<div style="font-size: 1.1em; font-weight: bold; margin-bottom: 8px;">${statusIcon} ${book.name}</div>`;
                    content += `<div style="margin-bottom: 5px;"><strong>👤 Author:</strong> ${book.author}</div>`;
                    if (book.category) {
                        content += `<div style="margin-bottom: 5px;"><strong>📂 Category:</strong> ${book.category}</div>`;
                    }
                    content += `<div style="margin-bottom: 5px;"><strong>📖 ISBN:</strong> ${book.isbn}</div>`;
                    content += `<div style="margin-bottom: 5px;"><strong>📊 Status:</strong> <span class="badge badge-${book.status_class}" style="padding: 4px 8px;">${book.status}</span></div>`;
                    content += `<div style="margin-bottom: 5px;"><strong>📦 Copies:</strong> ${book.available_quantity} available out of ${book.total_quantity} total</div>`;
                    if (book.available_quantity > 0) {
                        content += `<div style="color: #28a745; font-weight: bold; margin-top: 8px;">✅ You can request this book!</div>`;
                    } else {
                        content += `<div style="color: #dc3545; font-weight: bold; margin-top: 8px;">⚠️ Currently unavailable - You can reserve it</div>`;
                    }
                    content += '</div>';
                });
                
                content += '</div>';
            } else {
                // Format text with line breaks
                const formattedText = text.replace(/\n/g, '<br>');
                content = `<div class="message-content bg-white p-3 rounded" style="max-width: 80%; box-shadow: 0 2px 4px rgba(0,0,0,0.1); ${type === 'error' ? 'border-left: 4px solid #f44336;' : ''}">`;
                content += `<p class="mb-0">${formattedText}</p>`;
                content += '</div>';
            }
            
            if (sender === 'user') {
                messageDiv.innerHTML = '<div class="d-flex align-items-start justify-content-end">' + content + avatar + '</div>';
            } else {
                messageDiv.innerHTML = '<div class="d-flex align-items-start">' + avatar + content + '</div>';
            }
            
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Allow Enter key to send (but Shift+Enter for new line)
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Focus input on load
        messageInput.focus();

        // Function to ask a suggested question
        function askQuestion(index) {
            if (window.suggestedQuestions && window.suggestedQuestions[index]) {
                messageInput.value = window.suggestedQuestions[index];
                sendMessage();
            }
        }
    </script>
@endsection

