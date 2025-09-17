@include('emails.includes.lounge_header')
<tr>
    <td style="padding: 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 1.5;  font-size: 14px; position: relative;"
        class="body_text_color body_text_size">
        <div style=" border-radius: 5px; max-width: 900px; margin: 0 auto; " class="content">
            <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi {{ $data['recipient'] }},</span>

            <p>
                You have
                @if ($data['multiple'] == 'yes')
                pending friend requests
                @else
                a pending friend request
                @endif
                in The Lounge on
                <a href="{{ $data['FRONTEND_URL'] }}" target="_blank">{{ $data['APP_NAME'] }}</a>.
            </p>

            <p>Click <a href="{{ $data['FRONTEND_URL'] }}/friends?friendships=requests" style="color: #3c5cc4;"
                    target="_blank">my
                    requests</a> to
                accept

                @if ($data['multiple'] == 'yes')
                invites
                @else
                invite
                @endif

                and start a conversation, or click

                @if ($data['multiple'] == 'yes')
                profiles
                @else
                profile
                @endif

                below to view
                inviter’s profile.
            </p>
            <div style="margin: 15px 0;">

                @foreach ($data['senders'] as $user)
                <a href="{{ $data['FRONTEND_URL'] }}/creative/{{ $user->creative->slug }}" style="color: #3c5cc4;"
                    target="_blank">

                    <div class="candidate-top-wrapper flex-middle-sm"
                        style="display: flex; gap: 15px; margin-bottom: 5px;">

                        <div class="candidate-thumbnail">
                            <div class="candidate-logo">
                                <img width="50" height="50"
                                    style="border-radius: 100% !important; height: 50px !important; width: 50px !important; margin-right: 10px; object-fit:cover !important"
                                    src="{{ $user?->profile_picture }}" />
                            </div>
                        </div>

                        <div class="candidate-information">
                            <div class="title-wrapper">
                                <h1 class="candidate-title"
                                    style="font-size: 16px; font-weight: normal; margin-bottom: 5px; margin-top: 10px;">
                                    {{ $user?->first_name }}
                                </h1>
                                {{-- <span style="color: #ccc;">{{ $user['message_time'] }}</span> --}}
                            </div>
                            <div class="candidate-metas">

                            </div>
                        </div>

                    </div>
                </a>
                @endforeach
            </div>
            <p>Explore more jobs and update your preferences anytime by visiting your dashboard.</p>
            <p>Cheers,<br>
                The Ad Agency Creatives Team.</p>
            @include('emails.includes.lounge_footer')