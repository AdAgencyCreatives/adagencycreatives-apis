@include('emails.includes.lounge_header')
<tr>
    <td style="padding: 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 14px; color: #000000; font-size: 14px; position: relative;"
        class="body_text_color body_text_size">
        <div style="background:#fff; border-radius: 5px; width: 450px; margin: 0 auto; color:#000000">
            <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi {{ $data['recipient'] }},</span>

            <p>{{ $data['member'] }} has accepted your request in The Lounge on {{ env('APP_NAME') }}</p>
            <p>Click <a href="{{ env('FRONTEND_URL') }}/job-messages" style="color: #3c5cc4;">my
                    requests</a> to start the
                conversation.</p>
            @include('emails.includes.lounge_footer')
