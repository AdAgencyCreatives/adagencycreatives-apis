@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 0 0 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 14px; color: #000000; font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1
                        style="background: #fff; text-align: center; padding: 30px; border-bottom: 2px solid #000;     text-transform: uppercase;">
                        Registration request</h1>
                    <div style="background:#fff; border-radius: 5px; width: 450px; margin: 0 auto; color:#000000">
                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hello Member Support,</span>

                        <p>The following user has requested to join {{ $data['APP_NAME'] }}:</p>

                        <div><b>Name: </b>{{ $data['user']->username ?? '' }}</div>
                        <div><b>Email: </b>{{ $data['user']->email ?? '' }}</div>
                        <div><b>Creative Portfolio: </b>{{ $data['link'] ?? '' }}</div>

                        <div>
                            <b>Profile: </b>

                            <a href="{{ sprintf('%s/users/%d/details', $data['APP_URL'], $data['user']->id) }}"
                                target="_blank">Profile
                                URL</a>
                        </div>

                        @include('emails.includes.jobboard_footer')
