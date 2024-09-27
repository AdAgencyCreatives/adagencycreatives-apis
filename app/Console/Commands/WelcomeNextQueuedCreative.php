<?php

namespace App\Console\Commands;

use App\Exceptions\ApiException;
use App\Jobs\SendEmailJob;
use App\Models\Creative;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class WelcomeNextQueuedCreative extends Command {
    protected $signature = 'welcome-next-queued-creative';

    protected $description = 'Generates Welcome Post in Lounge';

    public function handle() {

        try {
            $this->info( "Today's: " . today()->toDateString() );
            $today_welcomed_at_creatives_count = Creative::where( 'is_welcomed', '=', 1 )->whereDate( 'welcomed_at', '=', today()->toDateString() )->count( 'welcomed_at' );
            $previous_welcome_queued_at_creatives_count = Creative::where( 'is_welcomed', '=', 0 )->whereNotNull( 'welcome_queued_at' )->count( 'welcome_queued_at' );
            $next_welcome_creative = Creative::where( 'is_welcomed', '=', 0 )->whereNotNull( 'welcome_queued_at' )->orderBy( 'welcome_queued_at' )->first();

            $this->info( implode( [
                'Before Processing Stats => ',
                'Today Welcomed: ',
                '' . $today_welcomed_at_creatives_count,
                ', Remaining in Queue: ',
                '' . $previous_welcome_queued_at_creatives_count,
                ', Next Creative in Queue: ',
                '' . $next_welcome_creative?->id ?? '',
            ] ) );

            if ( $today_welcomed_at_creatives_count < 3 ) {

                if ( $next_welcome_creative ) {
                    $creative = $next_welcome_creative;
                    $post = Post::create( [
                        'uuid' => Str::uuid(),
                        'user_id' => 202, // admin/erika
                        'group_id' => 4, // The Lounge Feed
                        'content' => $this->getWelcomePost( $creative ),
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ] );

                    if ( $post ) {
                        $creative->is_welcomed = true;
                        $creative->welcomed_at = now();
                        $creative->save();

                        $this->sendLoungeMentionNotifications( $post, [ $creative->user->uuid ], 'yes' );
                    }
                } else {
                    $this->info( 'No more next queued creative found.' );
                }
            } else {
                $this->info( "Today's Welcome Quota is finished." );
            }

            $today_welcomed_at_creatives_count = Creative::where( 'is_welcomed', '=', 1 )->whereDate( 'welcomed_at', '=', today()->toDateString() )->count( 'welcomed_at' );
            $previous_welcome_queued_at_creatives_count = Creative::where( 'is_welcomed', '=', 0 )->whereNotNull( 'welcome_queued_at' )->count( 'welcome_queued_at' );
            $next_welcome_creative = Creative::where( 'is_welcomed', '=', 0 )->whereNotNull( 'welcome_queued_at' )->orderBy( 'welcome_queued_at' )->first();

            $this->info( implode( [
                'After Processing Stats => ',
                'Today Welcomed: ',
                '' . $today_welcomed_at_creatives_count,
                ', Remaining in Queue: ',
                '' . $previous_welcome_queued_at_creatives_count,
                ', Next Creative in Queue: ',
                '' . $next_welcome_creative?->id ?? '',
            ] ) );
        } catch( \Exception $e ) {
            $this->info( $e->getMessage() );
        }
    }

    private function get_location( $user )
 {
        $address = $user->addresses ? collect( $user->addresses )->firstWhere( 'label', 'personal' ) : null;

        if ( $address ) {
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

    private function getWelcomePost( $creative ) {
        $user = $creative->user;
        $creative_category = isset( $creative->category ) ? $creative->category->name : null;
        $creative_location = $this->get_location( $user );

        return '<a href="' . env( 'FRONTEND_URL' ) . '/creative/' . $user->username . '">@' . $user->full_name . '</a><br />' .
        '<div class="welcome-lounge">' .
        '  <img src="' . env( 'APP_URL' ) . '/assets/img/welcome-blank.gif" alt="Welcome Creative" />' .
        '  <img class="user_image" src="' . ( isset( $user->profile_picture ) ? getAttachmentBasePath() . $user->profile_picture->path : asset( 'assets/img/placeholder.png' ) ) . '" alt="Profile Image" />' .
        '  <div class="user_info">' .
        '    <div class="name">' . ( $user->first_name . ' ' . $user->last_name ) . '</div>' .
        ( $creative_category != null ? ( '    <div class="category">' . $creative_category . '</div>' ) : '' ) .
        ( $creative_location[ 'state' ] || $creative_location[ 'city' ] ? ( '    <div class="location">' . ( $creative_location[ 'state' ] . ( ( $creative_location[ 'state' ] && $creative_location[ 'city' ] ) ? ', ' : '' ) . $creative_location[ 'city' ] ) . '</div>' ) : '' ) .
        '  </div>' .
        '</div>';
    }

    private function sendLoungeMentionNotifications( $post, $recipient_ids, $send_email = 'yes' ) {
        try {
            $author = $post->user;
            foreach ( $recipient_ids as $recipient_id ) {

                $receiver = User::where( 'uuid', $recipient_id )->first();

                $data = array();
                $data[ 'uuid' ] = Str::uuid();

                $data[ 'user_id' ] = $receiver->id;
                $data[ 'type' ] = 'lounge_mention';
                $data[ 'message' ] = $author->full_name . ' commented on you in his post';

                $data[ 'body' ] = array( 'post_id' => $post->id );

                $notification = Notification::create( $data );

                $group = $post->group;

                $group_url = $group ? ( $group->slug == 'feed' ? env( 'FRONTEND_URL' ) . '/community' : env( 'FRONTEND_URL' ) . '/groups/' . $group->uuid ) : '';

                $data = [
                    'data' => [
                        'recipient' => $receiver->first_name,
                        'name' => $author->full_name,
                        'inviter' => $author->full_name,
                        'inviter_profile_url' => sprintf( '%s/creative/%s', env( 'FRONTEND_URL' ), $author?->username ),
                        'profile_picture' => get_profile_picture( $author ),
                        'user' => $author,
                        'group_url' => $group_url,
                        'group' => $group->name,
                        'post_time' => \Carbon\Carbon::parse( $post->created_at )->diffForHumans(),
                        'notification_uuid' => $notification->uuid,
                    ],
                    'receiver' => $receiver
                ];

                if ( $send_email == 'yes' ) {
                    SendEmailJob::dispatch( $data, 'user_mentioned_in_post' );
                }
            }
        } catch ( \Exception $e ) {
            throw new ApiException( $e, 'NS-01' );
        }
    }
}