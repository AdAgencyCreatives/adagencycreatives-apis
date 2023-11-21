@include('emails.includes.lounge_header')
<tr>
    <td style="padding: 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 14px; color: #000000; font-size: 14px; position: relative;"
        class="body_text_color body_text_size">
        <div style="background:#fff; border-radius: 5px; max-width: 450px; margin: 0 auto; color:#000000" class="content">
            <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi {{ $data['recipient'] }},</span>

            <p><a href="{{ $data['inviter_profile_url'] }}">{{ $data['inviter'] }}</a> would like
                you to join "{{ $data['group'] }}".</p>

            <p>To accept this invitation <a href="{{ $data['inviter_profile_url'] }}">click here</a> or to learn more
                {visit the group}</p>
            @include('emails.includes.lounge_footer')
