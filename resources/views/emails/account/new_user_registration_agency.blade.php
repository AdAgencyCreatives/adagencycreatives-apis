@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 0 0 30px; font-family: sans-serif; mso-height-rule: exactly; color: #000000; font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1
                        style="background: #fff; text-align: center; padding: 30px; border-bottom: 2px solid #000;     text-transform: uppercase;">
                        Registration request</h1>
                    <div style="background:#fff; border-radius: 5px; max-width: 450px; margin: 0 auto; color:#000000; line-height:1.5 !important;"
                        class="content">
                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hello Member Support,</span>

                        <p>The following user has requested to join <a href="{{ $data['FRONTEND_URL'] }}"
                                target="_blank">{{ $data['APP_NAME'] }}</a>!</p>

                        <div><b>Name: </b>{{ $data['user']->username ?? '' }}</div>
                        <div><b>Email: </b>{{ $data['user']->email ?? '' }}</div>
                        <div><b>Agency LinkedIn: </b>{{ $data['link'] ?? '' }}</div>
                        <div>
                            <b>Profile: </b>

                            <a href="{{ sprintf('%s/users/%d/details', $data['APP_URL'], $data['user']->id) }}"
                                target="_blank">Profile
                                URL</a>
                        </div>

                        &nbsp;
                        <hr>
                        &nbsp;
                        <div>
                            <b>Approve URL: </b>
                            <a href="{{ $data['APPROVE_URL'] }}" target="_blank">Approve</a>
                        </div>
                        &nbsp;
                        <hr>
                        <div>
                            <b>Deny URL: </b>
                            <a href="{{ $data['DENY_URL'] }}" target="_blank">Deny</a>
                        </div>
                        <hr>
                        @include('emails.includes.jobboard_footer')
