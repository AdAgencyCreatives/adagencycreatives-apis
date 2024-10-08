@include('emails.includes.lounge_header')

<tr>
    <td style="padding: 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 14px; color: #000000; font-size: 14px; position: relative;"
        class="body_text_color body_text_size">
        <div style="background:#fff; border-radius: 5px; max-width: 450px; margin: 0 auto; color:#000000" class="content">
            <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi {{ $data['recipient'] }},</span>

            <p>We wanted you to know that you have
                @if ($data['unread_message_count'] > 1)
                    new messages
                @else
                    a new message
                @endif
                on
                <a href="{{ $data['FRONTEND_URL'] }}" target="_blank">{{ $data['APP_NAME'] }}</a>.
            </p>
            <div style="margin: 15px 0;">

                @foreach ($data['recent_messages'] as $user)
                    <div class="candidate-top-wrapper flex-middle-sm"
                        style="display: flex; gap: 15px; margin-bottom: 5px;">
                        <div class="candidate-thumbnail">
                            <div class="candidate-logo">
                                @if (strlen($user['profile_picture'] ?? '') > 0)
                                    <img width="50" height="50"
                                        style="border-radius: 100% !important; height: 50px !important; width: 50px !important; margin-right: 10px; object-fit:cover !important"
                                        src="{{ $user['profile_picture'] }}" />
                                @else
                                    <div
                                        style="text-transform: uppercase; width: 50px !important; height: 50px !important; margin: 0; margin-right: 10px !important; background-color: #000 !important; color: #fff !important; font-family: sans-serif; font-size: 16px; font-weight: bold; line-height: 1em; padding: 0;">
                                        {{ substr($user['name'], 0, 1) }}
                                    </div>
                                @endif
                            </div>
                        </div>


                        <div class="candidate-information">
                            <div class="title-wrapper">
                                <h1 class="candidate-title"
                                    style="font-size: 16px; font-weight: normal; margin-bottom: 5px; margin-top: 10px;">
                                    {{ $user['name'] }}
                                </h1>
                                <span style="color: #a9a9a9;">{{ $user['related'] }}</span>
                            </div>
                            <div class="candidate-metas">

                            </div>
                        </div>
                    </div>
                @endforeach
                <a href="{{ $data['FRONTEND_URL'] }}" target="_blank"
                    style="background: #000; color: #fff !important; padding: 15px 30px; text-decoration: none !important; border-radius: 20px; display: inline-block; margin: 30px 0 10px 0;">
                    Check Messages</a>
            </div>
            @include('emails.includes.lounge_footer')
