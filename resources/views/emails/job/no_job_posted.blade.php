@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style=" font-family: sans-serif; mso-height-rule: exactly;  font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1 class="main-heading">
                        Creative Talent Search</h1>
                    <div style=" border-radius: 5px; max-width: 450px; margin: 0 auto;  line-height:1.5 !important;"
                        class="content">
                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hello
                            {{ $data['first_name'] ?? '' }},</span>

                        <p>
                            We haven’t seen you around lately. We get you. New is always new. To make it easier, we want
                            to invite you to post your first creative job opportunity with our talented creative users,
                            on us.
                        </p>

                        <p>
                            For a test drive (free job post), simply type code: <strong>HireCreatives</strong> at check out.
                            That’s $149 gift
                            and worth so much more when you hire our talent. Whether it’s now or down the road, we
                            welcome you to lean on our network of creative talent.
                        </p>

                        <p>
                            Talent can apply online or you can redirect them to apply to an external ATS. We have some
                            wonderful features either way.
                        </p>

                        <p>
                            Questions? Reach out anytime: <a
                                href="mailto:membersupport@adagencycreatives.com">membersupport@adagencycreatives.com</a>.
                        </p>

                        @include('emails.includes.jobboard_footer')