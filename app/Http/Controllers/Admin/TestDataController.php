<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\Message\UnreadMessage;
use App\Models\Category;
use App\Models\FriendRequest;
use App\Models\Job;
use App\Models\JobAlert;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TestDataController extends Controller
{
    // public function index(Request $request)
    // {

    //     $view = $request?->view ?? '';

    //     $data = [];

    //     $date_range = date(now()->subDay());

    //     $unreadQuery = Message::whereDate('created_at', '<=', $date_range)
    //         ->whereIn('type', ['private', 'job'])
    //         ->whereNull('read_at')
    //         ->select('receiver_id', DB::raw('count(*) as message_count'))
    //         ->groupBy('receiver_id');

    //     $unreadMessages = $unreadQuery->get();

    //     foreach ($unreadMessages as $unreadMessage) {

    //         if (!$unreadMessage?->receiver) {
    //             continue;
    //         }

    //         $recipient = $unreadMessage->receiver;
    //         $unreadMessageCount = $unreadMessage->message_count;

    //         // Get the oldest contacts who sent messages to the user
    //         $oldestmessages = Message::select('sender_id', DB::raw('MAX(created_at) as max_created_at'))
    //             ->where('receiver_id', $unreadMessage->receiver_id)
    //             ->whereIn('type', ['private', 'job'])
    //             ->whereNull('read_at')
    //             ->whereDate('created_at', '<=', $date_range)
    //             ->groupBy('sender_id')
    //             ->take(5)
    //             ->orderBy('max_created_at', 'desc')
    //             ->with('sender')
    //             ->get();

    //         $recent_messages = [];

    //         foreach ($oldestmessages as $msg) {
    //             $recent_messages[] = [
    //                 'name' => $msg->sender->first_name,
    //                 'profile_url' => env('FRONTEND_URL') . '/profile/' . $msg->sender->id,
    //                 'profile_picture' => get_profile_picture($msg->sender),
    //                 'message_time' => \Carbon\Carbon::parse($msg->max_created_at)->diffForHumans(),
    //             ];
    //         }

    //         array_push($data, [
    //             'recipient_id' => $recipient->id,
    //             'recipient' => $recipient->first_name,
    //             'unread_message_count' => $unreadMessageCount,
    //             'recent_messages' => $recent_messages,

    //         ]);
    //     }

    //     if (strlen($view) > 0 && is_numeric($view)) {
    //         return new UnreadMessage($data[$view]);
    //     }

    //     return view('pages.test_data.index', ['data' => $data]);
    // }

    // public function index(Request $request)
    // {
    //     $job = json_decode(json_encode(array('category_id' => $request?->cid ?? 0)), FALSE);

    //     $categories = [];

    //     $category = Category::where('id', $job->category_id)->first();
    //     // return view('pages.test_data.index', ['data' => $category]);

    //     $group_categories = Category::where('group_name', $category->name)->get();

    //     if (count($group_categories) > 0) {
    //         for ($i = 0; $i < count($group_categories); $i++) {
    //             $categories[$i] = $group_categories[$i]->id;
    //         }
    //     } else {
    //         $categories[0] = $category->id;
    //     }

    //     // return view('pages.test_data.index', ['data' => $categories]);

    //     $data = JobAlert::with('user')->whereIn('category_id', $categories)->where('status', 1)->get();
    //     return view('pages.test_data.index', ['data' => $data]);
    // }

    public function index(Request $request)
    {
        $receiver = json_decode(json_encode(array(
            'first_name' => 'John',
            'last_name' => 'Doe',
            'slug' => 'john-doe',
            'role' => 'creative',
            'username' => 'johndoe'
        )), FALSE);

        $sender = json_decode(json_encode(array(
            'first_name' => 'Creative',
            'last_name' => 'Two',
            'slug' => 'creative-two',
            'role' => 'creative',
            'username' => 'creative-two',
        )), FALSE);

        if ($sender->role == 'creative') {
            $profile_url = '/creative/' . $sender?->slug ?? '';
        } elseif ($sender->role == 'agency') {
            $profile_url = '/agency/' . $sender?->slug ?? '';
        } else {
            $profile_url = $sender->username;
        }

        $data = [
            'receiver' => $receiver,
            'data' => [
                'recipient' => $receiver->first_name,
                'inviter' => $sender->first_name,
                'iniviter_profile' => sprintf("%s%s", env('FRONTEND_URL'), $profile_url),
                'APP_NAME' => env('APP_NAME'),
                'FRONTEND_URL' => env('FRONTEND_URL'),
            ],
        ];
        return view('emails.friendship.request', ['data' => $data['data']]);
    }

    public function testFr(Request $request)
    {
        $date_range = date(now()->subDay());
        $friendRequests = FriendRequest::where('status', 'pending')
            // ->where('email_due', '=', $date_range)
            ->get();
        return view('pages.test_data.index', ['data' => $friendRequests]);
    }
}