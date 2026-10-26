<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IssueController extends Controller
{
    public function index()
    {
        $issues = DB::table('issues')
            ->join('users', 'users.id', '=', 'issues.user_id')
            ->select([
                'issues.id',
                'issues.subject',
                'issues.description',
                'users.email',
            ])
            ->orderByDesc('issues.id')
            ->get();

        $tagsByIssue = DB::table('issues_tags')
            ->join('tags', 'tags.id', '=', 'issues_tags.tag_id')
            ->select([
                'issues_tags.issue_id',
                'tags.title',
            ])
            ->orderBy('tags.title')
            ->get()
            ->groupBy('issue_id');

        return view('issues.index', [
            'issues' => $issues,
            'tagsByIssue' => $tagsByIssue,
        ]);
    }

    public function create()
    {
        return view(
            'issues.create',
            [
                'users' => DB::table('users')->get(['id', 'email']),
                'tags' => DB::table('tags')->get(['id', 'title']),
            ]
        );
    }

    public function store(Request $request)
    {
        $dt = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'description' => ['nullable', 'string'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['integer', 'distinct', 'exists:tags,id'],
        ]);

        DB::transaction(function () use ($dt): void {
            $issueId = DB::table('issues')->insertGetId([
                'user_id' => $dt['user_id'],
                'subject' => $dt['subject'],
                'description' => $dt['description'] ?? null,
            ]);

            $issueTags = array_map(
                fn($tagId) => ['issue_id' => $issueId, 'tag_id' => $tagId],
                $dt['tags'] ?? []
            );

            if ($issueTags !== []) {
                DB::table('issues_tags')->insert($issueTags);
            }
        });

        return redirect()->route('issues.create')->with('status', 'Created successfully');
    }

    public function edit(int $id)
    {
        $issue = DB::table('issues')
            ->where('id', $id)
            ->first();

        abort_if($issue === null, 404);

        $selectedTagIds = DB::table('issues_tags')
            ->where('issue_id', $id)
            ->pluck('tag_id')
            ->all();

        return view('issues.edit', [
            'issue' => $issue,
            'users' => DB::table('users')->get(['id', 'email']),
            'tags' => DB::table('tags')->get(['id', 'title']),
            'selectedTagIds' => $selectedTagIds,
        ]);
    }

    public function update(Request $request, int $id)
    {
        abort_unless(
            DB::table('issues')->where('id', $id)->exists(),
            404
        );

        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['integer', 'distinct', 'exists:tags,id'],
        ]);

        DB::transaction(function () use ($data, $id): void {
            DB::table('issues')
                ->where('id', $id)
                ->update([
                    'user_id' => $data['user_id'],
                    'subject' => $data['subject'],
                    'description' => $data['description'] ?? null,
                ]);

            DB::table('issues_tags')
                ->where('issue_id', $id)
                ->delete();

            $issueTags = array_map(
                fn($tagId) => [
                    'issue_id' => $id,
                    'tag_id' => $tagId,
                ],
                $data['tags'] ?? []
            );

            if ($issueTags !== []) {
                DB::table('issues_tags')->insert($issueTags);
            }
        });

        return redirect()
            ->route('issues.index')
            ->with('status', 'Updated successfully');
    }

    public function destroy(int $id)
    {
        $deleted = DB::table('issues')
            ->where('id', $id)
            ->delete();

        abort_if($deleted === 0, 404);

        return redirect()
            ->route('issues.index')
            ->with('status', 'Deleted successfully');
    }
}
