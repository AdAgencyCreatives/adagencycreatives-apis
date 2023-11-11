@include('emails.includes.lounge_header')

<tr>
    <td style="padding: 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 14px; color: #000000; font-size: 14px; position: relative;"
        class="body_text_color body_text_size">
        <div style="background:#fff; border-radius: 5px; width: 450px; margin: 0 auto; color:#000000">
            <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi {{ $data['recipient'] }},</span>

            <p>We wanted you to know that you have {{ $data['message_count'] }} new message on
                ({{ $data['APP_NAME'] }}).</p>
            <div style="margin: 15px 0;">

                <a href="{{ $data['profile_url'] }}"
                    style="background: #000; color: #fff; padding: 15px 30px; text-decoration: none; border-radius: 20px; display: inline-block; margin: 0 0 10px 0;">
                    Check Messages</a>
            </div>
            @include('emails.includes.lounge_footer')
