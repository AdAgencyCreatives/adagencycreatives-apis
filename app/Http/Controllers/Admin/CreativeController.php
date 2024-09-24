<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Models\Address;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Creative;
use App\Models\Education;
use App\Models\Experience;
use App\Models\JobAlert;
use App\Models\Link;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Phone;
use App\Models\Post;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CreativeController extends Controller
 {
    public function __construct( protected UserService $userService )
 {
        $this->userService = $userService;
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

    private function getWelcomePost( $creative )
 {
        $user = $creative->user;
        $creative_category = isset( $creative->category ) ? $creative->category->name : null;
        $creative_location = $this->get_location( $user );

        return '<div class="welcome-lounge">' .
        '<a href="' . env( 'FRONTEND_URL' ) . '/creative/">' .  $user->slug. '</a><br />' .
        '  <img src="' . env( 'APP_URL' ) . '/assets/img/welcome-blank.gif" alt="Welcome Creative" />' .
        '  <img class="user_image" src="' . ( isset( $user->profile_picture ) ? getAttachmentBasePath() . $user->profile_picture->path : asset( 'assets/img/placeholder.png' ) ) . '" alt="Profile Image" />' .
        '  <div class="user_info">' .
        '    <div class="name">' . ( $user->first_name . ' ' . $user->last_name ) . '</div>' .
        ( $creative_category != null ? ( '    <div class="category">' . $creative_category . '</div>' ) : '' ) .
        ( $creative_location[ 'state' ] || $creative_location[ 'city' ] ? ( '    <div class="location">' . ( $creative_location[ 'state' ] . ( ( $creative_location[ 'state' ] && $creative_location[ 'city' ] ) ? ', ' : '' ) . $creative_location[ 'city' ] ) . '</div>' ) : '' ) .
        '  </div>' .
        '</div>';
    }

    public function sendLoungeMentionNotifications( $post, $recipient_ids, $send_email = 'yes' )
 {
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

    public function update( Request $request, $uuid )
 {
        $creative = Creative::where( 'uuid', $uuid )->first();

        $was_is_featured = $creative->is_featured;
        $was_is_welcomed = $creative->is_welcomed;

        $user = User::where( 'id', $creative->user_id )->first();
        $user->update( [
            'is_visible' => $request->is_visible,
        ] );
        //Present in users table

        $uuid = Str::uuid();
        $this->userService->appendWorkplacePreference( $request );

        $request->merge( [
            'employment_type' => implode( ',', $request->employment_type ?? [] ),
        ] );

        $data = $request->only( [
            'years_of_experience',
            'employment_type',
            'is_featured',
            'is_urgent',
            'is_remote',
            'is_hybrid',
            'is_onsite',
            'is_opentorelocation',
            'about',
        ] );

        if ( $request?->is_featured && !$creative?->is_featured ) {
            $creative->featured_at = now();
        }

        if ( $creative?->is_featured && !$request?->is_featured ) {
            $creative->featured_at = null;
        }

        $now_is_featured = $request?->is_featured;

        foreach ( $data as $key => $value ) {
            $creative->$key = $value;
        }
        $creative->save();

        if ( $request->input( 'phone' ) != null ) {
            $this->updatePhone( $user, $request->input( 'phone' ) );
        }

        if ( $request->has( 'linkedin' ) && $request->input( 'linkedin' ) != null ) {
            $this->updateLink( $user, 'linkedin', $request->input( 'linkedin' ) );
        }

        if ( $request->has( 'file' ) && is_object( $request->file ) ) {
            //Delete Previous picture
            if ( $user->attachments->where( 'resource_type', 'profile_picture' )->count() ) {
                Attachment::where( 'user_id', $user->id )->where( 'resource_type', 'profile_picture' )->delete();
            }

            $attachment = storeImage( $request, $user->id, 'profile_picture' );

            if ( isset( $attachment ) && is_object( $attachment ) ) {
                Attachment::whereId( $attachment->id )->update( [
                    'resource_id' => $creative->id,
                ] );
            }
        }

        $this->updateLocation( $request, $user );

        if ( !$was_is_welcomed && !$was_is_featured && $now_is_featured ) {

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
                $creative->save();

                $this->sendLoungeMentionNotifications( $post, [ $creative->user->uuid ], 'yes' );
            }
        }

        // dd( $request->all() );
        Session::flash( 'success', 'Creative updated successfully' );

        return redirect()->back();
    }

    public function update_qualification( Request $request, $uuid )
 {
        $creative = Creative::where( 'uuid', $uuid )->first();
        $user = User::where( 'id', $creative->user_id )->first();
        $uuid = Str::uuid();

        if ( $request->input( 'portfolio' ) != null ) {
            $this->updateLink( $user, 'portfolio', $request->input( 'portfolio' ) );
        }

        if ( $request->input( 'linkedin' ) != null ) {
            $this->updateLink( $user, 'linkedin', $request->input( 'linkedin' ) );
        }

        $category = Category::where( 'uuid', $request->category )->first();
        $creative->update( [
            'category_id' => $category->id ?? null,
            'title' => $request->title ?? '',
            'industry_experience' => '' . implode( ',', array_slice( $request->industry_experience ?? [], 0, 10 ) ) . '',
            'media_experience' => '' . implode( ',', array_slice( $request->media_experience ?? [], 0, 10 ) ) . '',
            'strengths' => '' . implode( ',', array_slice( $request->strengths ?? [], 0, 5 ) ) . '',
        ] );

        if ( $category?->id ) {
            $cat_ids = array( $category->id );
            $group_cat_ids = Category::where( 'group_name', '=', $category->name )->get()->pluck( 'id' )->toArray();
            $cat_ids = array_values( array_merge( $cat_ids, $group_cat_ids ) );
            foreach ( $cat_ids as $cat_id ) {
                $alert = JobAlert::where( 'user_id', $user->id )->where( 'category_id', $cat_id )->first();
                if ( !$alert ) {
                    JobAlert::create( [
                        'uuid' => Str::uuid(),
                        'user_id' => $user->id,
                        'category_id' => $cat_id,
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ] );
                }
            }
        }

        Session::flash( 'success', 'Creative updated successfully' );

        return redirect()->back();
    }

    public function update_experience( Request $request, $uuid )
 {
        $experience_ids = $request->input( 'experience_id' );
        $titles = $request->input( 'title' );
        $companies = $request->input( 'company' );
        $descriptions = $request->input( 'description' );

        foreach ( $experience_ids as $key => $experience_id ) {
            $experience = Experience::find( $experience_id );
            $experience->title = $titles[ $key ];
            $experience->company = $companies[ $key ];
            $experience->description = $descriptions[ $key ];
            $experience->save();
        }

        Session::flash( 'success', 'Creative updated successfully' );

        return redirect()->back();
    }

    public function update_education( Request $request, $uuid )
 {
        $education_ids = $request->input( 'education_id' );
        $degree = $request->input( 'degree' );
        $college = $request->input( 'college' );

        foreach ( $education_ids as $key => $education_id ) {
            $education = Education::find( $education_id );
            $education->degree = $degree[ $key ];
            $education->college = $college[ $key ];
            $education->save();
        }

        Session::flash( 'success', 'Creative updated successfully' );

        return redirect()->back();
    }

    private function updatePhone( $user, $phone_number )
 {
        $country_code = '+1';

        if ( strpos( $phone_number, $country_code ) === 0 ) {
            $phone_number = substr( $phone_number, strlen( $country_code ) );
            $phone_number = trim( $phone_number );
        }

        $phone = Phone::where( 'user_id', $user->id )->where( 'label', 'personal' )->first();
        if ( $phone ) {
            $phone->update( [ 'country_code' => $country_code, 'phone_number' => $phone_number ] );
        } else {

            Phone::create( [
                'uuid' => Str::uuid(),
                'user_id' => $user->id,
                'label' => 'personal',
                'country_code' => $country_code,
                'phone_number' => $phone_number,
            ] );
        }
    }

    private function updateLink( $user, $label, $url )
 {
        $link = Link::where( 'user_id', $user->id )->where( 'label', $label )->first();
        if ( $link ) {
            $link->update( [ 'url' => $url ] );
        } else {

            Link::create( [
                'uuid' => Str::uuid(),
                'user_id' => $user->id,
                'label' => $label,
                'url' => $url,
            ] );
        }
    }

    private function updateLocation( $request, $user )
 {
        $state = Location::where( 'uuid', $request->state )->first();
        $city = Location::where( 'uuid', $request->city )->first();
        $address = $user->addresses->first();

        if ( !$address ) {
            $address = new Address();
            $address->uuid = Str::uuid();
            $address->user_id = $user->id;
            $address->label = 'personal';
            $address->country_id = 1;
        }

        if ( !$state ) {
            return;
        }

        $address->state_id = $state->id;

        if ( $city ) {
            $address->city_id = $city->id;
        } else {
            // If only the state is available, set the city to 0
            $address->city_id = 0;
        }

        $address->save();
    }

    public function update_seo( Request $request, $uuid )
 {
        $creative = Creative::where( 'uuid', $uuid )->first();
        $creative->update( [
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'seo_keywords' => implode( ',', $request->seo_keywords ? $request->seo_keywords : [] ),
        ] );
        Session::flash( 'success', 'Creative updated successfully' );

        return redirect()->back();
    }

    public function update_website_preview( Request $request, $uuid )
 {
        $creative = Creative::where( 'uuid', $uuid )->first();

        if ( $request->has( 'file' ) && is_object( $request->file ) ) {
            //Delete Previous picture
            Attachment::where( 'user_id', $creative->user_id )->where( 'resource_type', 'website_preview' )->delete();
            $attachment = storeImage( $request, $creative->user_id, 'website_preview' );

            if ( isset( $attachment ) && is_object( $attachment ) ) {
                Attachment::whereId( $attachment->id )->update( [
                    'resource_id' => $creative->id,
                ] );
            }
        }
        Session::flash( 'success', 'Updated successfully' );
        return redirect()->back();
    }
}