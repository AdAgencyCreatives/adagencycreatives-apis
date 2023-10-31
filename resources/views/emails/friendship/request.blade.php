@include('emails.includes.lounge_header')
<tr>
    <td style="padding: 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 14px; color: #000000; font-size: 14px; position: relative;"
        class="body_text_color body_text_size">
        <div style="background:#fff; border-radius: 5px; width: 450px; margin: 0 auto; color:#000000">
            <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi {{ $data['recipient'] }},</span>

            <p>{{ $data['inviter'] }} would like to become friends in The
                Lounge
                on {{ env('APP_NAME') }} .</p>

            <p>Click <a href="{{ env('FRONTEND_URL') }}/profile" style="color: #3c5cc4;" target="_blank">my
                    requests</a> to
                accept this invite and start a conversation, or click <a href="{{ $data['iniviter_profile'] }}"
                    style="color: #3c5cc4;" target="_blank">here</a>
                to view
                {{ $data['inviter'] }}’s profile.</p>
            @include('emails.includes.lounge_footer')
