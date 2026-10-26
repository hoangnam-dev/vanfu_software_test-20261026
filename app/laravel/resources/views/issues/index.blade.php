<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Issue list</title>
</head>

<body>
    <h2>Issue list</h2>

    <p>
        <a href="{{ route('issues.create') }}">Add issue</a>
    </p>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Detail</th>
                <th>Tag</th>
                <th>User email</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($issues as $issue)
                <tr>
                    <td>{{ $issue->id }}</td>
                    <td>{{ $issue->subject }}</td>
                    <td>{{ $issue->description }}</td>

                    <td>
                        {{
                            $tagsByIssue
                                ->get($issue->id, collect())
                                ->pluck('title')
                                ->implode(', ')
                        }}
                    </td>

                    <td>{{ $issue->email }}</td>

                    <td>
                        <a href="{{ route('issues.edit', $issue->id) }}">
                            Update
                        </a>

                        <form
                            action="{{ route('issues.destroy', $issue->id) }}"
                            method="POST"
                            onsubmit="return confirm('Delete this issue?')"
                        >
                            @csrf
                            @method('DELETE')

                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No issues found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>