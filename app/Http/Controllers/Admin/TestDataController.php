<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\Message\UnreadMessage;
use App\Models\Job;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TestDataController extends Controller
{
    public function index(Request $request)
    {

        $data = [];

        $date_range = date(now()->subDay());

        $unreadQuery = Message::whereDate('created_at', '<=', $date_range)
            ->whereIn('type', ['private', 'job'])
            ->whereNull('read_at')
            ->select('receiver_id', DB::raw('count(*) as message_count'))
            ->groupBy('receiver_id');

        $unreadMessages = $unreadQuery->get();

        foreach ($unreadMessages as $unreadMessage) {

            if (!$unreadMessage?->receiver) {
                continue;
            }

            $recipient = $unreadMessage->receiver;
            $unreadMessageCount = $unreadMessage->message_count;

            // Get the oldest contacts who sent messages to the user
            $oldestmessages = Message::select('sender_id', DB::raw('MIN(created_at) as max_created_at'))
                ->where('receiver_id', $unreadMessage->receiver_id)
                ->whereIn('type', ['private', 'job'])
                ->whereNull('read_at')
                ->whereDate('created_at', '<=', $date_range)
                ->groupBy('sender_id')
                ->take(5)
                ->orderBy('max_created_at', 'desc')
                ->with('sender')
                ->get();

            $recent_messages = [];

            foreach ($oldestmessages as $msg) {
                $recent_messages[] = [
                    'name' => $msg->sender->first_name,
                    'profile_url' => env('FRONTEND_URL') . '/profile/' . $msg->sender->id,
                    'profile_picture' => get_profile_picture($msg->sender),
                    'message_time' => \Carbon\Carbon::parse($msg->max_created_at)->diffForHumans(),
                ];
            }

            array_push($data, [
                'recipient' => $recipient,
                'unread_message_count' => $unreadMessageCount,
                'recent_messages' => $recent_messages,
            ]);
        }

        return new UnreadMessage($data[10]);
        // return view('pages.test_data.index', ['data' => $data]);
    }
}
