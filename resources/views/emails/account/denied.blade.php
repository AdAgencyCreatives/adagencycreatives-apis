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
                        Registration Denied</h1>
                    <div style="background:#fff; border-radius: 5px; width: 450px; margin: 0 auto; color:#000000">
                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi
                            {{ $data['user']->first_name ?? '' }},</span>

                        <p style="margin-bottom: 30px;">We appreciate your interest in joining {{ $data['APP_NAME'] }}.
                            Unfortunately, the following
                            account has not been approved at this time.
                        </p>
                        <h4 style="text-decoration: underline; margin-bottom: 5px;">Details:</h4>
                        <div><b>Name: </b>{{ $data['user']->username ?? '' }}</div>
                        <div><b>Email: </b>{{ $data['user']->email ?? '' }}</div>


                        <div style="margin-top: 30px;">If you have any questions about this decision, reach out to
                            info@adagencycreatives.com. Our team receives many inquiries and requests, so please allow
                            up to a few business days for us to respond.
                        </div>

                        @include('emails.includes.jobboard_footer')
