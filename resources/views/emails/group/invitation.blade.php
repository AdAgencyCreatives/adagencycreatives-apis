@include('emails.includes.lounge_header')
<tr>
    <td style="padding: 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 14px; font-size: 14px; position: relative;"
        class="body_text_color body_text_size">
        <div style=" border-radius: 5px; max-width: 900px; margin: 0 auto; " class="content">
            <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi {{ $data['recipient'] }},</span>

            <p><a href="{{ $data['inviter_profile_url'] }}">{{ $data['inviter'] }}</a> would like
                you to join "<a href="{{ $data['group_url'] }}">{{ $data['group'] }}</a>".</p>

            <p>To accept this invitation <a href="{{ $data['action_url'] }}">click here</a> or to learn more,
            <a href="{{ $data['group_url'] }}">visit</a> the group.</p>
            <p>Explore more jobs and update your preferences anytime by visiting your dashboard.</p>
                <p>Cheers,<br>
                    The Ad Agency Creatives Team.</p>
            @include('emails.includes.lounge_footer')
