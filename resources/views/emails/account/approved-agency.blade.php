@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style=" font-family: sans-serif; mso-height-rule: exactly; font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1 class="main-heading">
                        Account Activated</h1>
                    <div style=" border-radius: 5px; max-width: 450px; margin: 0 auto;  line-height:1.5 !important"
                        class="content">
                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi
                            {{ $data['user']->first_name ?? '' }},</span>

                        <p>Welcome to the <a href="{{ $data['FRONTEND_URL'] }}"
                                target="_blank">{{ $data['APP_NAME'] }}</a> community.
                        </p>

                        <div style="">To help you get the most out of your account, you’ll want to
                            first complete <a href="{{ $data['FRONTEND_URL'] }}/profile" target="_blank">your Agency's
                                Profile.</a>
                            You can start by telling everyone a bit about your agency and speciality.</div>
                        <div style="margin-top: 20px;">Once you’ve got your profile looking good, take a moment to
                            adjust
                            your preferences. After that,
                            you’re ready to post jobs and find amazing talent for your team.</div>
                        <div style="margin-top: 20px;">If you forget your password, no problem. You can <a
                                href="{{ $data['FRONTEND_URL'] }}/reset-password" target="_blank">reset it
                                here.</a>

                        </div>
                        @include('emails.includes.jobboard_footer')