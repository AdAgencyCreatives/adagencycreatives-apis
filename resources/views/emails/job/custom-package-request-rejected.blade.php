<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <title>BuddyPress Emails</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
    <meta name="x-apple-disable-message-reformatting"> <!-- Disable auto-scale in iOS 10 Mail entirely -->
    <title></title> <!-- The title tag shows in email notifications, like Android 4.4. -->

    <!-- CSS Reset -->
    <style type="text/css">
        /* What it does: Remove spaces around the email design added by some email clients. */
        /* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
        }

        p {
            margin: 1em 0 !important;
            padding: 0 !important;
            line-height: 1.5em !important;
            text-align: justify;
        }

        /* What it does: Stops email clients resizing small text. */
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        /* What is does: Centers email on Android 4.4 */
        div[style*="margin: 16px 0"] {
            margin: 0 !important;
        }

        /* What it does: Stops Outlook from adding extra spacing to tables. */
        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }

        /* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */
        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }

        table table table {
            table-layout: auto;
        }

        /* What it does: Uses a better rendering method when resizing images in IE. */
        /* & manages img max widths to ensure content body images don't exceed template width. */
        img {
            -ms-interpolation-mode: bicubic;
            height: auto;
            max-width: 100%;
        }

        /* What it does: A work-around for email clients meddling in triggered links. */
        *[x-apple-data-detectors],
        /* iOS */
        .x-gmail-data-detectors,
        /* Gmail */
        .x-gmail-data-detectors *,
        .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        /* What it does: Prevents Gmail from displaying an download button on large, non-linked images. */
        .a6S {
            display: none !important;
            opacity: 0.01 !important;
        }

        /* If the above doesn't work, add a .g-img class to any image in question. */
        img.g-img+div {
            display: none !important;
        }

        /* What it does: Prevents underlining the button text in Windows 10 */
        .button-link {
            text-decoration: none !important;
        }

        a {
            color: #ffffff;
        }
    </style>

</head>

<body class="email_bg" width="100%" bgcolor="#fff7f7" style="margin: 0; mso-line-height-rule: exactly;">
    <table cellpadding="0" cellspacing="0" border="0" height="100%" width="100%" bgcolor="#fff7f7"
        style="border-collapse:collapse;" class="email_bg">
        <tr>
            <td valign="top">
                <center style="width: 100%; text-align: left;">

                    <div style="max-width: 600px; margin: auto;" class="email-container">
                        <!--[if mso]>
   <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" align="center">
   <tr>
   <td>
   <![endif]-->

                        <!-- Email Header : BEGIN -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center"
                            width="100%" style="max-width: 600px; border-radius: 5px; margin-top: 10px;"
                            bgcolor="#0a0606" class="header_bg">
                            <tr>
                                <td style="text-align: center; padding: 15px 0; font-family: sans-serif; mso-height-rule: exactly; font-weight: bold; color: #ffffff; font-size: 30px"
                                    class="header_text_color header_text_size">
                                    <img width="100%" style="max-width: 500px !important;"
                                        src="https://ad-agency-creatives.s3.amazonaws.com/agency_logo/ad-agency-creatives-logo-black-white.png"
                                        alt="" />
                                </td>
                            </tr>
                        </table>
                        <!-- Email Header : END -->

                        <!-- Email Body : BEGIN -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center"
                            bgcolor="#0a0a0a" width="100%" style="max-width: 600px; border-radius: 5px;"
                            class="body_bg">

                            <!-- 1 Column Text : BEGIN -->
                            <tr>
                                <td>
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                                        width="100%">
                                        <tr>
                                            <td style="padding: 20px; font-family: sans-serif; mso-height-rule: exactly; line-height: 24px; color: #ffffff; font-size: 15px"
                                                class="body_text_color body_text_size">
                                                <div
                                                    style="background:#fff; border-radius: 5px; width: 450px; margin: 0 auto; padding: 30px;color:#000">
                                                    <span style="font-weight: normal; font-size: 20px"
                                                        class="welcome">Hi {{ $data['user']->first_name }},</span>

                                                    <p><span style="color: #000000;">Welcome to
                                                            {{ env('APP_NAME') }}!</span>
                                                    </p>
                                                    <p><span style="color: #000000;">We regret to inform you that your
                                                            custom job request has been rejected.</span></p>

                                                    <p><span style="color: #000000;">Please feel free to
                                                            contact us if you have any further inquiries or would like
                                                            to discuss this decision.</span></p>

                                                    <p>
                                                        Thanks,<br />
                                                        Member Support<br />
                                                        <img width="200"
                                                            src="https://ad-agency-creatives.s3.us-east-1.amazonaws.com/agency_logo/ad-agency-creatives-logo-white-black.png"
                                                            alt="" />
                                                    </p>
                                                    <p style="text-align: center; margin-bottom: 0;">Gather. Inspire. Do
                                                        Cool $#*t!</p>
                                                    <img class="footer-logo"
                                                        style="width: 120px; position: absolute; right: 15px; bottom: 15px;"
                                                        src="https://ad-agency-creatives.s3.amazonaws.com/agency_logo/aac-logo-round-transparent-bold.png">
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <!-- 1 Column Text : BEGIN -->

                        </table>
                        <!-- Email Body : END -->

                        <!-- Email Footer : BEGIN -->
                        <br>
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="left"
                            width="100%" style="max-width: 600px; border-radius: 5px;" bgcolor="#000000"
                            class="footer_bg">
                            <tr>
                                <td style="padding: 15px; width: 100%; font-size: 14px; font-family: sans-serif; mso-height-rule: exactly; line-height: 14px; text-align: left; color: #ffffff; word-break: break-all;"
                                    class="footer_text_color footer_text_size">
                                    <div style="text-align:center;">
                                        <div>© {{ date('Y') }} Ad Agency Creatives. All Rights Reserved.</div>
                                        <div style="display:inline-block; margin-top:5px;" id="footer">
                                            <a style="color: #ffffff; margin-right: 15px;"
                                                href="https://adagencycreatives.com/privacy-policy/">Privacy Policy</a>
                                            <a style="color: #ffffff; margin-right: 15px;"
                                                href="https://adagencycreatives.com/terms-and-conditions/">User
                                                Agreement</a>
                                            <a style="color: #ffffff;"
                                                href="https://adagencycreatives.com/contact">Contact Us</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <!-- Email Footer : END -->

                        <!--[if mso]>
   </td>
   </tr>
   </table>
   <![endif]-->
                    </div>
                </center>
            </td>
        </tr>
    </table>
</body>

</html>
