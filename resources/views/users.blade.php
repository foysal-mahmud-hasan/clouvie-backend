<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waitlist Users - Clouvie</title>
    
    
    <style>
        /* CSS Styling - Makes the page look beautiful */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }
        
        .header h1 {
            font-size: 3em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }
        
        .stats {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            color: white;
            text-align: center;
            font-size: 1.3em;
        }
        
        .users-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .user-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: fadeIn 0.5s ease-in;
        }
        
        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2em;
            font-weight: bold;
            margin: 0 auto 15px;
            text-transform: uppercase;
        }
        
        .user-name {
            font-size: 1.5em;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            text-align: center;
        }
        
        .user-email {
            color: #666;
            font-size: 0.95em;
            margin-bottom: 12px;
            text-align: center;
            word-break: break-word;
        }
        
        .user-date {
            color: #999;
            font-size: 0.85em;
            text-align: center;
            padding-top: 12px;
            border-top: 1px solid #eee;
        }
        
        .user-id {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            margin-bottom: 10px;
        }
        
        .empty-state {
            text-align: center;
            color: white;
            padding: 60px 20px;
        }
        
        .empty-state h2 {
            font-size: 2em;
            margin-bottom: 15px;
        }
        
        .empty-state p {
            font-size: 1.2em;
            opacity: 0.8;
        }
        
        .api-info {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin-top: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .api-info h2 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 1.8em;
        }
        
        .api-endpoint {
            background: #f5f5f5;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
        }
        
        .api-endpoint strong {
            color: #764ba2;
            display: block;
            margin-bottom: 8px;
        }
        
        .method {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.9em;
            font-weight: bold;
            margin-right: 10px;
        }
        
        .method.post {
            background: #4caf50;
            color: white;
        }
        
        .method.get {
            background: #2196F3;
            color: white;
        }
        
        .code-block {
            background: #282c34;
            color: #abb2bf;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <h1>👥 Registered Users</h1>
            <p>Complete list of all registered users in the system</p>
        </div>
        
        <!-- Statistics -->
        <div class="stats">
            <strong>Total Users:</strong> {{ count($users) }}
        </div>
        
        <!-- Users Grid -->
        @if(count($users) > 0)
            {{-- 
                BLADE DIRECTIVE: @if checks if there are users
                If count($users) > 0, show the grid
                Otherwise, show empty state
            --}}
            <div class="users-grid">
                @foreach($users as $user)
                    {{-- 
                        BLADE DIRECTIVE: @foreach loops through each user
                        $user contains: id, name, email, created_at
                    --}}
                    <div class="user-card">
                        <!-- User Avatar with First Letter -->
                        <div class="user-avatar">
                            {{ substr($user->name, 0, 1) }}
                            {{-- substr() gets the first letter of the name --}}
                        </div>
                        
                        <!-- User ID Badge -->
                        <div style="text-align: center;">
                            <span class="user-id">ID: {{ $user->id }}</span>
                        </div>
                        
                        <!-- User Information -->
                        <div class="user-name">{{ $user->name }}</div>
                        <div class="user-email">📧 {{ $user->email }}</div>
                        <div class="user-date">
                            📅 Joined: {{ $user->created_at->format('M d, Y') }}
                            {{-- 
                                created_at is a Carbon instance (Laravel's date library)
                                format() converts it to readable format
                            --}}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State - Shows when no users exist -->
            <div class="empty-state">
                <h2>No Users Yet</h2>
                <p>Start by registering users through the API endpoint below</p>
            </div>
        @endif
        
        <!-- API Documentation Section -->
        <div class="api-info">
            <h2>📡 API Endpoints</h2>
            <p style="color: #666; margin-bottom: 20px;">
                Use these endpoints to interact with the user system from your frontend application, 
                mobile app, or any HTTP client like Postman.
            </p>
            
            <!-- Register Endpoint -->
            <div class="api-endpoint">
                <strong>
                    <span class="method post">POST</span>
                    Register New User
                </strong>
                <div style="margin-top: 10px; color: #333;">
                    <strong>URL:</strong> {{ url('/api/register') }}
                </div>
                <div style="margin-top: 10px; color: #666;">
                    <strong>Request Body (JSON):</strong>
                </div>
                <div class="code-block">
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123"
}
                </div>
                <div style="margin-top: 10px; color: #666;">
                    <strong>Example using cURL:</strong>
                </div>
                <div class="code-block">
curl -X POST {{ url('/api/register') }} \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password123"}'
                </div>
            </div>
            
            <!-- Get Users Endpoint -->
            <div class="api-endpoint">
                <strong>
                    <span class="method get">GET</span>
                    Get All Users
                </strong>
                <div style="margin-top: 10px; color: #333;">
                    <strong>URL:</strong> {{ url('/api/users') }}
                </div>
                <div style="margin-top: 10px; color: #666;">
                    <strong>Example using cURL:</strong>
                </div>
                <div class="code-block">
curl -X GET {{ url('/api/users') }}
                </div>
                <div style="margin-top: 10px; color: #666;">
                    <strong>Example using JavaScript (Fetch API):</strong>
                </div>
                <div class="code-block">
fetch('{{ url('/api/users') }}')
    .then(response => response.json())
    .then(data => console.log(data))
    .catch(error => console.error('Error:', error));
                </div>
            </div>
            
            <!-- Tips Section -->
            <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin-top: 20px; border-radius: 5px;">
                <strong style="color: #856404;">💡 Beginner Tips:</strong>
                <ul style="margin-top: 10px; padding-left: 20px; color: #856404;">
                    <li>Always send Content-Type: application/json header for POST requests</li>
                    <li>Password must be at least 8 characters</li>
                    <li>Email must be unique - you can't register the same email twice</li>
                    <li>API returns JSON responses with 'success', 'message', and 'data' fields</li>
                    <li>Use tools like Postman or Insomnia to test APIs easily</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
