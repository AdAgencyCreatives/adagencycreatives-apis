@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style=" font-family: sans-serif; mso-height-rule: exactly;  font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1 class="main-heading">
                        Your Agency Profile</h1>
                    <div style=" border-radius: 5px; max-width: 450px; margin: 0 auto;  line-height:1.5 !important;"
                        class="content">
                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hello
                            {{ $data['first_name'] ?? '' }},</span>

                        <p>
                            We noticed your Ad Agency Creatives agency profile is incomplete.
                            To view your current agency profile click <a
                                href="{{ $data['profile_url'] ?? '' }}" target="_blank">here</a>.
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