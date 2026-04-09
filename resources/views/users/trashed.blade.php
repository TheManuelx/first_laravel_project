<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deleted Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('users.index') }}">User Management</a>
            <div class="navbar-nav">
                <a class="nav-link" href="{{ route('users.index') }}">Active Users</a>
                <a class="nav-link" href="{{ route('users.trashed') }}">Deleted Users</a>
                <a class="nav-link" href="/">Home</a>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h1>Deleted Users (Can be restored within 30 days)</h1>
        <a href="{{ route('users.index') }}" class="btn btn-primary mb-3">Back to Users</a>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Deleted At</th>
                    <th>Days Left</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trashedUsers as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->deleted_at->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @php
                                $daysLeft = 30 - $user->deleted_at->diffInDays(now());
                            @endphp
                            {{ $daysLeft }} days
                        </td>
                        <td>
                            @if($daysLeft > 0)
                                <form action="{{ route('users.restore', $user->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm">Restore</button>
                                </form>
                            @endif
                            <form action="{{ route('users.forceDelete', $user->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to permanently delete this user? This action cannot be undone.')">Permanently Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($trashedUsers->isEmpty())
            <p class="text-muted">No deleted users found.</p>
        @endif
    </div>
</body>
</html>