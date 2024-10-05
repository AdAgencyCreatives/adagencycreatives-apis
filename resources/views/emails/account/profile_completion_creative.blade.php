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
                        Your Profile</h1>
                    <div style="background:#fff; border-radius: 5px; max-width: 450px; margin: 0 auto; color:#000000; line-height:1.5 !important;"
                        class="content">
                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hello
                            {{ $data['first_name'] ?? '' }},</span>

                        <p>We noticed your Ad Agency Creatives profile is incomplete.
                            Did you know your profile can be viewed by Agencies and Recruiters in their search
                            for their next {{ $data['category_name'] ?? '' }}? Don’t panic, your phone number is private
                            until you apply for the job.
                            We know you have high standards, click <a
                                href="{{ sprintf('%s/my-resume', $data['FRONTEND_URL']) }}" target="_blank">here</a> to
                            view your current profile.
                        </p>

                        <p>
                            When you’re setting up your profile, we invite you to introduce yourself to <a
                                href="{{ sprintf('%s/community', $data['FRONTEND_URL']) }}" target="_blank">The
                                Lounge</a> and
                            lean on your community. Experience a better way to network with fellow Ad Agency Creatives.
                        </p>

                        @include('emails.includes.jobboard_footer')