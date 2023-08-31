<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    public function index()
    {
        return view('pages.groups.index');
    }

    public function create()
    {
        return view('pages.groups.add');
    }

    public function store(Request $request)
    {
        if ($request->hasFile('cover_image')) {
            $attachment = $this->storeImage($request);
        }

        Group::create([
            'uuid' => Str::uuid(),
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'attachment_id' => isset($attachment) ? $attachment->id : null,
        ]);

        Session::flash('success', 'Group created successfully');

        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $group = Group::findOrFail($id);

        $group->name = $request->input('name');
        $group->description = $request->input('description');
        $group->status = $request->input('status');

        if ($request->hasFile('cover_image')) {
            if ($group->attachment_id) {
                $oldAttachment = Attachment::find($group->attachment_id);

                if ($oldAttachment) {
                    // Storage::disk('public')->delete($oldAttachment->path);
                    $oldAttachment->delete();
                }

                $attachment = $this->storeImage($request);
                $group->attachment_id = $attachment->id;
            }
        }

        // Save changes
        $group->save();

        Session::flash('success', 'Group updated successfully');

        return back();
    }

    public function details(Group $group)
    {
        $group->load(['attachment', 'members.user']);
        // dd($group);
        // dd($group->attachment->path);
        return view('pages.groups.detail', compact('group'));
    }

    public function storeImage($request)
    {
        $uuid = Str::uuid();
        $file = $request->cover_image;
        $resource_type = 'cover_image';

        $extension = $file->getClientOriginalExtension();
        $filename = $uuid.'.'.$extension;
        $file_path = Storage::disk('public')->putFileAs($resource_type, $file, $filename);

        $attachment = Attachment::create([
            'uuid' => $uuid,
            'user_id' => auth()->id(),
            'resource_type' => $resource_type,
            'path' => $file_path,
            'extension' => $extension,
        ]);

        return $attachment;
    }

    public function add_new_member(Request $request)
    {
        GroupMember::create([
            'uuid' => Str::uuid(),
            'group_id' => $request->group_id,
            'user_id' => $request->user_id,
            'role' => 0,
            'joined_at' => now(),
        ]);
    }

    public function update_member_role(Request $request)
    {
        $groupMember = GroupMember::findOrFail($request->member_id);
        $groupMember->role = $request->new_role;

        return ($groupMember->save()) ? true : false;
    }
}
