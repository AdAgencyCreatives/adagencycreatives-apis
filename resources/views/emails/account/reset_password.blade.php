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
                        Password Reset</h1>
                    <div style="background:#fff; border-radius: 5px; width: 450px; margin: 0 auto; color:#000000">
                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi
                            {{ $userName }},</span>

                        <p style="margin-bottom: 20px;">We received a request to reset your account password. If that
                            was you,
                            <a href="{{ $url }}" target="_blank">click here</a>.
                        </p>

                        <p style="margin-bottom: 20px;">If you didn’t make this request, you can safely ignore and
                            delete this email.
                        </p>

                        <p style="margin-bottom: 20px;">As always, be sure to update your password often and keep your
                            information confidential.
                        </p>

                        <p>If link is not clickable, copy and paste the folowing URL into the browser.
                        </p>
                        <p style="overflow-wrap: break-word; word-wrap: break-word;"> {{ $url }}</p>

                        @include('emails.includes.jobboard_footer')
