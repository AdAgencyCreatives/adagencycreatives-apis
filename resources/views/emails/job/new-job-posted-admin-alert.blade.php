<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>New Job Post Review on Ad Agency Creatives Community</title>
</head>

<body>
    <div style="padding: 50px 0;">
        <table style="max-width: 640px; height: 1315px;" border="0" width="997" cellspacing="0" cellpadding="0"
            align="center">
            <tbody>
                <tr>
                    <td align="center" valign="top"><span style="color: #ffffff;">  </span>
                        <table class="header_bg" style="border-radius: 5px; margin-top: 10px; margin-bottom: 20px;"
                            role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0"
                            align="center" bgcolor="#000">
                            <tbody>
                                <tr>
                                    <td class="header_text_color header_text_size"
                                        style="text-align: center; padding: 15px 0; font-family: sans-serif; font-weight: bold;">
                                        <img src="https://aacstagingsite.wpengine.com/wp-content/uploads/2022/04/AAC-LOGO-500-×-100-px-1.png"
                                            alt="" width="60%" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="top"><!-- Body -->
                        <table class="body_bg" style="border-radius: 5px; background-color: #000; padding: 20px;"
                            role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0"
                            align="center" bgcolor="#000">
                            <tbody>
                                <tr>
                                    <td id="mailtpl_body_bg"
                                        style="background: #fff; border-radius: 5px; margin: 0 auto; padding: 30px; color: #000;"
                                        valign="top"><!-- Content -->
                                        <p
                                            style="padding: 22px 22px; font-family: Arial; font-size: 18px; font-weight: bold; text-align: center; line-height: 100%;">
                                            New Job Post Review</p>
                                        <hr />

                                        <table style="height: 848px;" border="0" width="963" cellspacing="0"
                                            cellpadding="20">
                                            <tbody>
                                                <tr>
                                                    <td valign="top">Hello Member Support,

                                                        Great news!

                                                        A New Job <strong>"{{ $data['job']->title }}"</strong> has been
                                                        posted
                                                        by <strong>{{ $data['author'] }}</strong>. It's time to review
                                                        this job. After reviewing you can approve or reach out for
                                                        additional details if
                                                        needed.

                                                        After approval, welcome them to the community. At Ad Agency
                                                        Creatives it is the little thoughtful and helpful interactions
                                                        that matter.
                                                        <h4><span style="color: #000000;">Posted Job Information</span>
                                                        </h4>
                                                        <table class="blueTable" style="height: 399px;" width="581">
                                                            <thead>
                                                                <tr>
                                                                    <th style="text-align: center;" colspan="2"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td><strong>Title </strong></td>
                                                                    <td>{{ $data['job']->title }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Type</strong></td>
                                                                    <td>{{ $data['job']->employment_type }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Category</strong></td>
                                                                    <td>{{ $data['category'] }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Posted</strong></td>
                                                                    <td>{{ $data['job']->created_at }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Expires</strong></td>
                                                                    <td>{{ $data['job']->expired_at }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Posted By</strong></td>
                                                                    <td>{{ $data['author'] }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        &nbsp;

                                                        Thank you,

                                                        Ad Agency Creatives Community
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="top">
                        <table id="template_footer"
                            style="background-color: #000; margin-top: 20px; border-radius: 6px; width: 100%;"
                            border="0" cellspacing="0" cellpadding="10">
                            <tbody>
                                <tr>
                                    <td valign="top">
                                        <table style="height: 65px;" border="0" width="967" cellspacing="0"
                                            cellpadding="10">
                                            <tbody>
                                                <tr>
                                                    <td style="text-align: center; height: 100px;"><img
                                                            class="alignnone size-medium wp-image-3243 aligncenter"
                                                            src="https://aacstagingsite.wpengine.com/wp-content/uploads/2023/04/AAC-LOGO-dark-300x150.png"
                                                            alt="" width="300" height="150" /></td>
                                                </tr>
                                                <tr>
                                                    <td id="credit"
                                                        style="border: 0; color: #000; font-family: Arial; font-size: 12px; line-height: 125%; text-align: center;"
                                                        colspan="2" valign="middle">&nbsp;

                                                        <a style="color: #ffffff;"
                                                            href="mailto:info@AdAgencyCreatives.com">info@AdAgencyCreatives.com</a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
