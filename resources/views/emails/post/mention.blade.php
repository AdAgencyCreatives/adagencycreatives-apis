@include('emails.includes.lounge_header')
<tr>
    <td style="padding: 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 14px; color: #000000; font-size: 14px; position: relative;"
        class="body_text_color body_text_size">
        <div style="background:#fff; border-radius: 5px; max-width: 450px; margin: 0 auto; color:#000000" class="content">
            <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi {{ $data['recipient'] }},</span>

            <p><a href="{{ $data['inviter_profile_url'] }}" target="_blank">{{ $data['inviter'] }}</a> mentioned you in
                "{{ $data['group'] }}".</p>


            <div class="candidate-top-wrapper flex-middle-sm" style="display: flex; gap: 15px; ">
                <div class="candidate-thumbnail">
                    <div class="candidate-logo">
                        <img width="50" style="border-radius: 50%;" src="{{ $data['profile_picture'] }}">
                    </div>
                </div>

                <div class="candidate-information">
                    <div class="title-wrapper">
                        <h1 class="candidate-title" style="font-size: 16px; font-weight: normal; margin-bottom: 0;">
                            {{ $data['name'] }}
                        </h1>
                        <span style="color: #ccc;">{{ $data['post_time'] }}</span>
                    </div>
                </div>
            </div>

            <p style="margin-top: -10px;">Click to see the
                <a href="{{ $data['group_url'] }}" target="_blank"
                    style="background: #000; color: #fff !important; padding: 15px 30px; text-decoration: none !important; border-radius: 20px; display: inline-block; margin: 30px 0 10px 0;">
                    Post</a>
            </p>

            @include('emails.includes.lounge_footer')
