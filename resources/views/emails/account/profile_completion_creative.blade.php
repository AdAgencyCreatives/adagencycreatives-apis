@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style=" font-family: sans-serif; mso-height-rule: exactly;  font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1 class="main-heading">
                        Your Profile Needs You</h1>
                    <div style="border-radius: 5px; max-width: 450px; margin: 0 auto;  line-height:1.5 !important;"
                        class="content">
                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hello
                            {{ $data['first_name'] ?? '' }},</span>

                        <p>We noticed your Ad Agency Creatives profile is incomplete.
                            Did you know your profile can be viewed by Agencies and Recruiters in their search
                            for their next {{ $data['category_name'] ?? '' }}?
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

                        <p>
                            If you like it, tell us, and tell others. Others will tell their agencies.
                            Agencies will post jobs, recruiters will network, and life runs smooth again,
                            for creatives and agencies. Not a bad goal. It’s a better way to get back to work.
                        </p>

                        <p>
                            Help us help you. Good or bad, let us know <a
                                href="mailto:membersupport@adagencycreatives.com"
                                target="_blank">membersupport@adagencycreatives.com</a>.
                        </p>

                        @include('emails.includes.jobboard_footer')