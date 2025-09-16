@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style=" font-family: sans-serif; mso-height-rule: exactly; line-height: 1.5;  font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1 class="main-heading"> New Job Post</h1>
                    <div style=" border-radius: 5px; max-width: 450px; margin: 0 auto;  line-height:1.5 !important"
                        class="content">
                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hello Member
                            Support,</span>


                        <p>Great news!</p>
                        <p>A New Job <strong><a href="{{ $data['url'] ?? '' }}"
                                    target="_blank">{{ $data['job']->title ?? '' }}</a></strong> has been
                            posted
                            by <strong>
                                @if (strlen($data['agency_profile'] ?? '') > 0)
                                <a href="{{ $data['agency_profile'] ?? '' }}" target="_blank">{{ $data['author'] ?? '' }}</a>
                                @else
                                {{ $data['author'] ?? '' }}
                                @endif
                            </strong>.</p>

                        <h4 style="text-decoration: underline; margin-bottom: 5px;">
                            Posted Job Information</h4>
                        <div><b>Title:
                            </b>
                            <a href="{{ $data['url'] ?? '' }}" target="_blank">{{ $data['job']->title ?? '' }}</a>
                        </div>

                        <div><b>Type:
                            </b>{{ $data['job']->employment_type ?? '' }}
                        </div>

                        <div><b>Category:
                            </b>{{ $data['category'] ?? '' }}
                        </div>

                        <div><b>Posted at:
                            </b>{{ $data['created_at'] ?? '' }}
                        </div>

                        <div><b>Expires at:
                            </b>{{ $data['expired_at'] ?? '' }}
                        </div>

                        <div><b>Posted by:
                            </b>{{ sprintf('%s (%s)', $data['author'] ?? '', $data['agency'] ?? '') }}
                        </div>
                        @include('emails.includes.jobboard_footer')