@include('emails.includes.lounge_header')
<tr>
    <td style="padding: 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 1.5;  font-size: 14px; position: relative;"
        class="body_text_color body_text_size">
        <div style=" border-radius: 5px; max-width: 450px; margin: 0 auto; " class="content">
            <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi {{ $data['recipient'] }},</span>

            <p>{{ $data['member'] }} has accepted your request in The Lounge on <a href="{{ $data['FRONTEND_URL'] }}"
                    target="_blank">{{ $data['APP_NAME'] }}</a>.
                Click <a href="{{ $data['FRONTEND_URL'] }}/friends" target="_blank" style="color: #3c5cc4;">here</a> to
                start a conversation.</p>

            @include('emails.includes.lounge_footer')
        </div>
    </td>
</tr>
