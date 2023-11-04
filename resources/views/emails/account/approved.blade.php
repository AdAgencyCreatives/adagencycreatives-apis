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
                        Account Activated</h1>
                    <div style="background:#fff; border-radius: 5px; width: 450px; margin: 0 auto; color:#000000">
                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi
                            {{ $data['user']->first_name ?? '' }},</span>

                        <p>Welcome to {{ $data['APP_NAME'] }}!</p>

                        <div style="margin-bottom: 30px;">To help you get the most out of your account, you’ll want to
                            first complete your <a href="{{ env('FRONTEND_URL') }}/profile" target="_blank">Profile.</a>
                            You can start by telling everyone a bit about yourself, your skills, and your goals.</div>
                        <div style="margin-bottom: 30px;">Once you’ve got your profile popping, take a moment to adjust
                            your preferences. After that,
                            you’re ready to get out there and start making some new connections!</div>
                        <div style="margin-bottom: 30px;">If you forget your password, no problem. You can reset it
                            <a href="{{ $data['FRONTEND_RESET_PASSWORD_URL'] }}" target="_blank">here.</a>

                        </div>


                        @include('emails.includes.jobboard_footer')
