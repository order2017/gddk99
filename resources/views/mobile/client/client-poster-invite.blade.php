@extends('layouts.mobile')
@section('content')

    <div class="am-list-news-hd am-cf" style="padding-left: 10px;">
        <h2>向{{ $member['member_surname'] }}申请成为推客</h2>
    </div>

    <div class="pet_article_like" style="margin-top: 0;">
        <div class="pet_content_main pet_article_like_delete">
            <div data-am-widget="list_news" class="am-list-news am-list-news-default am-no-layout">
                <div class="am-list-news-bd">
                    <ul class="am-list">
                        <li class="am-g am-list-item-desced am-list-item-thumbed am-list-item-thumb-right pet_hd_list">
                            <div class="pet_hd_block_title">{{ $member['member_surname'] }}</div>
                            <div class="pet_hd_block_map">手机号：{{ $member['member_mobile'] }}</div>
                            <div class="pet_hd_block_map"><font color="red">已有36名人员向他申请推客</font></div>
                            <div class="pet_hd_block_tag"><span class="hd_tag_jh" onclick="javascript:window.location='{{ url('/mobile/member/push-firm-apply',['member_id'=>$member['member_id']]) }}'">企业申请</span> <span onclick="javascript:window.location='{{ url('/mobile/member/push-person-apply',['member_id'=>$member['member_id']]) }}'">个人申请</span></div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection