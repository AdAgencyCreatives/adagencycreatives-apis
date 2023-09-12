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
        $group = Group::create([
            'uuid' => Str::uuid(),
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        if ($request->hasFile('file')) {
            $attachment = storeImage($request, auth()->id(), 'cover_image');

            if (isset($attachment) && is_object($attachment)) {
                Attachment::whereId($attachment->id)->update([
                    'resource_id' => $group->id,
                ]);
            }
        }

        Session::flash('success', 'Group created successfully');

        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $group = Group::findOrFail($id);

        $group->name = $request->input('name');
        $group->description = $request->input('description');
        $group->status = $request->input('status');
        $group->save();

        if ($request->hasFile('file')) {
            if ($group->attachment) {
                $group->attachment->delete();
            }
            $attachment = storeImage($request, auth()->id(), 'cover_image');
            if (isset($attachment) && is_object($attachment)) {
                Attachment::whereId($attachment->id)->update([
                    'resource_id' => $group->id,
                ]);
            }
        }

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

    public function storeImage($request, $resource_type)
    {
        $uuid = Str::uuid();
        $file = $request->file;

        $extension = $file->getClientOriginalExtension();
        $folder = $resource_type.'/'.$uuid;
        $filePath = Storage::disk('s3')->put($folder, $file);

        $attachment = Attachment::create([
            'uuid' => $uuid,
            'user_id' => auth()->id(),
            'resource_type' => $resource_type,
            'path' => $filePath,
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
