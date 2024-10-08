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
                        Your Agency Profile</h1>
                    <div style="background:#fff; border-radius: 5px; max-width: 450px; margin: 0 auto; color:#000000; line-height:1.5 !important;"
                        class="content">
                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hello
                            {{ $data['first_name'] ?? '' }},</span>

                        <p>
                            We noticed your Ad Agency Creatives agency profile is incomplete.
                            Our community is currently viewing your agency as <a
                                href="{{ sprintf('%s', $data['profile_url']) }}" target="_blank">profile</a>.
                        </p>

                        <p>
                            When you are entering your profile,
                            we invite you to post your first creative job opportunity.
                            Experience a better way for your team to source creative talent solutions.
                            A community in a network.
                        </p>

                        <p>
                            Help us help you. Good or bad, let us know <a
                                href="mailto:membersupport@adagencycreatives.com">membersupport@adagencycreatives.com</a>.
                        </p>

                        @include('emails.includes.jobboard_footer')
