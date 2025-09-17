@include('emails.includes.jobboard_header')

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
        <td align="center">
            <table role="presentation" class="container" width="600" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td style="position:relative;" class="body_text_color body_text_size">
                        <h1 class="main-heading">Creative Talent Search</h1>
                        <div class="content">
                            <span class="welcome">Hello {{ $data['first_name'] ?? '' }},</span>

                            <p>
                                We haven’t seen you around lately. We get you. New is always new. To make it easier,
                                we want to invite you to post your first creative job opportunity with our talented
                                creative users, on us.
                            </p>

                            <p>
                                For a test drive (free job post), simply type code:
                                <strong>HireCreatives</strong> at check out. That’s $149 gift
                                and worth so much more when you hire our talent. Whether it’s now or down the road,
                                we welcome you to lean on our network of creative talent.
                            </p>

                            <p>
                                Talent can apply online or you can redirect them to apply to an external ATS.
                                We have some wonderful features either way.
                            </p>

                            <p>
                                Questions? Reach out anytime:
                                <a href="mailto:membersupport@adagencycreatives.com">
                                    membersupport@adagencycreatives.com
                                </a>.
                            </p>
                            <p>Explore more jobs and update your preferences anytime by visiting your dashboard. </p>
                            <p>Cheers,<br>
                                The Ad Agency Creatives Team.</p>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

@include('emails.includes.jobboard_footer')