<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update issue</title>
</head>

<body>
    <h2>Update issue</h2>
    <p>
        <a href="{{ route('issues.index') }}">Issue list</a>
    </p>

    <form
        action="{{ route('issues.update', $issue->id) }}"
        method="POST">
        @csrf
        @method('PUT')

        <table cellpadding="8">
            <tr>
                <th align="left">
                    <label for="subject">Title</label>
                </th>

                <td>
                    <input id="subject" name="subject" type="text" maxlength="255" value="{{ old('subject', $issue->subject) }}" required>

                    @error('subject')
                    <div>{{ $message }}</div>
                    @enderror
                </td>
            </tr>

            <tr>
                <th align="left">
                    <label for="description">Detail</label>
                </th>
                <td>
                    <textarea id="description" name="description" rows="5" cols="30">{{ old('description', $issue->description) }}</textarea>

                    @error('description')
                    <div>{{ $message }}</div>
                    @enderror
                </td>
            </tr>

            <tr>
                <th align="left">
                    <label for="tags">Tag</label>
                </th>
                <td>
                    <select
                        id="tags"
                        name="tags[]"
                        size="3"
                        multiple>
                        @foreach ($tags as $tag)
                        <option value="{{ $tag->id }}" @selected(in_array($tag->id, old('tags', $selectedTagIds)))>
                            {{ $tag->title }}
                        </option>
                        @endforeach
                    </select>
                    @error('tags')
                    <div>{{ $message }}</div>
                    @enderror

                    @error('tags.*')
                    <div>{{ $message }}</div>
                    @enderror
                </td>
            </tr>
            <tr>
                <th align="left">
                    <label for="user_id">User email</label>
                </th>
                <td>
                    <select id="user_id" name="user_id" required>
                        @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(old('user_id', $issue->user_id) == $user->id)>
                            {{ $user->email }}
                        </option>
                        @endforeach
                    </select>
                    @error('user_id')
                    <div>{{ $message }}</div>
                    @enderror
                </td>
            </tr>

            <tr>
                <td></td>

                <td>
                    <button type="submit">Update</button>
                    <a href="{{ route('issues.index') }}">
                        Cancel
                    </a>
                </td>
            </tr>
        </table>
    </form>
</body>

</html>
