<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Agency\AgencyCollection;
use App\Http\Resources\Attachment\AttachmentResource;
use App\Http\Resources\Creative\LoggedinCreativeCollection;
use App\Http\Resources\Job\JobResource;
use App\Http\Resources\Post\TrendingPostCollection;
use App\Jobs\SendEmailJob;
use App\Mail\Account\ProfileCompletionCreativeReminder;
use App\Mail\Account\ProfileCompletionAgencyReminder;
use App\Mail\Application\JobClosed;
use App\Mail\Application\NewApplication;
use App\Mail\Job\NoJobPostedAgencyReminder;
use App\Mail\Message\UnreadMessage;
use App\Models\Agency;
use App\Models\Application;
use App\Models\ApplicationEmailLog;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Creative;
use App\Models\FriendRequest;
use App\Models\Job;
use App\Models\JobAlert;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use GifCreator\GifCreator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Response;
use Intervention\Image\ImageManagerStatic as Image;
use App\Mail\Account\AccountApproved;
use App\Mail\Account\AccountApprovedAgency;
use App\Mail\Account\AccountDenied;
use App\Mail\Account\NewUserRegistrationAgency;
use App\Mail\Account\NewUserRegistrationCreative;
use App\Mail\Application\ApplicationSubmitted;
use App\Mail\Application\Interested;
use App\Mail\Application\Removed;
use App\Mail\ContactFormMail;
use App\Mail\ContentUpdated\EmailUpdated;
use App\Mail\CustomPkg\HireAnAdvisorJobCompleted;
use App\Mail\CustomPkg\RequestAdminAlert;
use App\Mail\ErrorNotificationMail;
use App\Mail\Friend\FriendshipRequest;
use App\Mail\Friend\FriendshipRequestAccepted;
use App\Mail\Group\Invitation;
use App\Mail\Job\CustomJobRequestRejected;
use App\Mail\Job\Invitation as JobInvitation;
use App\Mail\Job\JobPostedApprovedAlertAllSubscribers;
use App\Mail\Job\NewJobPosted;
use App\Mail\JobPostExpiring\JobPostExpiringAdmin;
use App\Mail\JobPostExpiring\JobPostExpiringAgency;
use App\Mail\Order\ConfirmationAdmin;
use App\Mail\Post\LoungeMention;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;


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

        $date_range = now()->subDay()->format('Y-m-d');

        $friendRequests = FriendRequest::where('status', 'pending')
            ->whereDate('updated_at', '=', $date_range)
            ->orderBy('receiver_id')->orderByDesc('updated_at')
            ->get();

        $bundle = [];
        $receivers = [];

        foreach ($friendRequests as $fr) {
            $receiver = $fr->receiver;
            $sender = $fr->sender;

            $sender->profile_picture = get_profile_picture($sender);

            if (array_key_exists($receiver->id, $bundle)) {
                $bundle[$receiver->id][count($bundle)] = $sender;
            } else {
                $bundle[$receiver->id] = array(0 => $sender);
                $receivers[count($receivers)] = $receiver;
            }
        }

        foreach ($receivers as $receiver) {
            $senders = $bundle[$receiver->id];

            $data = [
                'receiver' => $receiver,
                'data' => [
                    'recipient' => $receiver->first_name,
                    'senders' => $senders,
                    'multiple' => count($senders) > 0,
                    'APP_NAME' => env('APP_NAME'),
                    'FRONTEND_URL' => env('FRONTEND_URL'),
                ],
            ];
            return view('emails.friendship.request', ['data' => $data['data']]);
        }
    }

    public function testFr(Request $request)
    {
        $date_range = now()->subDay()->format('Y-m-d');

        $friendRequests = FriendRequest::where('status', 'pending')
            ->whereDate('updated_at', '=', $date_range)
            ->orderBy('receiver_id')->orderByDesc('updated_at')
            ->get();

        $bundle = [];
        $receivers = [];

        foreach ($friendRequests as $fr) {
            $receiver = $fr->receiver;
            $sender = $fr->sender;

            $sender->profile_picture = get_profile_picture($sender);

            if (array_key_exists($receiver->id, $bundle)) {
                $bundle[$receiver->id][count($bundle)] = $sender;
            } else {
                $bundle[$receiver->id] = array(0 => $sender);
                $receivers[count($receivers)] = $receiver;
            }
        }

        foreach ($receivers as $receiver) {
            $senders = $bundle[$receiver->id];
            dd($senders);
        }
    }

    public function testThumb(Request $request)
    {
        $user_id = $request->user_id;

        if ($user_id) {
            $user = User::where('uuid', $user_id)->first();

            // $attachment = Attachment::where(['user_id' => $user->id, 'resource_type' => 'profile_picture'])->first();

            $profile_picture = getAttachmentBasePath() . $user->profile_picture->path;

            $info = pathinfo($profile_picture);
            // dd($info);

            $fname = $info['basename'];
            $thumbWidth = 150;
            $thumb_path = str_replace($info['filename'], $info['filename'] . "_thumb", $user->profile_picture->path);

            // dd($thumb_path);

            if (strtolower($info['extension']) == 'jpg') {

                // load image and get image size
                $img = \imagecreatefromjpeg("{$profile_picture}");
                $width = imagesx($img);
                $height = imagesy($img);

                // calculate thumbnail size
                $new_width = $thumbWidth;
                $new_height = floor($height * ($thumbWidth / $width));

                // create a new temporary image
                $tmp_img = imagecreatetruecolor($new_width, $new_height);

                // copy and resize old image into new image 
                imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

                $temp = tmpfile();
                // save thumbnail into a temp file
                imagejpeg($tmp_img, $temp);

                $filePath = Storage::disk('s3')->put($thumb_path, $temp);

                fclose($temp);

                return '<img src="' . getAttachmentBasePath() . $thumb_path . '" />';
            }
        }
        return "No-UUID";
    }

    public function testThumbAtt(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();

        // return new AttachmentResource($user->user_thumbnail);

        return new AttachmentResource(storeThumb($user, 'user_thumbnail'));
    }

    public function testThumbResampled(Request $request)
    {
        $user_id = $request->user_id;
        $thumbWidth = 250;

        if ($user_id) {
            $user = User::where('uuid', $user_id)->first();

            $original_image = getAttachmentBasePath() . $user?->portfolio_website_preview?->path;
            $extension = $user?->portfolio_website_preview?->extension;

            if (strtolower($extension) == 'png') {
                $img = \imagecreatefrompng("{$original_image}");
            } else if (strtolower($extension) == 'bmp') {
                $img = \imagecreatefrombmp("{$original_image}");
            } else if (strtolower($extension) == 'gif') {
                $img = \imagecreatefromgif("{$original_image}");
            } else {
                $img = \imagecreatefromjpeg("{$original_image}");
            }

            // get image size
            $width = imagesx($img);
            $height = imagesy($img);

            // calculate thumbnail size
            if ($width >= $height) {
                $new_width = $thumbWidth;
                $new_height = floor($height * ($thumbWidth / $width));
            } else {
                $new_height = $thumbWidth;
                $new_width = floor($width * ($thumbWidth / $height));
            }

            // create a new temporary image
            $tmp_img = imagecreatetruecolor($new_width, $new_height);

            if (strtolower($extension) == 'png') {
                imagefill($tmp_img, 0, 0, imagecolorallocate($tmp_img, 255, 255, 255));
                imagealphablending($tmp_img, TRUE);
            }

            // copy and resize old image into new image 
            imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

            imagefilter($tmp_img, IMG_FILTER_CONTRAST, -5);

            ob_start();
            // save thumbnail into a temp file
            imagejpeg($tmp_img, null, 100);

            $imageData = ob_get_contents();
            ob_end_clean();

            imagedestroy($tmp_img);
            imagedestroy($img);

            return '<img src="' . 'data:image/jpeg;charset=utf-8;base64,' . (strlen($original_image) > 0 ? base64_encode($imageData) : '') . '" />';
        }
        return "No-UUID";
    }

    public function testJobClosed(Request $request)
    {
        $apply_type = $request->apply_type ?? "Internal";

        if (env('APP_ENV') != 'production') {
            Job::whereIn('id', [171])->update(['apply_type' => $apply_type, 'expired_at' => now()->subDay(), 'updated_at' => now()->subDay(), 'status' => 3]);
        }

        $yesterday = now()->subDay()->toDateString();
        $today = now()->toDateString();

        $jobs = Job::where(function ($query) use ($yesterday, $today) {
            $query->where(function ($q) use ($yesterday, $today) {
                $q->where('status', 4)->whereDate('updated_at', '>=', $yesterday)->where('updated_at', '<', $today);
            })->orWhere(function ($q) use ($yesterday, $today) {
                $q->whereDate('expired_at', '>=', $yesterday)->where('expired_at', '<', $today);
            });
        })->with('applications', function ($query) {
            $query->where('status', 0);
        });

        if ($apply_type != '') {
            $jobs = $jobs->where('apply_type', $apply_type);
        }

        $jobs = $jobs->get();

        $data = [];
        for ($i = 0; $i < count($jobs); $i++) {
            $job = $jobs[$i];

            $author = User::find($job->user_id);
            $agency = $author->agency;

            $agency_name = $job?->agency_name ?? ($agency?->name ?? '');
            $agency_profile = $job?->agency_website ?? (in_array($author->role, ['agency']) ? sprintf("%s/agency/%s", env('FRONTEND_URL'), $agency?->slug) : '');

            $job_url = sprintf('%s/job/%s', env('FRONTEND_URL'), $job->slug);

            for ($j = 0; $j < count($job->applications); $j++) {
                $application = $job->applications[$j];

                $data[] = array(
                    'receiver' => $application->user->email,
                    'recipient_name' => $application->user->first_name,
                    'job_title' => $job->title,
                    'job_url' => $job_url,
                    'agency_name' => $agency_name,
                    'agency_profile' => $agency_profile,
                    'apply_type' => $job->apply_type,
                    'show_test_links' => 'yes'
                );
            }
        }

        for ($k = 0; $k < count($data); $k++) {
            $item = $data[$k];

            // SendEmailJob::dispatch([
            //     'receiver' => $item['receiver'],
            //     'data' => [
            //         'recipient' => $item['recipient_name'],
            //         'job_title' => $item['job_title'],
            //         'agency_name' => $item['agency_name'],
            //     ],
            // ], 'job_closed_email');
            return new JobClosed([
                'recipient_name' => $item['recipient_name'],
                'job_title' => $item['job_title'],
                'job_url' => $item['job_url'],
                'agency_name' => $item['agency_name'],
                'agency_profile' => $item['agency_profile'],
                'apply_type' => $item['apply_type'],
                'show_test_links' => $item['show_test_links'],
            ]);
        }

        // return view('pages.test_data.index', ['data' => $data]);
    }

    public function testNewApplication(Request $request)
    {
        $json_data = File::get(resource_path('ignore/new-application.json'));
        $data = json_decode($json_data, true);

        return new NewApplication($data['data']['data']);
        // return "<pre>" . print_r($data['data']['data'], true) . "</pre>";
    }

    public function testCreativeSearch(Request $request) //Agency with No package
    {
        $search = $request->search;

        $agency_user_id = $request->user_id;
        $agency_user_applicants = [];
        if (isset($agency_user_id)) {
            $agency_user_applicants = array_unique(Application::whereHas('job', function ($query) use ($agency_user_id) {
                $query->where('user_id', $agency_user_id);
            })->pluck('user_id')->toArray());
        }

        $exact_search_ids = $this->getSearch1CreativeIDs($search, 'exact-match');
        $contains_search_ids = $this->getSearch1CreativeIDs($search, 'contains');

        $combinedCreativeIds = array_merge($exact_search_ids, $contains_search_ids);
        $combinedCreativeIds = array_values(array_unique($combinedCreativeIds, SORT_NUMERIC));
        $rawOrder = 'FIELD(id, ' . implode(',', $combinedCreativeIds) . ')';

        $creatives = Creative::whereIn('id', $combinedCreativeIds)
            ->whereHas('user', function ($query) use ($agency_user_applicants) {
                $query->where('status', 1)
                    ->where(function ($q) use ($agency_user_applicants) {
                        $q->where('is_visible', 1)
                            ->orWhere(function ($q1) use ($agency_user_applicants) {
                                $q1->where('is_visible', 0)
                                    ->whereIn('user_id', $agency_user_applicants);
                            });
                    });
            })
            ->orderByRaw($rawOrder)
            ->orderByDesc('is_featured')
            ->orderBy('created_at')
            ->paginate($request->per_page ?? config('global.request.pagination_limit'))
            ->withQueryString();

        return new LoggedinCreativeCollection($creatives);
    }

    public function getSearch1CreativeIDs($search, $match_type = 'contains')
    {
        if (!isset($match_type) || strlen($match_type) == 0) {
            $match_type = 'contains';
        }

        $wildCardStart = '%';
        $wildCardEnd = '%';

        if ($match_type == 'starts-with') {
            $wildCardStart = '';
        } elseif ($match_type == 'exact-match') {
            $wildCardStart = '';
            $wildCardEnd = '';
        }


        $terms = explode(',', $search);

        // Search via First or Last Name
        $sql = 'SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = trim($terms[$i]);
            // Check if the term contains a space or underscore (full name or both names)
            if (strpos($term, ' ') !== false || strpos($term, '_') !== false) {
                $separator = strpos($term, ' ') !== false ? ' ' : '_';
                $names = explode($separator, $term);
                $firstName = trim($names[0]);
                $lastName = trim($names[1]);

                $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "CONCAT(ur.first_name, ' ', ur.last_name) LIKE '" . $wildCardStart . "$firstName% $lastName" . $wildCardEnd . "'" . "\n";
                $sql .= " OR CONCAT(ur.last_name, ' ', ur.first_name) LIKE '" . $wildCardStart . "$lastName% $firstName" . $wildCardEnd . "'" . "\n";

                // Additional check for reverse order
                $sql .= " OR CONCAT(ur.first_name, ' ', ur.last_name) LIKE '" . $wildCardStart . "$lastName% $firstName" . $wildCardEnd . "'" . "\n";
                $sql .= " OR CONCAT(ur.last_name, ' ', ur.first_name) LIKE '" . $wildCardStart . "$firstName% $lastName" . $wildCardEnd . "'" . "\n";
            } else {
                // Search by individual terms
                $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "ur.first_name LIKE '" . $wildCardStart . "$term" . $wildCardEnd . "'" . "\n";
                $sql .= " OR ur.last_name LIKE '" . $wildCardStart . "$term" . $wildCardEnd . "'" . "\n";
            }

            break; //Because we only allow single term search
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via City Name
        $sql .= 'SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.city_id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(lc.parent_id IS NOT NULL AND lc.name LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "')" . "\n";
            break; //Because we only allow single term search
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via State Name
        $sql .= 'SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.state_id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(lc.parent_id IS NULL AND lc.name LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "')" . "\n";
            break; //Because we only allow single term search
        }

        $res = DB::select($sql);
        $creativeIds = collect($res)
            ->pluck('id')
            ->toArray();
        return $creativeIds;
    }

    public function testSkipJobAlertsForRepostJobs(Request $request)
    {

        $applications = array();
        $notifications = array();

        $current_job = Job::where('id', $request?->job_id)->first();
        $apps = Application::where('job_id', $current_job?->id)->get()->pluck('user_id')->toArray();
        $notifs = Notification::where('type', 'job_alert')->where('body', json_encode(array('job_id' => $current_job?->id)))->get()->pluck('user_id')->toArray();
        $applications = array_merge($applications, $apps ? $apps : []);
        $notifications = array_merge($notifications, $notifs ? $notifs : []);

        $original_job = Job::where('id', $request?->repost_job_id)->first();

        while ($original_job?->id) {
            $apps = Application::where('job_id', $original_job?->id)->get()->pluck('user_id')->toArray();
            $notifs = Notification::where('type', 'job_alert')->where('body', json_encode(array('job_id' => $original_job?->id)))->get()->pluck('user_id')->toArray();
            $applications = array_merge($applications, $apps ? $apps : []);
            $notifications = array_merge($notifications, $notifs ? $notifs : []);
            $original_job = Job::where('id', $original_job?->repost_job_id)->first();
        }

        $users = array_values(array_unique(array_merge($applications, $notifications)));

        $category = Category::where('id', $current_job->category_id)->first();

        $group_categories = Category::where('group_name', $category->name)->get();

        if (count($group_categories) > 0) {
            for ($i = 0; $i < count($group_categories); $i++) {
                $categories[$i] = $group_categories[$i]->id;
            }
        } else {
            $categories[0] = $category->id;
        }
        $categorySubscribers = JobAlert::with('user')->whereNotIn('user_id', $users)->whereIn('category_id', $categories)->where('status', 1)->get();

        $uniqueCategorySubscribers = [];
        $uniqueUserIds = [];
        for ($i = 0; $i < count($categorySubscribers); $i++) {
            if (!in_array($categorySubscribers[$i]->user_id, $uniqueUserIds)) {
                $uniqueCategorySubscribers[] = $categorySubscribers[$i];
                $uniqueUserIds[] = $categorySubscribers[$i]->user_id;
            }
        }

        return $uniqueCategorySubscribers;
    }

    function validate_url($url)
    {
        $tries = "";
        $valid_url = false;
        try {
            $find = ['https://wwww.', 'https://', 'http://www.', 'http://', 'www.'];
            $replace = ['', '', '', '', ''];

            $trimmed_url = str_replace($find, $replace, $url);

            $formats = ["https://", "https://www.", "http://", "http://www."];

            foreach ($formats as $format) {
                $formatted_url = $format . $trimmed_url;
                try {
                    if (url_exists($formatted_url)) {
                        $valid_url = $formatted_url;
                        break;
                    } else {
                        throw new Exception($formatted_url);
                    }
                } catch (Exception $e) {
                    $tries .= "Failed for => " . $e->getMessage() . "<br>\n";
                }
            }
        } catch (Exception $ex) {
            $tries .= "Something else => " . $ex->getMessage() . "<br>\n";
        }

        return array(
            'status' => $valid_url != false ? "success" : "failure",
            'url' => $url,
            'valid_url' => $valid_url,
            'tries' => $tries,
        );
    }

    public function testDataUrl(Request $request)
    {
        return formate_url($request->url);
    }

    public function testCrop(Request $request)
    {
        $user_id = $request->user_id;
        $crop_x = $request->x;
        $crop_y = $request->y;
        $crop_width = $request->width;
        $crop_height = $request->height;

        if ($user_id) {
            $user = User::where('uuid', $user_id)->first();

            // $attachment = Attachment::where(['user_id' => $user->id, 'resource_type' => 'profile_picture'])->first();

            $profile_picture = getAttachmentBasePath() . $user->profile_picture->path;

            $info = pathinfo($profile_picture);
            // dd($info);

            $fname = $info['basename'];
            $thumbWidth = 150;
            $thumb_path = str_replace($info['filename'], $info['filename'] . "_thumb", $user->profile_picture->path);

            // dd($thumb_path);

            if (strtolower($info['extension']) == 'jpg') {

                // load image and get image size
                $img = \imagecreatefromjpeg("{$profile_picture}");

                $tmp_img = imagecrop($img, ['x' => $crop_x, 'y' => $crop_y, 'width' => $crop_width, 'height' => $crop_height]);

                $temp = tmpfile();
                // save thumbnail into a temp file
                imagejpeg($tmp_img, $temp);

                $filePath = Storage::disk('s3')->put($thumb_path, $temp);

                fclose($temp);

                $html = 'Crop Params:<br>';
                $html .= 'x:' . $crop_x . '<br>';
                $html .= 'y:' . $crop_y . '<br>';
                $html .= 'width:' . $crop_width . '<br>';
                $html .= 'height:' . $crop_height . '<br>';
                $html .= '<img src="' . getAttachmentBasePath() . $thumb_path . '" />';
                return $html;
            }
        }
        return "No-UUID";
    }

    private function get_location($user)
    {
        $address = $user->addresses ? collect($user->addresses)->firstWhere('label', 'personal') : null;

        if ($address) {
            return [
                'state_id' => $address->state ? $address->state->uuid : null,
                'state' => $address->state ? $address->state->name : null,
                'city_id' => $address->city ? $address->city->uuid : null,
                'city' => $address->city ? $address->city->name : null,
            ];
        } else {
            return [
                'state_id' => null,
                'state' => null,
                'city_id' => null,
                'city' => null,
            ];
        }
    }

    // public function testWelcome(Request $request)
    // {
    //     $creative = Creative::where('id', '=', $request->creative_id)->first();

    //     $user = $creative->user;
    //     $creative_category = isset($creative->category) ? $creative->category->name : null;
    //     $creative_location = $this->get_location($user);

    //     return '<div class="welcome-lounge">' .
    //         '  <img src="' . env('APP_URL') . '/assets/img/welcome-blank.jpeg" alt="Welcome Creative" />' .
    //         '  <img class="user_image" src="' . (isset($user->profile_picture) ? getAttachmentBasePath() . $user->profile_picture->path : asset('assets/img/placeholder.png')) . '" alt="Profile Image" />' .
    //         '  <div class="user_info">' .
    //         '    <div class="name">' . ($user->first_name . ' ' . $user->last_name) . '</div>' .
    //         ($creative_category != null ? ('    <div class="category">' . $creative_category . '</div>') : '') .
    //         ($creative_location['state'] || $creative_location['city'] ? ('    <div class="location">' . ($creative_location['state'] . (($creative_location['state'] && $creative_location['city']) ? ', ' : '') . $creative_location['city']) . '</div>') : '') .
    //         '  </div>' .
    //         '</div>';
    // }

    private function getWelcomePost($creative)
    {
        $user = $creative->user;
        $creative_category = isset($creative->category) ? $creative->category->name : null;
        $creative_location = $this->get_location($user);

        return '<a href="' . env('FRONTEND_URL') . '/creative/' . ($user?->creative?->slug ?? $user->username) . '">@' . $user->full_name . '</a><br />' .
            '<div class="welcome-lounge">' .
            '  <img src="' . env('APP_URL') . '/assets/img/welcome-blank.gif" alt="Welcome Creative" />' .
            '  <img class="user_image" src="' . env('APP_URL') . '/api/v1/get-user-preferred-picture/?slug=' . ($user?->creative?->slug ?? $user->username) . '" alt="Profile Image" />' .
            '  <div class="user_info">' .
            '    <div class="name">' . ($user->first_name . ' ' . $user->last_name) . '</div>' .
            ($creative_category != null ? ('    <div class="category">' . $creative_category . '</div>') : '') .
            ($creative_location['state'] || $creative_location['city'] ? ('    <div class="location">' . ($creative_location['state'] . (($creative_location['state'] && $creative_location['city']) ? ', ' : '') . $creative_location['city']) . '</div>') : '') .
            '  </div>' .
            '</div>';
    }

    public function sendLoungeMentionNotifications($post, $recipient_ids, $send_email = 'yes')
    {
        try {
            $author = $post->user;
            foreach ($recipient_ids as $recipient_id) {

                $receiver = User::where('uuid', $recipient_id)->first();

                $data = array();
                $data['uuid'] = Str::uuid();

                $data['user_id'] = $receiver->id;
                $data['type'] = 'lounge_mention';
                $data['message'] = $author->full_name . ' commented on you in a post';

                $data['body'] = array('post_id' => $post->id);

                $notification = Notification::create($data);

                $group = $post->group;

                $group_url = $group ? ($group->slug == 'feed' ? env('FRONTEND_URL') . '/community' : env('FRONTEND_URL') . '/groups/' . $group->uuid) : '';

                $data = [
                    'data' => [
                        'recipient' => $receiver->first_name,
                        'name' => $author->full_name,
                        'inviter' => $author->full_name,
                        'inviter_profile_url' => sprintf('%s/creative/%s', env('FRONTEND_URL'), ($author?->creative?->slug ?? $author->username)),
                        'profile_picture' => get_profile_picture($author),
                        'user' => $author,
                        'group_url' => $group_url,
                        'group' => $group->name,
                        'post_time' => \Carbon\Carbon::parse($post->created_at)->diffForHumans(),
                        'notification_uuid' => $notification->uuid,
                    ],
                    'receiver' => $receiver
                ];

                if ($send_email == 'yes') {
                    SendEmailJob::dispatch($data, 'user_mentioned_in_post');
                }
            }
        } catch (\Exception $e) {
            throw new ApiException($e, 'NS-01');
        }
    }

    public function testWelcome(Request $request)
    {
        // $creative_id = $request->has('creative_id') ? $request->creative_id : null;

        // if($creative_id) {
        //     $creative = Creative::where('id','=',$creative_id)->first();
        //     $post = Post::create( [
        //         'uuid' => Str::uuid(),
        //         'user_id' => 202, // admin/erika
        //         'group_id' => 4, // The Lounge Feed
        //         'content' => $this->getWelcomePost( $creative ),
        //         'status' => 1,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ] );

        //     if ( $post ) {
        //         $creative->is_welcomed = true;
        //         $creative->welcomed_at = now();
        //         $creative->save();

        //         $this->sendLoungeMentionNotifications( $post, [ $creative->user->uuid ], 'yes' );
        //     }
        // } 

        $date_threshold = now()->subDays(14);
        $queued_creatives = Creative::with('user')->where('created_at', '>', $date_threshold)->where('is_welcomed', '=', 0)->whereNotNull('welcome_queued_at')->get();

        $html = '';
        $html .= '<html><head><title>Welcome Queue List</title>';
        $html .= '<style>';
        $html .= 'table { width: 100%; } td { min-width: 100px; } th { text-align: left; } img { width: 100px; height: 100px; border-radius: 100%; object-fit: cover;';
        $html .= '</style><body>';
        $html .= '<h3>Welcome Queue List</h3>';
        $html .= '<table border="0" cellpadding="0" cellspacing="0">';
        $html .= '<tr>';
        $html .= '<th>SR</th>';
        $html .= '<th>User ID</th>';
        $html .= '<th>User Pic</th>';
        $html .= '<th>First Name</th>';
        $html .= '<th>Last Name</th>';
        $html .= '<th>Created At</th>';
        $html .= '<th>Featured At</th>';
        $html .= '<th>Welcome Queued At</th>';
        $html .= '<th>Action</th>';
        $html .= '</tr>';
        $qc_sr = 0;
        foreach ($queued_creatives as $qc) {
            $html .= '<tr>';
            $html .= '<td>' . ++$qc_sr . '</td>';
            $html .= '<td>' . $qc->user_id . '</td>';
            $html .= '<td><img src="' . get_user_picture_preferred($qc->user) . '" /></td>';
            $html .= '<td>' . $qc->user->first_name . '</td>';
            $html .= '<td>' . $qc->user->last_name . '</td>';
            $html .= '<td>' . $qc->created_at . '</td>';
            $html .= '<td>' . $qc->featured_at . '</td>';
            $html .= '<td>' . $qc->welcome_queued_at . '</td>';
            $html .= '<td><a href="javascript:void(0);" onClick="remove(\'' . $qc->uuid . '\')">Remove</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        $html .= '<form id="removefrom" method="POST" action="/test-welcome-remove" style="display: none;">
                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                    <input type="hidden" name="uuid" id="uuid" value="">
                  </form>
                  <script>
                    function remove(uuid) {
                        document.getElementById("uuid").value = uuid;
                        document.getElementById("removefrom").submit();
                    }
                  </script>';
        $html .= '</body></html>';

        return $html;

        $today_welcomed_at_creatives_count = Creative::where('is_welcomed', '=', 1)->whereDate('welcomed_at', '=', today()->toDateString())->count('welcomed_at');
        $previous_welcome_queued_at_creatives_count = Creative::where('is_welcomed', '=', 0)->whereNotNull('welcome_queued_at')->count('welcome_queued_at');
        $next_welcome_creative = null;

        if ($today_welcomed_at_creatives_count < 3) {
            $next_welcome_creative = Creative::where('is_welcomed', '=', 0)->whereNotNull('welcome_queued_at')->orderBy('welcome_queued_at')->first();
        }

        return array(
            'Today' => today()->toDateString(),
            'today_welcomed_at_creatives_count' => $today_welcomed_at_creatives_count,
            'previous_welcome_queued_at_creatives_count' => $previous_welcome_queued_at_creatives_count,
            'next_welcome_creative' => $next_welcome_creative?->id ?? "",
        );
    }

    public function testWelcomeRemove(Request $request)
    {
        Creative::where('uuid', $request->uuid)->update(['welcome_queued_at' => null]);
        return redirect()->back()->with('success', 'Removed from the welcome list!');
    }

    private function getCreativeProfileProgress($creative)
    {
        $progress = 0;
        $required_fields = 17;
        $completed_fields = 0;

        $completed_fields += strlen($creative?->user?->profile_picture?->path ?? '') > 0 ? 1 : 0;
        $completed_fields += strlen($creative?->user?->first_name ?? '') > 0 ? 1 : 0;
        $completed_fields += strlen($creative?->user?->last_name ?? '') > 0 ? 1 : 0;
        $completed_fields += strlen($creative?->user?->portfolio_website_link?->url ?? '') > 0 ? 1 : 0;
        $completed_fields += strlen($creative?->user?->creative_linkedin_link?->url ?? '') > 0 ? 1 : 0;
        $completed_fields += strlen($creative?->user?->email ?? '') > 0 ? 1 : 0;
        $completed_fields += strlen($creative?->user?->personal_phone?->phone_number ?? '') > 0 ? 1 : 0;
        $completed_fields += (strlen($creative?->title ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->category?->name ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->years_of_experience ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->industry_experience ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->media_experience ?? "") > 0) ? 1 : 0;

        $address = $creative?->user?->addresses ? collect($creative?->user->addresses)->firstWhere('label', 'personal') : null;

        if ($address) {
            $completed_fields += (strlen($address?->state?->name ?? "") > 0) ? 1 : 0;
            $completed_fields += (strlen($address?->city?->name ?? "") > 0) ? 1 : 0;
        }

        $completed_fields += (strlen($creative?->strengths ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->employment_type ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->about ?? "") > 0) ? 1 : 0;

        $progress = intval(100 * $completed_fields / $required_fields);

        return $progress;
    }

    private function getAgencyProfileProgress($agency): int
    {
        $progress = 0;
        $required_fields = 16;
        $completed_fields = 0;

        $completed_fields += strlen($agency?->user?->agency_logo?->path ?? '') > 0 ? 1 : 0;
        $completed_fields += (strlen($agency?->name ?? "") > 0) ? 1 : 0;
        $completed_fields += strlen($agency?->user?->agency_website_link?->url ?? '') > 0 ? 1 : 0;

        $address = $agency?->user?->addresses ? collect($agency?->user->addresses)->firstWhere('label', 'business') : null;
        if ($address) {
            $completed_fields += (strlen($address?->state?->name ?? "") > 0) ? 1 : 0;
            $completed_fields += (strlen($address?->city?->name ?? "") > 0) ? 1 : 0;
        }

        $completed_fields += strlen($agency?->user?->agency_linkedin_link?->url ?? '') > 0 ? 1 : 0;
        $completed_fields += strlen($agency?->user?->email ?? '') > 0 ? 1 : 0;
        $completed_fields += strlen($agency?->user?->first_name ?? '') > 0 ? 1 : 0;
        $completed_fields += strlen($agency?->user?->last_name ?? '') > 0 ? 1 : 0;
        $completed_fields += strlen($agency?->user?->business_phone?->phone_number ?? '') > 0 ? 1 : 0;
        $completed_fields += (strlen($agency?->about ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($agency?->industry_experience ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($agency?->media_experience ?? "") > 0) ? 1 : 0;
        $completed_fields += ($agency?->is_remote || $agency?->is_hybrid || $agency?->is_onsite) ? 1 : 0;
        $completed_fields += (strlen($agency?->size ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($agency?->slug ?? "") > 0) ? 1 : 0;

        $progress = intval(100 * $completed_fields / $required_fields);

        return $progress;
    }

    public function calculateProfileCompletionCreative(Request $request)
    {

        $date_before = today()->subDays(2);

        $users = User::where('role', '=', 4)
            ->whereNull('profile_completed_at')
            ->whereNull('profile_completion_reminded_at')
            ->where('status', 1)
            ->whereDate('created_at', '<=', $date_before)
            ->orderBy('created_at')
            ->get();

        $output = [];

        $output[] = "Total Active Creatives: " . count($users) . ", Registered On/Before: " . $date_before->toDateString();

        foreach ($users as $user) {

            $creative = $user->creative;

            $progress = $this->getCreativeProfileProgress($creative);
            $output[] = sprintf("Progress: %d%%", $progress) . ", Registered: " . $user?->created_at?->format(config('global.datetime_format')) . ", Full Name: " . $user?->full_name . ", Email: " . $user?->email;

            $user->profile_complete_progress = $progress;
            $user->profile_completed_at = $progress == 100 ? today() : null;
            $user->save();
        }

        return implode("\n<br />", $output);
    }

    public function calculateProfileCompletionAgency(Request $request)
    {
        $date_before = today()->subDays(2);

        $users = User::where('role', '=', 3)
            ->whereNull('profile_completed_at')
            ->whereNull('profile_completion_reminded_at')
            ->where('status', 1)
            ->whereDate('created_at', '<=', $date_before)
            ->orderBy('created_at')
            ->get();

        $output = [];

        $output[] = "Total Active Agencies: " . count($users) . ", Registered On/Before: " . $date_before->toDateString();

        foreach ($users as $user) {

            $agency = $user->agency;

            $progress = $this->getAgencyProfileProgress($agency);
            $output[] = sprintf("Progress: %d%%", $progress) . ", Registered: " . $user?->created_at?->format(config('global.datetime_format')) . ", Full Name: " . $user?->full_name . ", Company: " . $agency?->name . ", Email: " . $user?->email;

            $user->profile_complete_progress = $progress;
            $user->profile_completed_at = $progress == 100 ? today() : null;
            $user->save();
        }

        return implode("\n<br />", $output);
    }

    public function profileCompletionCreative(Request $request)
    {
        if ($request->has('user_id')) {
            $creative = Creative::whereHas('user', function ($q) use ($request) {
                $q->where('id', '=', $request->user_id);
            })->first();
        } else {
            $creative = Creative::whereHas('user', function ($q) {
                $q->whereNull('profile_completed_at')->orderBy('created_at');
            })->take(1)->first();
        }

        if (!$creative) {
            return response()->json([
                'message' => "Creative not found",
            ], 500);
        }

        $data = [
            'data' => [
                'first_name' => $creative?->user?->first_name ?? '',
                'category_name' => $creative?->category?->name ?? '',
            ],
            'receiver' => $creative?->user,
        ];
        if ($request?->has('email') && $request?->email == "yes") {
            SendEmailJob::dispatch($data, 'profile_completion_creative');
            $user = User::where('uuid', '=', $creative->user->uuid)->first();
            $user->profile_completion_reminded_at = today();
            $user->save();
        }

        return new ProfileCompletionCreativeReminder($data['data']);
    }

    public function profileCompletionAgency(Request $request)
    {
        if ($request->has('user_id')) {
            $agency = Agency::whereHas('user', function ($q) use ($request) {
                $q->where('id', '=', $request->user_id);
            })->first();
        } else {
            $agency = Agency::whereHas('user', function ($q) {
                $q->whereNull('profile_completed_at')->orderBy('created_at');
            })->take(1)->first();
        }

        if (!$agency) {
            return response()->json([
                'message' => "Agency not found",
            ], 500);
        }

        $data = [
            'data' => [
                'first_name' => $agency?->user?->first_name ?? '',
                'profile_url' => sprintf('%s/agency/%s', env('FRONTEND_URL'), $agency->slug),
            ],
            'receiver' => $agency?->user,
        ];
        if ($request?->has('email') && $request?->email == "yes") {
            SendEmailJob::dispatch($data, 'profile_completion_agency');
            $user = User::where('uuid', '=', $agency->user->uuid)->first();
            $user->profile_completion_reminded_at = today();
            $user->save();
        }

        return new ProfileCompletionAgencyReminder($data['data']);
    }

    public function dateTimeCheck(Request $request)
    {

        return now();

        $date = today();
        $targetDate = Carbon::parse("2024-10-23");

        if ($request->has('date') && strlen($request->date) > 0) {
            $date = Carbon::parse($request->date);
        }

        $messages = [];

        $messages[] = "Today: " . $date;

        // if ($date->dayOfWeek >= Carbon::MONDAY && $date->dayOfWeek <= Carbon::FRIDAY) {
        //     $messages[] = "The date is between Monday and Friday.";
        // } else {
        //     $messages[] = "The date is not between Monday and Friday.";
        // }

        if ($date->greaterThanOrEqualTo($targetDate)) {
            $messages[] = "The date is >= " . $targetDate->toDateString();
        } else {
            $messages[] = "The date is < " . $targetDate->toDateString();
        }

        $messages[] = "Difference in days to remind: ";

        $diff = 2;
        switch ($date->dayOfWeek) {
            case Carbon::MONDAY:
            case Carbon::TUESDAY:
                $diff = 4;
                break;
        }

        $new_date = $date->subDays($diff);
        $messages[] = "Remind: " . $new_date;

        return implode("\n<br />", $messages);
    }

    public function agenciesWithoutJobPosts(Request $request)
    {
        $date = today();
        $wait = 8; // business days
        $business_days = $wait + ($date->dayOfWeek <= Carbon::WEDNESDAY ? 4 : 2);
        $date_before = today()->subDays($business_days);

        if ($request->has('date')) {
            $date_before = Carbon::parse($request->date);
        }

        $agency_user_ids = Agency::whereHas('user', function ($q) use ($date_before) {
            $q->where('status', '=', 1)
                ->where('role', '=', 3)
                ->whereDate('created_at', '<=', $date_before);
        })->pluck('user_id')->toArray();

        $job_user_ids = Job::whereHas('user', function ($q) use ($date_before) {
            $q->where('status', '=', 1)
                ->where('role', '=', 3)
                ->whereDate('created_at', '<=', $date_before);
        })->pluck('user_id')->toArray();

        $agency_user_ids = array_values(array_unique($agency_user_ids));
        $job_user_ids = array_values(array_unique($job_user_ids));

        Agency::whereIn('user_id', $job_user_ids)->update(['is_job_posted' => 1]);

        $agency_users_without_job_posts = array_values(array_unique(array_diff($agency_user_ids, $job_user_ids)));

        $agencies_without_job_posts = User::whereHas('agency', function ($q) use ($agency_users_without_job_posts) {
            $q->whereIn('user_id', $agency_users_without_job_posts)
                ->where('is_job_posted', '=', 0)
                ->whereNull('job_posting_reminded_at');
        })->orderBy("created_at")
            ->get();

        $output = [];

        $output[] = "Today: " . $date->toDateString();
        $output[] = "Date Before: " . $date_before->toDateString();

        $output[] = "Total Agencies Not Posting Any Job: " . count($agencies_without_job_posts) . ", Registered On/Before: " . $date_before->toDateString();

        foreach ($agencies_without_job_posts as $user) {

            $agency = $user->agency;

            $output[] = "Registered: " . $user?->created_at?->format(config('global.datetime_format')) . ", Full Name: " . $user?->full_name . ", Company: " . $agency?->name . ", Email: " . $user?->email;
        }

        return implode("\n<br />", $output);
    }

    public function noJobPostedAgencyReminder(Request $request)
    {
        $date_before = today()->subDays(5);

        $agency_user_ids = Agency::whereHas('user', function ($q) use ($date_before) {
            $q->where('status', '=', 1)
                ->where('role', '=', 3)
                ->whereDate('created_at', '<=', $date_before);
        })->pluck('user_id')->toArray();

        $job_user_ids = Job::whereHas('user', function ($q) use ($date_before) {
            $q->where('status', '=', 1)
                ->where('role', '=', 3)
                ->whereDate('created_at', '<=', $date_before);
        })->pluck('user_id')->toArray();

        $agency_user_ids = array_values(array_unique($agency_user_ids));
        $job_user_ids = array_values(array_unique($job_user_ids));

        Agency::whereIn('user_id', $job_user_ids)->update(['is_job_posted' => 1]);

        $agency_users_without_job_posts = array_values(array_unique(array_diff($agency_user_ids, $job_user_ids)));

        $agencies_without_job_posts = User::whereHas('agency', function ($q) use ($agency_users_without_job_posts) {
            $q->whereIn('user_id', $agency_users_without_job_posts)
                ->where('is_job_posted', '=', 0)
                ->whereNull('job_posting_reminded_at');
        })->orderBy("created_at")
            ->take(1)
            ->get();

        $user = $agencies_without_job_posts[0];
        $agency = $user?->agency;

        if (!$agency) {
            return response()->json([
                'message' => "Agency not found",
            ], 500);
        }

        $data = [
            'data' => [
                'first_name' => $user?->first_name ? $user->first_name : $user->username,
                'profile_url' => sprintf('%s/agency/%s', env('FRONTEND_URL'), $agency->slug),
            ],
            'receiver' => $user,
        ];

        if ($request?->has('email') && $request?->email == "yes") {
            SendEmailJob::dispatch($data, 'no_job_posted_agency_reminder');
            $agency = Agency::where('uuid', '=', $agency->uuid)->first();
            $agency->job_posting_reminded_at = today();
            $agency->save();
        }

        return new NoJobPostedAgencyReminder($data['data']);
    }

    public function test_trending_posts(Request $request)
    {
        $cacheKey = 'trending_posts';
        // Attempt to retrieve the data from the cache
        $trendingPosts = QueryBuilder::for(Post::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
                AllowedFilter::scope('group_id'),
            ])
            ->whereHas('user')
            ->whereHas('group', function ($query) {
                $query->where('status', '=', 0);
            })
            ->whereBetween('created_at', [now()->subMonth(), now()])
            ->withCount('reactions')
            ->withCount('comments')
            ->orderBy('reactions_count', 'desc')
            ->orderBy('comments_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->with('comments')
            ->withCount('reactions')
            ->paginate($request->per_page ?? config('global.request.pagination_limit'))
            ->withQueryString();

        return $trendingPosts->all();

        $authenticatedUserId = auth()->id();

        $trendingPosts->getCollection()->transform(function ($post) use ($authenticatedUserId) {
            $post->user_has_liked = $post->likes->contains('user_id', $authenticatedUserId);

            return $post;
        });

        return new TrendingPostCollection($trendingPosts);
    }

    public function test_user_preferred_picture(Request $request)
    {
        $slug = $request->has('slug') ? $request->slug : '';
        $preferred_picture = asset('assets/img/placeholder.png');
        if (strlen($slug) > 0) {

            $user = User::where('username', 'LIKE', $slug)->first();

            if ($user) {
                $preferred_picture = get_user_picture_preferred($user);
            }
        }

        return response(file_get_contents($preferred_picture), 200)->header('Content-Type', 'image/jpeg');
    }

    public function test_welcome_picture(Request $request)
    {
        return "in-progress";
    }

    public function testRegenThumb(Request $request)
    {
        $user_id = $request->has('user_id') ? $request->user_id : null;
        $action = $request->has('action') ? $request->action : null;
        $html = '';

        if (!empty($user_id) && !empty($action)) {
            $user = User::where('id', '=', $user_id)->first();
            if ($action == "mark-ok") {
                $user->update(['regen_thumb' => 'marked-ok::' . now()]);
                return redirect()->to(url()->current());
            } else if ($action == "skip-for-now") {
                $user->update(['regen_thumb' => 'skipped::' . now()]);
                $user->refresh();
                return redirect()->to(url()->current());
            } else if ($action == "regenerate-thumbnail") {
                $profile_picture = get_profile_picture($user);
                if (!empty($profile_picture)) {
                    storeThumb($user, 'user_thumbnail');
                    $user->update(['regen_thumb' => 'regenerated::' . now()]);
                    $user->refresh();
                } else {
                    $html .= 'Profile Picture not available' . '<br />';
                }
            }
        } else {
            $user = User::whereNull('regen_thumb')->orderByDesc('id')->limit(1)->first();
        }

        $html .= '<html><body>';
        $html .= '<style>';
        $html .= 'body{font-size: 16px; line-height: 1.5em;}';
        $html .= 'button[type="submit"]{margin:10px 10px 10px 0px;padding:10px;}';
        $html .= '.thumbnails-container{display:flex;gap:10px; align-items:center;}';
        $html .= '.thumbnail{border-radius:100%;}';
        $html .= '</style>';

        if ($user) {

            $profile_picture = get_profile_picture($user);
            $profile_thumbnail = get_user_thumbnail($user);

            $html .= 'User ID: ' . $user->id . '<br />';
            $html .= 'First Name: ' . $user->first_name . '<br />';
            $html .= 'Last Name: ' . $user->last_name . '<br />';
            $html .= 'Account Created: ' . $user->created_at . '<br />';

            $html .= '<hr /><form method="get">';
            $html .= '<input type="hidden" name="user_id" value="' . $user->id . '" />';

            if (empty($action) && $action != "regenerate-thumbnail") {
                $html .= '<button type="submit" name="action" value="skip-for-now">Skip for now</button>';
            }

            if (!empty($profile_picture)) {
                $html .= '<button type="submit" name="action" value="regenerate-thumbnail">Regenerate Thumbnail</button>';
            }
            $html .= '<button type="submit" name="action" value="mark-ok">Mark OK</button>';

            $html .= '</form><hr />';

            $html .= 'Profile Thumbnail: ' . $profile_thumbnail . '<br />';
            if (!empty($profile_thumbnail)) {
                $html .= '<div class="thumbnails-container">';
                $html .= '<img class="thumbnail" width="150" height="150" src="' . $profile_thumbnail . '" /><br />';
                $html .= '<img class="thumbnail" width="100" height="100" src="' . $profile_thumbnail . '" /><br />';
                $html .= '<img class="thumbnail" width="80" height="80" src="' . $profile_thumbnail . '" /><br />';
                $html .= '<img class="thumbnail" width="50" height="50" src="' . $profile_thumbnail . '" /><br />';
                $html .= '</div>';
            }

            $html .= 'Profile Picture: ' . $profile_picture . '<br />';
            if (!empty($profile_picture)) {
                $html .= '<div class="thumbnails-container">';
                $html .= '<img class="thumbnail" width="150" height="150" src="' . $profile_picture . '" /><br />';
                $html .= '<img class="thumbnail" width="100" height="100" src="' . $profile_picture . '" /><br />';
                $html .= '<img class="thumbnail" width="80" height="80" src="' . $profile_picture . '" /><br />';
                $html .= '<img class="thumbnail" width="50" height="50" src="' . $profile_picture . '" /><br />';
                $html .= '</div>';
                $html .= '<br />';
                $html .= '<img src="' . $profile_picture . '" /><br />';
            }
        } else {
            $html .= '<h3>No more users...</h3>';
        }

        $html .= '';
        $html .= '</body></html>';

        return $html;
    }
    /**
     * Gets the mock data for a specific email type.
     *
     * @param string $emailType
     * @return array|null
     */
    private function getMockDataForEmail(string $emailType)
    {
        // Define a mock user to act as the recipient and sender
        $mockUser = (object) [
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test_user@example.com',
            'full_name' => 'John Doe',
            'role_name' => 'creative',
            'agency_name' => 'Mock Agency'
        ];

        // Define a mock agency user
        $mockAgency = (object) [
            'id' => 2,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'test_agency@example.com',
            'full_name' => 'Jane Smith',
            'role_name' => 'agency',
            'agency_name' => 'Mock Agency'
        ];

        // Map each email type to its specific data payload
        $dataPayloads = [
            'test-account-approved' => [
                'receiver' => $mockUser,
                'emailType' => 'account_approved',
                'data' => [
                    'user' => $mockUser
                ]
            ],
            'test-job-closed' => [
                'receiver' => $mockUser,
                'emailType' => 'job_closed_email',
                'data' => [
                    'job_title' => 'Creative Director Position',
                    'agency_name' => $mockAgency->agency_name
                ]
            ],
            'test-new-application' => [
                'receiver' => $mockAgency,
                'emailType' => 'new_candidate_application',
                'data' => [
                    'applicant' => $mockUser,
                    'job_title' => 'Graphic Designer Job'
                ]
            ],
            'profile-completion-creative' => [
                'receiver' => $mockUser,
                'emailType' => 'profile_completion_creative',
                'data' => [
                    'user' => $mockUser
                ]
            ],
            'profile-completion-agency' => [
                'receiver' => $mockAgency,
                'emailType' => 'profile_completion_agency',
                'data' => [
                    'user' => $mockAgency
                ]
            ],
            'no-job-posted-agency-reminder' => [
                'receiver' => $mockAgency,
                'emailType' => 'no_job_posted_agency_reminder',
                'data' => [
                    'user' => $mockAgency
                ]
            ],
            'test-skip-job-alerts-for-repost-jobs' => [
                'receiver' => $mockUser, // This will be reassigned by the job itself, but we provide a default
                'emailType' => 'new_job_added_admin',
                'data' => [
                    'job_title' => 'Test Job'
                ]
            ],
        ];

        return $dataPayloads[$emailType] ?? null;
    }

    /**
     * Dispatches a SendEmailJob for a specific email type with mock data.
     *
     * @param string $emailType
     * @return void
     */
    private function dispatchTestEmail(string $emailType)
    {
        $payload = $this->getMockDataForEmail($emailType);

        if ($payload) {
            dispatch(new SendEmailJob($payload, $payload['emailType']));
        }
    }

    /**
     * Dispatches all test email jobs.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function sendAllTestEmails(Request $request)
    {
        // 1. ADD ALL EIGHT TEST CASE KEYS HERE
        $testEmailTypes2 = [];
        $testEmailTypes = [
            'account_approved_agency',
            'new_user_registration_creative_role',
            'new_user_registration_agency_role',
            'account_approved',
            'account_denied',
            'group_invitation',
            'job_approved_alert_all_subscribers',
            'new_job_added_admin',
            'job_invitation',
            'custom_pkg_request_admin_alert',
            'hire-an-advisor-job-completed',
            'application_submitted',
            'new_candidate_application',
            'application_removed_by_agency',
            'agency_is_interested',
            'job_closed_email',
            'friendship_request_sent',
            'friendship_request_accepted',
            'unread_message',
            'contact_us_inquiry',
            'job_expiring_soon_admin',
            'job_expiring_soon_agency',
            'email_updated',
            'user_mentioned_in_post',
            'profile_completion_creative',
            'profile_completion_agency',
            'no_job_posted_agency_reminder',
            'error_notification',
        ];

        $view = $request->query('view');

        if ($view) {
            if (! in_array($view, $testEmailTypes)) {
                $html = "<h1><b>Error!</b> </h1>
                            <h3>Please select one of the email types to test</h3>
                            <ol style='margin:0;padding-left:20px;list-style-position:inside;line-height:1.2;'>";
                foreach ($testEmailTypes as $i => $type) {
                    $html .= "<li style='margin:2px 0;'>
                                    <a href='" . route('test-email-previews', ['view' => $type]) . "' style='text-decoration:none;color:#2c7be5;'>"
                        . e($type) .
                        "</a>
                                </li>";
                }

                $html .= "</ol>";

                return response($html);
            }
            $testEmailTypes2 = $testEmailTypes;
            $testEmailTypes = [$view];
        } else {
            $testEmailTypes2 = $testEmailTypes;
        }

        $agency_user_approved = (object)['id' => 1, 'first_name' => 'John', 'last_name' => 'Doe', 'email' => 'alihumdard125@gmail.com'];
        $creative_user_registered = (object)['id' => 123, 'FRONTEND_URL' => 'http://127.0.0.1:8000', 'uuid' => 'a1b2c3d4-e5f6-7890-g1h2-i3j4k5l6m7n8', 'first_name' => 'Creative John', 'username' => 'Creative John', 'email' => 'creative.john@example.com'];
        $agency_user_registered = (object)['id' => 456, 'uuid' => 'z9y8x7w6-v5u4-t3s2-r1q0-p9o8n7m6l5k4', 'username' => 'Agency Bob', 'email' => 'agency.bob@example.com'];
        $creative_user_approved = (object)['first_name' => 'Jane'];
        $group_invitation_data = ['receiver_name' => 'Sarah Connor', 'agency_name' => 'Cyberdyne Systems', 'job_title' => 'Lead Developer', 'job_url' => 'https://example.com/jobs/lead-developer', 'group' => 'Tech Innovators Group'];

        // Data for: 'job_approved_alert_all_subscribers'
        $job_alert_data = [
            'title' => 'Senior Art Director',
            'agency' => 'Innovate Agency',
            'agency_profile' => 'https://example.com/agency/innovate',
            'location' => 'New York, NY',
            'remote' => 'Hybrid',
            'url' => 'https://example.com/jobs/senior-art-director',
            'subscribers_count' => 2,
        ];
        $subscribers = [
            (object)['user' => (object)['first_name' => 'Subscriber One', 'email' => 'subscriber.one@example.com']],
            (object)['user' => (object)['first_name' => 'Subscriber Two', 'email' => 'subscriber.two@example.com']],
        ];
        $admin_user_for_alert = (object)['first_name' => 'Admin'];

        // Data for: 'new_job_added_admin'
        $new_job = (object)['title' => 'Graphic Designer', 'employment_type' => 'Full-time'];

        // 2. ADD THE PAYLOADS FOR ALL FIVE TEST CASES HERE
        $payloads = [];
        $payloads['account_approved_agency'] = [
            "user" => $agency_user_approved,
            'APP_NAME' => 'CreativeHub',
            'APP_URL' => 'https://johndoeportfolio.com',
            'FRONTEND_URL' => 'https://johndoeportfolio.com',
            'APPROVE_URL' => 'https://johndoeportfolio.com',
            'DENY_URL' => 'https://johndoeportfolio.com'

        ];

        $payloads['new_user_registration_creative_role'] = [
            'user' => $creative_user_registered,
            'url' => 'https://johndoeportfolio.com',
            'APP_NAME' => 'CreativeHub',
            'APP_URL' => 'https://johndoeportfolio.com',
            'FRONTEND_URL' => 'https://johndoeportfolio.com',
            'APPROVE_URL' => 'https://johndoeportfolio.com',
            'DENY_URL' => 'https://johndoeportfolio.com'
        ];

        $payloads['new_user_registration_agency_role'] = [
            'user' => $agency_user_registered,
            'url' => 'https://www.linkedin.com/company/example-agency',
            'APP_NAME' => 'CreativeHub',
            'APP_URL' => 'https://johndoeportfolio.com',
            'FRONTEND_URL' => 'https://johndoeportfolio.com',
            'APPROVE_URL' => 'https://johndoeportfolio.com',
            'DENY_URL' => 'https://johndoeportfolio.com'
        ];

        $payloads['account_approved'] = [
            'user' => $creative_user_approved
        ];
        $payloads['account_denied'] =
            [
                'user' => $creative_user_registered,
            ];
        $payloads['group_invitation'] = [
            'receiver_name' => 'Sarah Connor',
            'inviter' => 'Sarah Connor',
            'recipient' => 'Sarah Connor',
            'agency_name' => 'Cyberdyne Systems',
            'job_title' => 'Lead Developer',
            'action_url' => 'https://example.com/jobs/lead-developer',
            'group_url' => 'https://example.com/jobs/lead-developer',
            'job_url' => 'https://example.com/jobs/lead-developer',
            'inviter_profile_url' => 'https://example.com/jobs/lead-developer',
            'group' => 'Tech Innovators Group', // For the subject line
        ];

        $payloads['order_confirmation'] = [
            'username' => 'Sarah Connor',
            'order_no' => 'Sarah Connor',
            'total' => '9890',
            'created_at' => 'Sarah Connor',
            'plan_name' => 'Cyberdyne Systems',
            'email' => 'example.com',
            'image' => 'https://example.com/jobs/lead-developer',
            'group_url' => 'https://example.com/jobs/lead-developer',
            'job_url' => 'https://example.com/jobs/lead-developer',
            'inviter_profile_url' => 'https://example.com/jobs/lead-developer',
            'group' => 'Tech Innovators Group', // For the subject line
        ];
        $payloads['job_approved_alert_all_subscribers'] = ['email_data' => $job_alert_data, 'subscribers' => $subscribers];
        $payloads['new_job_added_admin'] = [
            'job' => $new_job,
            'agency' => 'Creative Solutions LLC',
            'author' => 'Bob Belcher',
            'agency_profile' => 'https://example.com/agency/creative-solutions',
            'url' => 'https://example.com/jobs/graphic-designer',
            'category' => 'Design',
            'created_at' => now()->subMinutes(5)->format('Y-m-d H:i:s'),
            'expired_at' => now()->addDays(30)->format('Y-m-d H:i:s'),
        ];

        $payloads['job_invitation'] = [
            'receiver_name' => 'Linda Hamilton',
            'agency_name' => 'Acme Corporation',
            'job_title' => 'Product Manager',
            'job_url' => 'https://example.com/jobs/product-manager',
        ];

        $payloads['custom_pkg_request_admin_alert'] = [
            'agency' => 'Stark Industries',
            'author' => 'Tony Stark',
            'agency_profile' => 'https://example.com/agency/stark-industries',
            'category' => 'Hire an Advisor',
            'state' => 'California',
            'city' => 'Los Angeles',
            'comment' => 'We need to find a top-tier marketing advisor for a new product launch. Urgently.',
        ];

        // --- Data for: 'hire-an-advisor-job-completed' ---
        $payloads['hire-an-advisor-job-completed'] = [
            'recipient' => 'Agency Admin',
            'agency_name' => 'Wayne Enterprises',
            'agency_profile' => 'https://example.com/agency/wayne-enterprises',
            'category' => 'Hire an Advisor',
            'state' => 'New Jersey',
            'city' => 'Gotham',
            'advisor' => 'Lucius Fox',
        ];
        // --- Data for: 'application_submitted' ---
        $payloads['application_submitted'] = [
            'recipient' => 'Peter Parker',
            'job_title' => 'Staff Photographer',
            'job_url' => 'https://example.com/jobs/staff-photographer',
        ];

        // First, create a dummy applicant object with an email property.
        $applicant_user = (object)[
            'email' => 'clark.kent@example.com',
        ];

        // Create the main payload array.
        $payloads['new_candidate_application'] = [
            'receiver_name' => 'Perry White',
            'job_title' => 'Daily Planet Reporter',
            'job_url' => 'https://example.com/jobs/reporter',
            'creative_name' => 'Clark Kent',
            'applicant' => $applicant_user,
            'resume_url' => 'https://example.com/resumes/clark-kent',
            'creative_profile' => 'https://example.com/creatives/clark-kent',
            'message' => 'I believe my skills in investigative journalism would be a great asset to your team. I am a fast writer and work well under pressure.',
            'apply_type' => 'Internal', // This can be 'Internal' or 'External'
        ];
        // --- Data for: 'application_removed_by_agency' ---
        $payloads['application_removed_by_agency'] = [
            'applicant' => 'Diana Prince',
            'agency_name' => 'Starr Warehousing Co.',
            'agency_profile' => 'https://example.com/agency/starr-warehousing',
            'job_title' => 'Antiquities Specialist',
            'job_url' => 'https://example.com/jobs/antiquities-specialist',
        ];
        // --- Data for: 'agency_is_interested' ---
        $payloads['agency_is_interested'] = [
            'applicant' => 'Bruce Wayne',
            'agency_name' => 'Kord Industries',
            'agency_profile' => 'https://example.com/agency/kord-industries',
            'job_title' => 'Lead Gadget Designer',
            'job_url' => 'https://example.com/jobs/lead-gadget-designer',
        ];

        // --- Data for: 'job_closed_email' ---
        $payloads['job_closed_email'] = [
            'recipient_name' => 'Barry Allen',
            'job_title' => 'Forensic Scientist',
            'job_url' => 'https://example.com/jobs/forensic-scientist',
            'agency_name' => 'S.T.A.R. Labs',
            'agency_profile' => 'https://example.com/agency/star-labs',
            'apply_type' => 'Internal', // Can be 'Internal' or 'External'
        ];
        // --- Data for: 'friendship_request_sent' ---
        $sender1 = (object)[
            'first_name' => 'Steve Rogers',
            'profile_picture' => 'https://example.com/path/to/captain_america_avatar.jpg',
            'creative' => (object)['slug' => 'steve-rogers']
        ];

        $sender2 = (object)[
            'first_name' => 'Bucky Barnes',
            'profile_picture' => 'https://example.com/path/to/winter_soldier_avatar.jpg',
            'creative' => (object)['slug' => 'bucky-barnes']
        ];

        // Create the main payload array.
        $payloads['friendship_request_sent'] = [
            'recipient' => 'Sam Wilson',
            'multiple' => 'yes', // Use 'yes' for multiple senders, 'no' for a single sender
            'senders' => [$sender1, $sender2],
        ];

        // --- Data for: 'friendship_request_accepted' ---
        $payloads['friendship_request_accepted'] = [
            'recipient' => 'Tony Stark',
            'member' => 'Peter Parker',
        ];

        // First, create an array of recent message senders.
        $recent_messages = [
            [
                'name' => 'Natasha Romanoff',
                'profile_picture' => 'https://example.com/path/to/black_widow_avatar.jpg',
                'related' => 'Re: Mission Report',
            ],
            [
                'name' => 'Clint Barton',
                'profile_picture' => '', // Example of a user without a profile picture
                'related' => 'Re: Project Follow-up',
            ],
        ];

        // Create the main payload array.
        $payloads['unread_message'] = [
            'recipient' => 'Nick Fury',
            'unread_message_count' => 2,
            'recent_messages' => $recent_messages,
        ];

        // --- Data for: 'contact_us_inquiry' ---
        $payloads['contact_us_inquiry'] = [
            'name' => 'Charles Xavier',
            'location' => 'Westchester, New York',
            'email' => 'professor.x@example.com',
            'phone' => '1-800-555-XMEN',
            'message' => 'I would like to inquire about group discounts for educational institutions. We have several gifted youngsters who could benefit from your platform.',
        ];

        // --- Data for: 'job_expiring_soon_admin' ---
        $payloads['job_expiring_soon_admin'] = [
            'job_title' => 'Senior Copywriter',
            'agency_name' => 'Sterling Cooper',
            'agency_profile' => 'https://example.com/agency/sterling-cooper',
            'url' => 'https://example.com/jobs/senior-copywriter',
            'created_at' => now()->subDays(29)->format('Y-m-d'),
        ];

        // --- Data for: 'job_expiring_soon_agency' ---
        $payloads['job_expiring_soon_agency'] = [
            'author' => 'Don Draper',
            'job_title' => 'Creative Director',
            'agency_name' => 'Sterling Cooper Draper Pryce',
            'agency_profile' => 'https://example.com/agency/scdp',
            'url' => 'https://example.com/jobs/creative-director',
            'created_at' => now()->subDays(27)->format('Y-m-d'),
            'expired_at' => now()->addDays(3)->format('Y-m-d'),
        ];

        // --- Data for: 'email_updated' ---
        $payloads['email_updated'] = [
            'recipient' => 'James Howlett',
            'new_email' => 'logan.wolverine@example.com',
        ];

        // --- Data for: 'user_mentioned_in_post' ---
        $payloads['user_mentioned_in_post'] = [
            'recipient' => 'Dr. Stephen Strange',
            'inviter' => 'Wong',
            'inviter_profile_url' => 'https://example.com/creatives/wong',
            'profile_picture' => 'https://example.com/path/to/wong_avatar.jpg',
            'name' => 'Wong',
            'post_time' => now()->subMinutes(15)->diffForHumans(),
            'group_url' => 'https://example.com/lounge/post/123',
            'notification_uuid' => 'k1l2m3n4-o5p6-q7r8-s9t0-u1v2w3x4y5z6',
        ];

        // --- Data for: 'profile_completion_creative' ---
        $payloads['profile_completion_creative'] = [
            'first_name' => 'Matt Murdock',
            'category_name' => 'Copywriter',
        ];

        // --- Data for: 'profile_completion_agency' ---
        $payloads['profile_completion_agency'] = [
            'first_name' => 'Amanda Waller',
            'profile_url' => 'https://example.com/agency/argus/profile',
        ];

        // --- Data for: 'no_job_posted_agency_reminder' ---
        $payloads['no_job_posted_agency_reminder'] = [
            'first_name' => 'Lex Luthor',
        ];

        // --- Data for: 'error_notification' ---
        $payloads['error_notification'] = [
            'url' => 'https://example.com/some/problematic/page',
            'error_message' => 'PDOException: SQLSTATE[42S02]: Base table or view not found: 1146 Table \'database.non_existent_table\' doesn\'t exist',
            'date_time' => now()->toDateTimeString(),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        ];


        foreach ($testEmailTypes as $emailType) {
            // Get the correct payload for the current email being tested.
            $payload = $payloads[$emailType];
            $payload['FRONTEND_URL'] = 'https://johndoeportfolio.com';
            $payload['APP_URL'] = 'https://johndoeportfolio.com';
            $payload['APP_NAME'] = 'CreativeHub';

            $dummyEmail = 'alihumdard125@gmail.com';
            $devEmails = 'developer@example.com'; // A different address for BCC
            $adminEmail = 'admin@example.com';

            switch ($emailType) {
                /**
                 * Account
                 */
                case 'new_user_registration_creative_role':
                    return view('emails.test_preview', ['view' => 'emails.account.new_user_registration_creative', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);
                    break;
                case 'new_user_registration_agency_role':
                    return view('emails.test_preview', ['view' => 'emails.account.new_user_registration_agency', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);
                    break;
                case 'account_approved_agency':
                    return view('emails.test_preview', ['view' => 'emails.account.approved-agency', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);
                    // $this->sendEmail($dummyEmail, $devEmails, new AccountApprovedAgency($payload));
                    break;
                case 'account_approved':
                    return view('emails.test_preview', ['view' => 'emails.account.approved', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);
                    // $this->sendEmail($dummyEmail, $devEmails, new AccountApproved($payload));
                    break;
                case 'account_denied':
                    return view('emails.test_preview', ['view' => 'emails.account.denied', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);
                    // $this->sendEmail($dummyEmail, $devEmails, new AccountDenied($payload));
                    break;

                /**
                     * Group
                     */
                case 'group_invitation':
                    return view('emails.test_preview', ['view' => 'emails.group.invitation', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);
                    // $this->sendEmail($dummyEmail, $devEmails, new Invitation($payload));
                    break;
                case 'order_confirmation':
                    return view('emails.test_preview', ['view' => 'emails.order.alert-admin', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);
                    // $this->sendEmail($dummyEmail, $devEmails, new ConfirmationAdmin($payload));
                    break;
                case 'job_approved_alert_all_subscribers':
                    $payload =  $job_alert_data;

                    return view('emails.test_preview', ['view' => 'emails.job.new-job-alert', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);
                    // $this->sendEmail($adminEmail, $devEmails, new \App\Mail\Job\JobPostedApprovedAlertAllSubscribers($email_data, $admin_user_for_alert));
                    break;


                /**
                     * Job
                     */
                case 'new_job_added_admin':
                    return view('emails.test_preview', ['view' => 'emails.job.new-job-posted-admin-alert', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);
                    // $this->sendEmail($dummyEmail, $devEmails, new NewJobPosted($payload));
                    break;
                case 'job_invitation':
                    return view('emails.test_preview', ['view' => 'emails.job.invitation', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);

                    // $this->sendEmail($dummyEmail, $devEmails, new JobInvitation($payload));
                    break;
                // case 'custom_job_request_rejected':
                //     $this->sendEmail($dummyEmail, $devEmails, new CustomJobRequestRejected($payload));
                //     break;
                case 'custom_pkg_request_admin_alert':
                    return view('emails.test_preview', ['view' => 'emails.custom-pkg.admin-alert', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);

                    // $this->sendEmail($dummyEmail, $devEmails, new RequestAdminAlert($payload));
                    break;
                case 'hire-an-advisor-job-completed':
                    return view('emails.test_preview', ['view' => 'emails.custom-pkg.hire-an-advisor-job-completed', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);

                    // $this->sendEmail($dummyEmail, $devEmails, new HireAnAdvisorJobCompleted($payload));
                    break;

                /**
                     * Application
                     */
                case 'application_submitted':
                    return view('emails.test_preview', ['view' => 'emails.application.submitted', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);

                    // $this->sendEmail($dummyEmail, $devEmails, new ApplicationSubmitted($payload)); 
                    break;
                case 'new_candidate_application':
                    return view('emails.test_preview', ['view' => 'emails.application.new_to_the_agency', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);

                    // $this->sendEmail($dummyEmail, $devEmails, new NewApplication($payload)); // To the Agency
                    break;
                case 'application_removed_by_agency':
                    return view('emails.test_preview', ['view' => 'emails.application.removed', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);

                    // $this->sendEmail($dummyEmail, $devEmails, new Removed($payload)); // To the applicant
                    break;
                case 'agency_is_interested':
                    return view('emails.test_preview', ['view' => 'emails.application.they-are-looking', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);

                    // $this->sendEmail($dummyEmail, $devEmails, new Interested($payload)); // To the applicant
                    break;
                case 'job_closed_email':
                    return view('emails.test_preview', ['view' => 'emails.application.job_closed', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);
                    // $this->sendEmail($dummyEmail, $devEmails, new JobClosed($payload)); // To the applicant
                    break;

                /**
                     * Friend
                     */
                case 'friendship_request_sent':
                    return view('emails.test_preview', ['view' => 'emails.friendship.request', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);

                    // $this->sendEmail($dummyEmail, $devEmails, new FriendshipRequest($payload));
                    break;
                case 'friendship_request_accepted':
                    return view('emails.test_preview', ['view' => 'emails.friendship.request_accepted', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);

                    // $this->sendEmail($dummyEmail, $devEmails, new FriendshipRequestAccepted($payload));
                    break;

                /**
                     * Message Count
                     */
                case 'unread_message':
                    return view('emails.test_preview', ['view' => 'emails.message.unread', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);

                    // $this->sendEmail($dummyEmail, $devEmails, new UnreadMessage($payload));
                    break;
                case 'contact_us_inquiry':
                    return view('emails.test_preview', ['view' => 'emails.contact-us.index', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);

                    // $this->sendEmail($dummyEmail, $devEmails, new ContactFormMail($payload));
                    break;

                /**
                     * Job Post Expiring Soon
                     */
                case 'job_expiring_soon_admin':
                    return view('emails.test_preview', ['view' => 'emails.job.job_post_expiring_soon_admin', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);

                    // $this->sendEmail($dummyEmail, $devEmails, new JobPostExpiringAdmin($payload));
                    break;
                case 'job_expiring_soon_agency':
                    return view('emails.test_preview', ['view' => 'emails.job.job_post_expiring_soon_agency', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);

                    // $this->sendEmail($dummyEmail, $devEmails, new JobPostExpiringAgency($payload));
                    break;
                case 'email_updated':
                    return view('emails.test_preview', ['view' => 'emails.content-updated.email', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);

                    // $this->sendEmail($dummyEmail, $devEmails, new EmailUpdated($payload));
                    break;

                /**
                     * Group Post
                     */
                case 'user_mentioned_in_post':
                    return view('emails.test_preview', ['view' => 'emails.post.mention', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);

                    // $this->sendEmail($dummyEmail, $devEmails, new LoungeMention($payload));
                    break;
                case 'profile_completion_creative':
                    return view('emails.test_preview', ['view' => 'emails.account.profile_completion_creative', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);

                    // $this->sendEmail($dummyEmail, $devEmails, new ProfileCompletionCreativeReminder($payload));
                    break;
                case 'profile_completion_agency':
                    return view('emails.test_preview', ['view' => 'emails.account.profile_completion_agency', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);

                    // $this->sendEmail($dummyEmail, $devEmails, new ProfileCompletionAgencyReminder($payload));
                    break;
                case 'no_job_posted_agency_reminder':
                    return view('emails.test_preview', ['view' => 'emails.job.no_job_posted', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);

                    // $this->sendEmail($dummyEmail, $devEmails, new NoJobPostedAgencyReminder($payload));
                    break;
                case 'error_notification':
                    return view('emails.test_preview', ['view' => 'emails.site_error.index', 'data' => $payload, 'testEmailTypes' => $testEmailTypes2 ?? $testEmailTypes]);

                    // $this->sendEmail($dummyEmail, $devEmails, new ErrorNotificationMail($payload));
                    break;
                default:
                    // Handle unknown email types or fallback logic
                    break;
            }
        }


        $html = "<h1><b>Success Sended</b> </h1>
        <h3>To View signle email template select that email type.</h3>
        <ol style='margin:0; padding-left:20px; list-style-position:inside; line-height:1.2;'>";
        foreach ($testEmailTypes2 ?? $testEmailTypes as $i => $type) {
            $html .= "<li style='margin:2px 0;'>
                 <a href='" . route('test-email-previews', ['view' => $type]) . "' style='text-decoration:none;color:#2c7be5;'>"
                . e($type) .
                "</a>
             </li>";
        }

        $html .= "</ol>";

        return response($html);
    }


    // public function sendAllTestEmailsold(Request $request)
    // {
    //     // 1. ADD ALL EIGHT TEST CASE KEYS HERE
    //     $testEmailTypes2 = [];
    //     $testEmailTypes = [
    //         'account_approved_agency',
    //         'new_user_registration_creative_role',
    //         'new_user_registration_agency_role',
    //         'account_approved',
    //         'account_denied',
    //         'group_invitation',
    //         'job_approved_alert_all_subscribers',
    //         'new_job_added_admin',
    //         'job_invitation',
    //         'custom_pkg_request_admin_alert',
    //         'hire-an-advisor-job-completed',
    //         'application_submitted',
    //         'new_candidate_application',
    //         'application_removed_by_agency',
    //         'agency_is_interested',
    //         'job_closed_email',
    //         'friendship_request_sent',
    //         'friendship_request_accepted',
    //         'unread_message',
    //         'contact_us_inquiry',
    //         'job_expiring_soon_admin',
    //         'job_expiring_soon_agency',
    //         'email_updated',
    //         'user_mentioned_in_post',
    //         'profile_completion_creative',
    //         'profile_completion_agency',
    //         'no_job_posted_agency_reminder',
    //         'error_notification',
    //     ];

    //     $view = $request->query('view');

    //     if ($view) {
    //         if (! in_array($view, $testEmailTypes)) {
    //             $html = "<h1><b>Error!</b> </h1>
    //                         <h3>Please select one of the email types to test</h3>
    //                         <ol style='margin:0;padding-left:20px;list-style-position:inside;line-height:1.2;'>";
    //             foreach ($testEmailTypes as $i => $type) {
    //                 $html .= "<li style='margin:2px 0;'>
    //                                 <a href='" . route('test-email-previews', ['view' => $type]) . "' style='text-decoration:none;color:#2c7be5;'>"
    //                     . e($type) .
    //                     "</a>
    //                             </li>";
    //             }

    //             $html .= "</ol>";

    //             return response($html);
    //         }
    //         $testEmailTypes2 = $testEmailTypes;
    //         $testEmailTypes = [$view];
    //     } else {
    //         $testEmailTypes2 = $testEmailTypes;
    //     }

    //     $agency_user_approved = (object)['id' => 1, 'first_name' => 'John', 'last_name' => 'Doe', 'email' => 'alihumdard125@gmail.com'];
    //     $creative_user_registered = (object)['id' => 123, 'uuid' => 'a1b2c3d4-e5f6-7890-g1h2-i3j4k5l6m7n8', 'first_name' => 'Creative John', 'username' => 'Creative John', 'email' => 'creative.john@example.com'];
    //     $agency_user_registered = (object)['id' => 456, 'uuid' => 'z9y8x7w6-v5u4-t3s2-r1q0-p9o8n7m6l5k4', 'username' => 'Agency Bob', 'email' => 'agency.bob@example.com'];
    //     $creative_user_approved = (object)['first_name' => 'Jane'];
    //     $group_invitation_data = ['receiver_name' => 'Sarah Connor', 'agency_name' => 'Cyberdyne Systems', 'job_title' => 'Lead Developer', 'job_url' => 'https://example.com/jobs/lead-developer', 'group' => 'Tech Innovators Group'];

    //     // Data for: 'job_approved_alert_all_subscribers'
    //     $job_alert_data = [
    //         'title' => 'Senior Art Director',
    //         'agency' => 'Innovate Agency',
    //         'agency_profile' => 'https://example.com/agency/innovate',
    //         'location' => 'New York, NY',
    //         'remote' => 'Hybrid',
    //         'url' => 'https://example.com/jobs/senior-art-director',
    //         'subscribers_count' => 2,
    //     ];
    //     $subscribers = [
    //         (object)['user' => (object)['first_name' => 'Subscriber One', 'email' => 'subscriber.one@example.com']],
    //         (object)['user' => (object)['first_name' => 'Subscriber Two', 'email' => 'subscriber.two@example.com']],
    //     ];
    //     $admin_user_for_alert = (object)['first_name' => 'Admin'];

    //     // Data for: 'new_job_added_admin'
    //     $new_job = (object)['title' => 'Graphic Designer', 'employment_type' => 'Full-time'];

    //     // 2. ADD THE PAYLOADS FOR ALL FIVE TEST CASES HERE
    //     $payloads = [];
    //     $payloads['account_approved_agency'] =  [
    //         'user' => $agency_user_approved
    //     ];
    //     $payloads['new_user_registration_creative_role'] = [
    //         'user' => $creative_user_registered,
    //         'url' => 'https://johndoeportfolio.com'
    //     ];
    //     $payloads['new_user_registration_agency_role'] = [
    //         'user' => $agency_user_registered,
    //         'url' => 'https://www.linkedin.com/company/example-agency'
    //     ];
    //     $payloads['account_approved'] = $creative_user_approved;
    //     $payloads['account_denied'] = $creative_user_registered; // Reusing this user object
    //     $payloads['group_invitation'] = [
    //         'receiver_name' => 'Sarah Connor',
    //         'inviter' => 'Sarah Connor',
    //         'recipient' => 'Sarah Connor',
    //         'agency_name' => 'Cyberdyne Systems',
    //         'job_title' => 'Lead Developer',
    //         'action_url' => 'https://example.com/jobs/lead-developer',
    //         'group_url' => 'https://example.com/jobs/lead-developer',
    //         'job_url' => 'https://example.com/jobs/lead-developer',
    //         'inviter_profile_url' => 'https://example.com/jobs/lead-developer',
    //         'group' => 'Tech Innovators Group', // For the subject line
    //     ];

    //     $payloads['order_confirmation'] = [
    //         'username' => 'Sarah Connor',
    //         'order_no' => 'Sarah Connor',
    //         'total' => '9890',
    //         'created_at' => 'Sarah Connor',
    //         'plan_name' => 'Cyberdyne Systems',
    //         'email' => 'example.com',
    //         'image' => 'https://example.com/jobs/lead-developer',
    //         'group_url' => 'https://example.com/jobs/lead-developer',
    //         'job_url' => 'https://example.com/jobs/lead-developer',
    //         'inviter_profile_url' => 'https://example.com/jobs/lead-developer',
    //         'group' => 'Tech Innovators Group', // For the subject line
    //     ];
    //     $payloads['job_approved_alert_all_subscribers'] = ['email_data' => $job_alert_data, 'subscribers' => $subscribers];
    //     $payloads['new_job_added_admin'] = [
    //         'job' => $new_job,
    //         'agency' => 'Creative Solutions LLC',
    //         'author' => 'Bob Belcher',
    //         'agency_profile' => 'https://example.com/agency/creative-solutions',
    //         'url' => 'https://example.com/jobs/graphic-designer',
    //         'category' => 'Design',
    //         'created_at' => now()->subMinutes(5)->format('Y-m-d H:i:s'),
    //         'expired_at' => now()->addDays(30)->format('Y-m-d H:i:s'),
    //     ];

    //     $payloads['job_invitation'] = [
    //         'receiver_name' => 'Linda Hamilton',
    //         'agency_name' => 'Acme Corporation',
    //         'job_title' => 'Product Manager',
    //         'job_url' => 'https://example.com/jobs/product-manager',
    //     ];

    //     $payloads['custom_pkg_request_admin_alert'] = [
    //         'agency' => 'Stark Industries',
    //         'author' => 'Tony Stark',
    //         'agency_profile' => 'https://example.com/agency/stark-industries',
    //         'category' => 'Hire an Advisor',
    //         'state' => 'California',
    //         'city' => 'Los Angeles',
    //         'comment' => 'We need to find a top-tier marketing advisor for a new product launch. Urgently.',
    //     ];

    //     // --- Data for: 'hire-an-advisor-job-completed' ---
    //     $payloads['hire-an-advisor-job-completed'] = [
    //         'recipient' => 'Agency Admin',
    //         'agency_name' => 'Wayne Enterprises',
    //         'agency_profile' => 'https://example.com/agency/wayne-enterprises',
    //         'category' => 'Hire an Advisor',
    //         'state' => 'New Jersey',
    //         'city' => 'Gotham',
    //         'advisor' => 'Lucius Fox',
    //     ];
    //     // --- Data for: 'application_submitted' ---
    //     $payloads['application_submitted'] = [
    //         'recipient' => 'Peter Parker',
    //         'job_title' => 'Staff Photographer',
    //         'job_url' => 'https://example.com/jobs/staff-photographer',
    //     ];

    //     // First, create a dummy applicant object with an email property.
    //     $applicant_user = (object)[
    //         'email' => 'clark.kent@example.com',
    //     ];

    //     // Create the main payload array.
    //     $payloads['new_candidate_application'] = [
    //         'receiver_name' => 'Perry White',
    //         'job_title' => 'Daily Planet Reporter',
    //         'job_url' => 'https://example.com/jobs/reporter',
    //         'creative_name' => 'Clark Kent',
    //         'applicant' => $applicant_user,
    //         'resume_url' => 'https://example.com/resumes/clark-kent',
    //         'creative_profile' => 'https://example.com/creatives/clark-kent',
    //         'message' => 'I believe my skills in investigative journalism would be a great asset to your team. I am a fast writer and work well under pressure.',
    //         'apply_type' => 'Internal', // This can be 'Internal' or 'External'
    //     ];
    //     // --- Data for: 'application_removed_by_agency' ---
    //     $payloads['application_removed_by_agency'] = [
    //         'applicant' => 'Diana Prince',
    //         'agency_name' => 'Starr Warehousing Co.',
    //         'agency_profile' => 'https://example.com/agency/starr-warehousing',
    //         'job_title' => 'Antiquities Specialist',
    //         'job_url' => 'https://example.com/jobs/antiquities-specialist',
    //     ];
    //     // --- Data for: 'agency_is_interested' ---
    //     $payloads['agency_is_interested'] = [
    //         'applicant' => 'Bruce Wayne',
    //         'agency_name' => 'Kord Industries',
    //         'agency_profile' => 'https://example.com/agency/kord-industries',
    //         'job_title' => 'Lead Gadget Designer',
    //         'job_url' => 'https://example.com/jobs/lead-gadget-designer',
    //     ];

    //     // --- Data for: 'job_closed_email' ---
    //     $payloads['job_closed_email'] = [
    //         'recipient_name' => 'Barry Allen',
    //         'job_title' => 'Forensic Scientist',
    //         'job_url' => 'https://example.com/jobs/forensic-scientist',
    //         'agency_name' => 'S.T.A.R. Labs',
    //         'agency_profile' => 'https://example.com/agency/star-labs',
    //         'apply_type' => 'Internal', // Can be 'Internal' or 'External'
    //     ];
    //     // --- Data for: 'friendship_request_sent' ---
    //     $sender1 = (object)[
    //         'first_name' => 'Steve Rogers',
    //         'profile_picture' => 'https://example.com/path/to/captain_america_avatar.jpg',
    //         'creative' => (object)['slug' => 'steve-rogers']
    //     ];

    //     $sender2 = (object)[
    //         'first_name' => 'Bucky Barnes',
    //         'profile_picture' => 'https://example.com/path/to/winter_soldier_avatar.jpg',
    //         'creative' => (object)['slug' => 'bucky-barnes']
    //     ];

    //     // Create the main payload array.
    //     $payloads['friendship_request_sent'] = [
    //         'recipient' => 'Sam Wilson',
    //         'multiple' => 'yes', // Use 'yes' for multiple senders, 'no' for a single sender
    //         'senders' => [$sender1, $sender2],
    //     ];

    //     // --- Data for: 'friendship_request_accepted' ---
    //     $payloads['friendship_request_accepted'] = [
    //         'recipient' => 'Tony Stark',
    //         'member' => 'Peter Parker',
    //     ];

    //     // First, create an array of recent message senders.
    //     $recent_messages = [
    //         [
    //             'name' => 'Natasha Romanoff',
    //             'profile_picture' => 'https://example.com/path/to/black_widow_avatar.jpg',
    //             'related' => 'Re: Mission Report',
    //         ],
    //         [
    //             'name' => 'Clint Barton',
    //             'profile_picture' => '', // Example of a user without a profile picture
    //             'related' => 'Re: Project Follow-up',
    //         ],
    //     ];

    //     // Create the main payload array.
    //     $payloads['unread_message'] = [
    //         'recipient' => 'Nick Fury',
    //         'unread_message_count' => 2,
    //         'recent_messages' => $recent_messages,
    //     ];

    //     // --- Data for: 'contact_us_inquiry' ---
    //     $payloads['contact_us_inquiry'] = [
    //         'name' => 'Charles Xavier',
    //         'location' => 'Westchester, New York',
    //         'email' => 'professor.x@example.com',
    //         'phone' => '1-800-555-XMEN',
    //         'message' => 'I would like to inquire about group discounts for educational institutions. We have several gifted youngsters who could benefit from your platform.',
    //     ];

    //     // --- Data for: 'job_expiring_soon_admin' ---
    //     $payloads['job_expiring_soon_admin'] = [
    //         'job_title' => 'Senior Copywriter',
    //         'agency_name' => 'Sterling Cooper',
    //         'agency_profile' => 'https://example.com/agency/sterling-cooper',
    //         'url' => 'https://example.com/jobs/senior-copywriter',
    //         'created_at' => now()->subDays(29)->format('Y-m-d'),
    //     ];

    //     // --- Data for: 'job_expiring_soon_agency' ---
    //     $payloads['job_expiring_soon_agency'] = [
    //         'author' => 'Don Draper',
    //         'job_title' => 'Creative Director',
    //         'agency_name' => 'Sterling Cooper Draper Pryce',
    //         'agency_profile' => 'https://example.com/agency/scdp',
    //         'url' => 'https://example.com/jobs/creative-director',
    //         'created_at' => now()->subDays(27)->format('Y-m-d'),
    //         'expired_at' => now()->addDays(3)->format('Y-m-d'),
    //     ];

    //     // --- Data for: 'email_updated' ---
    //     $payloads['email_updated'] = [
    //         'recipient' => 'James Howlett',
    //         'new_email' => 'logan.wolverine@example.com',
    //     ];

    //     // --- Data for: 'user_mentioned_in_post' ---
    //     $payloads['user_mentioned_in_post'] = [
    //         'recipient' => 'Dr. Stephen Strange',
    //         'inviter' => 'Wong',
    //         'inviter_profile_url' => 'https://example.com/creatives/wong',
    //         'profile_picture' => 'https://example.com/path/to/wong_avatar.jpg',
    //         'name' => 'Wong',
    //         'post_time' => now()->subMinutes(15)->diffForHumans(),
    //         'group_url' => 'https://example.com/lounge/post/123',
    //         'notification_uuid' => 'k1l2m3n4-o5p6-q7r8-s9t0-u1v2w3x4y5z6',
    //     ];

    //     // --- Data for: 'profile_completion_creative' ---
    //     $payloads['profile_completion_creative'] = [
    //         'first_name' => 'Matt Murdock',
    //         'category_name' => 'Copywriter',
    //     ];

    //     // --- Data for: 'profile_completion_agency' ---
    //     $payloads['profile_completion_agency'] = [
    //         'first_name' => 'Amanda Waller',
    //         'profile_url' => 'https://example.com/agency/argus/profile',
    //     ];

    //     // --- Data for: 'no_job_posted_agency_reminder' ---
    //     $payloads['no_job_posted_agency_reminder'] = [
    //         'first_name' => 'Lex Luthor',
    //     ];

    //     // --- Data for: 'error_notification' ---
    //     $payloads['error_notification'] = [
    //         'url' => 'https://example.com/some/problematic/page',
    //         'error_message' => 'PDOException: SQLSTATE[42S02]: Base table or view not found: 1146 Table \'database.non_existent_table\' doesn\'t exist',
    //         'date_time' => now()->toDateTimeString(),
    //         'ip_address' => '127.0.0.1',
    //         'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
    //     ];

    //     foreach ($testEmailTypes as $emailType) {
    //         // Get the correct payload for the current email being tested.
    //         $payload = $payloads[$emailType];

    //         $dummyEmail = 'alihumdard125@gmail.com';
    //         $devEmails = 'developer@example.com'; // A different address for BCC
    //         $adminEmail = 'admin@example.com';

    //         switch ($emailType) {
    //             /**
    //              * Account
    //              */
    //             case 'new_user_registration_creative_role':
    //                 $this->sendEmail($dummyEmail, $devEmails, new NewUserRegistrationCreative($payload));
    //                 break;
    //             case 'new_user_registration_agency_role':
    //                 $this->sendEmail($dummyEmail, $devEmails, new NewUserRegistrationAgency($payload));
    //                 break;
    //             case 'account_approved_agency':
    //                 $this->sendEmail($dummyEmail, $devEmails, new AccountApprovedAgency($payload));
    //                 break;
    //             case 'account_approved':
    //                 $this->sendEmail($dummyEmail, $devEmails, new AccountApproved($payload));
    //                 break;
    //             case 'account_denied':
    //                 $this->sendEmail($dummyEmail, $devEmails, new AccountDenied($payload));
    //                 break;

    //             /**
    //                  * Group
    //                  */
    //             case 'group_invitation':
    //                 $this->sendEmail($dummyEmail, $devEmails, new Invitation($payload));
    //                 break;
    //             case 'order_confirmation':
    //                 $this->sendEmail($dummyEmail, $devEmails, new ConfirmationAdmin($payload));
    //                 break;
    //             case 'job_approved_alert_all_subscribers':
    //                 $email_data = $payload['email_data'];
    //                 $this->sendEmail($adminEmail, $devEmails, new \App\Mail\Job\JobPostedApprovedAlertAllSubscribers($email_data, $admin_user_for_alert));
    //                 break;


    //             /**
    //                  * Job
    //                  */
    //             case 'new_job_added_admin': // To inform the admin that a new job has been added
    //                 $this->sendEmail($dummyEmail, $devEmails, new NewJobPosted($payload));
    //                 break;
    //             case 'job_invitation':
    //                 $this->sendEmail($dummyEmail, $devEmails, new JobInvitation($payload));
    //                 break;
    //             // case 'custom_job_request_rejected':
    //             //     $this->sendEmail($dummyEmail, $devEmails, new CustomJobRequestRejected($payload));
    //             //     break;
    //             case 'custom_pkg_request_admin_alert':
    //                 $this->sendEmail($dummyEmail, $devEmails, new RequestAdminAlert($payload));
    //                 break;
    //             case 'hire-an-advisor-job-completed':
    //                 $this->sendEmail($dummyEmail, $devEmails, new HireAnAdvisorJobCompleted($payload));
    //                 break;

    //             /**
    //                  * Application
    //                  */
    //             case 'application_submitted':
    //                 $this->sendEmail($dummyEmail, $devEmails, new ApplicationSubmitted($payload)); // To the applicant
    //                 break;
    //             case 'new_candidate_application':
    //                 $this->sendEmail($dummyEmail, $devEmails, new NewApplication($payload)); // To the Agency
    //                 break;
    //             case 'application_removed_by_agency':
    //                 $this->sendEmail($dummyEmail, $devEmails, new Removed($payload)); // To the applicant
    //                 break;
    //             case 'agency_is_interested':
    //                 $this->sendEmail($dummyEmail, $devEmails, new Interested($payload)); // To the applicant
    //                 break;
    //             case 'job_closed_email':
    //                 $this->sendEmail($dummyEmail, $devEmails, new JobClosed($payload)); // To the applicant
    //                 break;

    //             /**
    //                  * Friend
    //                  */
    //             case 'friendship_request_sent':
    //                 $this->sendEmail($dummyEmail, $devEmails, new FriendshipRequest($payload));
    //                 break;
    //             case 'friendship_request_accepted':
    //                 $this->sendEmail($dummyEmail, $devEmails, new FriendshipRequestAccepted($payload));
    //                 break;

    //             /**
    //                  * Message Count
    //                  */
    //             case 'unread_message':
    //                 $this->sendEmail($dummyEmail, $devEmails, new UnreadMessage($payload));
    //                 break;
    //             case 'contact_us_inquiry':
    //                 $this->sendEmail($dummyEmail, $devEmails, new ContactFormMail($payload));
    //                 break;

    //             /**
    //                  * Job Post Expiring Soon
    //                  */
    //             case 'job_expiring_soon_admin':
    //                 $this->sendEmail($dummyEmail, $devEmails, new JobPostExpiringAdmin($payload));
    //                 break;
    //             case 'job_expiring_soon_agency':
    //                 $this->sendEmail($dummyEmail, $devEmails, new JobPostExpiringAgency($payload));
    //                 break;
    //             case 'email_updated':
    //                 $this->sendEmail($dummyEmail, $devEmails, new EmailUpdated($payload));
    //                 break;

    //             /**
    //                  * Group Post
    //                  */
    //             case 'user_mentioned_in_post':
    //                 $this->sendEmail($dummyEmail, $devEmails, new LoungeMention($payload));
    //                 break;
    //             case 'profile_completion_creative':
    //                 $this->sendEmail($dummyEmail, $devEmails, new ProfileCompletionCreativeReminder($payload));
    //                 break;
    //             case 'profile_completion_agency':
    //                 $this->sendEmail($dummyEmail, $devEmails, new ProfileCompletionAgencyReminder($payload));
    //                 break;
    //             case 'no_job_posted_agency_reminder':
    //                 $this->sendEmail($dummyEmail, $devEmails, new NoJobPostedAgencyReminder($payload));
    //                 break;
    //             case 'error_notification':
    //                 $this->sendEmail($dummyEmail, $devEmails, new ErrorNotificationMail($payload));
    //                 break;
    //             default:
    //                 // Handle unknown email types or fallback logic
    //                 break;
    //         }
    //     }


    //     $html = "<h1><b>Success Sended</b> </h1>
    //     <h3>To View signle email template select that email type.</h3>
    //     <ol style='margin:0; padding-left:20px; list-style-position:inside; line-height:1.2;'>";
    //     foreach ($testEmailTypes2 ?? $testEmailTypes as $i => $type) {
    //         $html .= "<li style='margin:2px 0;'>
    //              <a href='" . route('test-email-previews', ['view' => $type]) . "' style='text-decoration:none;color:#2c7be5;'>"
    //             . e($type) .
    //             "</a>
    //          </li>";
    //     }

    //     $html .= "</ol>";

    //     return response($html);
    // }

    private function sendEmail($receiver, $bcc = [], $mailable)
    {
        // Added (array) casting to make sure the diff works even if a string is passed.
        $final_bcc = array_values(array_diff((array)$bcc, (array)$receiver));
        Mail::to($receiver)->bcc($final_bcc)->send($mailable);
    }



    public function testRegenerateThumbnails()
    {
        // Start of the cloned logic from the Artisan command
        $output = "<h1>Starting thumbnail regeneration process...</h1>";
        set_time_limit(600); // Extend max execution time to 10 minutes

        $users = User::with(['profile_picture', 'agency_logo'])->where(function ($query) {
            $query->whereHas('profile_picture')->orWhereHas('agency_logo');
        })->get();

        $output .= "<p>Found " . count($users) . " users to process.</p>";
        $processedCount = 0;
        $errorCount = 0;

        foreach ($users as $user) {
            try {
                $this->regenerateThumbnailForUser($user, 362);
                $processedCount++;
            } catch (\Exception $e) {
                Log::error("Failed to regenerate thumbnail for user: {$user->id}. Error: " . $e->getMessage());
                $output .= "<p style='color:red;'>Failed for user: {$user->id} - {$user->email}</p>";
                $errorCount++;
            }
        }

        // --- New Cache Clearing Section ---
        $output .= "<h2>Clearing Application Caches...</h2>";
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        $output .= "<p>Application caches have been cleared successfully.</p>";
        // ------------------------------------

        $output .= "<h2>Thumbnail regeneration process completed.</h2>";
        $output .= "<p>Successfully processed: {$processedCount}</p>";
        $output .= "<p>Failed: {$errorCount}</p>";

        return $output;
        // End of the cloned logic
        // End of the cloned logic
    }

    /**
     * Regenerate the thumbnail for a specific user with a circular radial mask.
     */
    private function regenerateThumbnailForUser(User $user, int $thumbWidth): ?Attachment
    {
        $resource_type = 'user_thumbnail';
        $original_attachment = $user->profile_picture ?: $user->agency_logo;

        if (!$original_attachment || !$original_attachment->path) {
            return null;
        }

        if (!Storage::disk('public')->exists($original_attachment->path)) {
            Log::error("Original file not found for user: {$user->id} at path: {$original_attachment->path}");
            return null;
        }

        $fileContents = Storage::disk('public')->get($original_attachment->path);
        $original_extension = strtolower($original_attachment->extension);

        // 1. Resize the base image
        $thumbnail = Image::make($fileContents)
            ->fit($thumbWidth, $thumbWidth, function ($constraint) {
                $constraint->upsize();
            });

        // 2. Create the circular radial gradient mask
        $mask = Image::canvas($thumbWidth, $thumbWidth);
        $center = $thumbWidth / 2;
        $maxDistance = sqrt(pow($center, 2) + pow($center, 2));

        for ($x = 0; $x < $thumbWidth; $x++) {
            for ($y = 0; $y < $thumbWidth; $y++) {
                $distance = sqrt(pow($x - $center, 2) + pow($y - $center, 2));
                $opacity = ($distance / $maxDistance) * 0.5;
                $mask->pixel('rgba(0, 0, 0, ' . $opacity . ')', $x, $y);
            }
        }

        // 3. Apply the mask to the thumbnail
        $thumbnail->insert($mask);

        // 4. Save the new thumbnail
        $fileName = uniqid() . '.' . $original_extension;
        $directory = 'attachments/' . $user->id;
        $thumbnail_path = sprintf('%s/thumbnails/%s', $directory, $fileName);
        Storage::disk('public')->put($thumbnail_path, (string) $thumbnail->encode($original_extension, 90));

        // 5. Update the database
        $existing_attachment = Attachment::where('user_id', $user->id)
            ->where('resource_type', $resource_type)
            ->first();

        if ($existing_attachment) {
            Storage::disk('public')->delete($existing_attachment->path);
            $existing_attachment->delete();
        }

        return Attachment::create([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'resource_type' => $resource_type,
            'path' => $thumbnail_path,
            'name' => $original_attachment->name,
            'extension' => $original_extension,
        ]);
    }


    public function testSingleImage(Request $request)
    {
        set_time_limit(300);

        $user = $this->findUserForTest($request->user_id);

        if (!$user) {
            return "<h1>User not found.</h1><p>Please provide a valid 'user_id' in the URL, like so: `/test-single-image?user_id=USER_UUID`</p>";
        }

        $original_attachment = $user->profile_picture ?: $user->agency_logo;
        if (!$original_attachment) {
            return "<h1>Error</h1><p>This user does not have a profile picture or agency logo to process.</p>";
        }

        try {
            if (!Storage::disk('public')->exists($original_attachment->path)) {
                return "<h1>An Error Occurred</h1><p>The source file does not exist in storage.</p>";
            }

            $fileContents = Storage::disk('public')->get($original_attachment->path);

            $logo = Image::make($fileContents);
            $logo->resize(400, 400);

            $maskPath = public_path('assets/img/radial-mask.png');
            if (!file_exists($maskPath)) {
                return "<h1>Error</h1><p>Mask file not found at: $maskPath</p>";
            }
            $mask = Image::make($maskPath)->resize(400, 400)->invert();

            $overlay = Image::canvas(400, 400, '#000000');
            $overlay->mask($mask);

            $logo->insert($overlay);

            $imageData = (string) $logo->encode('png');

            return Response::make($imageData)->header('Content-Type', 'image/png');
        } catch (\Exception $e) {
            return "<h1>An Error Occurred</h1><p>Details: " . $e->getMessage() . "</p>";
        }
    }


    /**
     * Helper to find a user for testing.
     */
    private function findUserForTest($userId = null)
    {
        if ($userId) {
            return User::where('uuid', $userId)->first();
        }

        return User::whereHas('profile_picture')->orWhereHas('agency_logo')->first();
    }
}
