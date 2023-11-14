@include('emails.includes.lounge_header')

<tr>
    <td style="padding: 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 14px; color: #000000; font-size: 14px; position: relative;"
        class="body_text_color body_text_size">
        <div style="background:#fff; border-radius: 5px; width: 450px; margin: 0 auto; color:#000000">
            <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi {{ $data['recipient'] }},</span>

            <p>We wanted you to know that you have {{ $data['unread_message_count'] }} new message on
                <a href="{{ $data['APP_URL'] }}" target="_blank">{{ $data['APP_NAME'] }}</a>
            </p>
            <div style="margin: 15px 0;">

                @foreach ($data['recent_messages'] as $user)
                    <div class="candidate-top-wrapper flex-middle-sm"
                        style="display: flex; gap: 15px; margin-bottom: 5px;">
                        <div class="candidate-thumbnail">
                            <div class="candidate-logo">
                                <img width="50" style="border-radius: 50%;" src="{{ $user['profile_picture'] }}">
                            </div>
                        </div>


                        <div class="candidate-information">
                            <div class="title-wrapper">
                                <h1 class="candidate-title"
                                    style="font-size: 16px; font-weight: normal; margin-bottom: 0;">
                                    {{ $user['name'] }}
                                </h1>
                                <span style="color: #ccc;">{{ $user['message_time'] }}</span>
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
