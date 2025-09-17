@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style=" font-family: sans-serif; mso-height-rule: exactly;  font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1 class="main-heading">
                        Account Activated</h1>
                    <div style=" border-radius: 5px; max-width: 900px; margin: 0 auto; line-height:1.5 !important"
                        class="content">
                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi
                            {{ $data['user']->first_name ?? '' }},</span>

                        <p>Welcome to <a href="{{ $data['FRONTEND_URL'] }}" target="_blank">{{ $data['APP_NAME'] }}</a>
                            community.
                        </p>

                        <div style="">To help you get the most out of your account, you'll want to
                            complete your <a href="{{ $data['FRONTEND_URL'] }}/profile" target="_blank">profile</a>
                            first.
                            You can start by telling everyone a bit about yourself, your skills, and your goals.</div>
                        <div style="margin-top: 20px;">Once you’ve got a strong profile and resume, take a moment to
                            adjust your preferences.
                            After that, you're all set. You'll receive notifications when new jobs
                            are posted or you're invited to apply by a recruiter or agency.</div>
                        <div style="margin-top: 20px;">If you forget your password, no problem. You can reset it <a
                                href="{{ $data['FRONTEND_URL'] }}/forgot-password?email=ali@gmail.com&auto-submit=true"
                                target="_blank">here</a>.
                        </div>
                        <p>Explore more jobs and update your preferences anytime by visiting your dashboard.</p>
                        <p>Cheers,<br>
                            The Ad Agency Creatives Team.</p>
                        @include('emails.includes.jobboard_footer')