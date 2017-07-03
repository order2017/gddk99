<?php

namespace App\Http\Controllers\Admin;

use App\Common\Common;
use App\Consultant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Redirect;

class ConsultantController extends Controller
{
    // 顾问列表
    public function ConsultantList(){
        $consultant = Consultant::where('con_type',1)->paginate(15);
        return view('admin.consultant-list',['consultant' => $consultant]);
    }

    // 顾问列表--存储
    public function ConsultantStore(){
        return view('admin.consultant-store');
    }

    // 顾问列表--存储--成功
    public function ConsultantStoreOk(Request $request,Common $common,Consultant $consultant){

        // 接收参数
        $data = $request->except(['_token','con_pic','con_wx_pic','con_pic_all']);

        if($request->isMethod('POST'))
        {
            // 上传图片
            $avatar =$common->If_val($common->FileOne($request->file('con_pic')));
            $qrcode =$common->If_val($common->FileOne($request->file('con_wx_pic')));
            $much =$common->If_val($common->FileAll($request->file('con_pic_all')));

            // 组装数据
            $arr = array_merge($data,['con_type'=>Consultant::CON_TYPE_ONE,'con_pic'=>$avatar,'con_wx_pic'=>$qrcode,'con_pic_all'=>$much]);

            // 保存数据
            $consultant->create($arr);

            return redirect('/admin/consultant-list');

        }

    }

    // 顾问列表--存储---更新
    public function ConsultantEdit($id){
        $consultant = Consultant::find($id);
        return view('admin.consultant-edit',['consultant'=>$consultant]);
    }

    // 顾问列表--存储---更新---成功
    public function ConsultantEditOk(Request $request,Common $common,Consultant $consultant){

        // 接收参数
        $data = $request->except(['_token','con_pic','con_wx_pic','con_pic_all','con_id']);
        $data_id = $request->get('con_id');

        if($request->isMethod('POST'))
        {
            // 上传图片
            $avatar =$common->If_val($common->FileOne($request->file('con_pic')));
            $qrcode =$common->If_val($common->FileOne($request->file('con_wx_pic')));
            $much =$common->If_val($common->FileAll($request->file('con_pic_all')));

            $con = $consultant->find($data_id);

            // 判断头像是否是空，为空不替换头像，不为空替换头像-------------单张图片
            if (empty($avatar)){

                // 更新数据--为空
                $consultant->where('id',$data_id)->update($data);
            }else{
                // 删除替换目录图片
                $common->DataPic($avatar,$con['con_pic']);

                // 组装数据
                $arr = array_merge($data,['con_pic'=>$avatar]);

                // 更新数据--不为空
                $consultant->where('id',$data_id)->update($arr);
            }

            // 判断二维码是否是空，为空不替换二维码，不为空替换二维码----------单张图片
            if (empty($qrcode)){

                // 更新数据--为空
                $consultant->where('id',$data_id)->update($data);
            }else{
                // 删除替换目录图片
                $common->DataPic($qrcode,$con['con_wx_pic']);

                // 组装数据
                $arr = array_merge($data,['con_wx_pic'=>$qrcode]);

                // 更新数据--不为空
                $consultant->where('id',$data_id)->update($arr);
            }

            // 判断店铺图片是否是空，为空不替换店铺图片，不为空替换店铺图片----------多张图片
            if (empty($much)){

                // 更新数据--为空
                $consultant->where('id',$data_id)->update($data);
            }else{
                // 删除替换目录图片
                $common->DataPicAllJson($much,$con['con_pic_all']);

                // 组装数据
                $arr = array_merge($data,['con_pic_all'=>$much]);

                // 更新数据--不为空
                $consultant->where('id',$data_id)->update($arr);
            }

            return redirect('/admin/consultant-list');
        }

    }


    // 顾问列表--删除---成功
    public function ConsultantDel($id,Consultant $consultant,Common $common){
        $conId = $consultant->find($id);

        $common->DataPicDel($conId['con_pic']);
        $common->DataPicDelAll($conId['con_pic_all']);
        $common->DataPicDel($conId['con_wx_pic']);

        $conId->delete();
        return Redirect::back();
    }

    // 店铺
    public function ShopList(){
        $consultant = Consultant::where('con_type',2)->paginate(15);
        return view('admin.shop-list',['consultant' => $consultant]);
    }

    public function ShopStore(){
        return view('admin.shop-store');
    }

    public function ShopStoreOk(Request $request){

        $conName = $request->get('con_name');
        $conPic = $request->file('con_pic');
        $conPicAll = $request->file('con_pic_all');
        $conPerson = $request->get('con_person');
        $conTime = $request->get('con_time');
        $conTel = $request->get('con_tel');
        $conWxPic = $request->file('con_wx_pic');
        $conContent = $request->get('con_content');
        $conContentArea = $request->get('con_content_area');
        $conContentRange = $request->get('con_content_range');
        $conAdd = $request->get('con_add');

        if($request->isMethod('POST'))
        {
            $pic = new Common();
            $av_pic = $pic->FileOne($conPic);
            $wx = $pic->FileOne($conWxPic);
            $all_pic = $pic->FileAll($conPicAll);

            //实例化，保存数据
            $consultant = new Consultant();
            $consultant ->con_name = $conName;
            $consultant ->con_pic = empty($av_pic)? null :$av_pic;
            $consultant ->con_pic_all = empty($all_pic)? null :$all_pic;
            $consultant ->con_person = $conPerson;
            $consultant ->con_time = $conTime;
            $consultant ->con_tel = $conTel;
            $consultant ->con_wx_pic = empty($wx)? null :$wx;
            $consultant ->con_content = $conContent;
            $consultant ->con_content_area = $conContentArea;
            $consultant ->con_content_range = $conContentRange;
            $consultant ->con_add = $conAdd;
            $consultant ->con_type = Consultant::CON_TYPE_TWO;
            $consultant ->save();

            return redirect('/admin/shop-list');
        }

    }

    public function ShopEdit($id){
        $consultant = Consultant::find($id);
        return view('admin.shop-edit',['consultant'=>$consultant]);
    }

    public function ShopEditOk(Request $request){
        $conId = $request->get('con_id');
        $conName = $request->get('con_name');
        $conPic = $request->file('con_pic');
        $conPicAll = $request->file('con_pic_all');
        $conPerson = $request->get('con_person');
        $conTime = $request->get('con_time');
        $conTel = $request->get('con_tel');
        $conWxPic = $request->file('con_wx_pic');
        $conContent = $request->get('con_content');
        $conContentArea = $request->get('con_content_area');
        $conContentRange = $request->get('con_content_range');
        $conAdd = $request->get('con_add');

        if($request->isMethod('POST'))
        {

            $pic = new Common();
            $av_pic = $pic->FileOne($conPic);
            $wx = $pic->FileOne($conWxPic);
            $all_pic = $pic->FileAll($conPicAll);

            if(empty($av_pic) && empty($wx) && empty($all_pic)){

                $consultant = Consultant::find($conId);
                $consultant ->con_name = $conName;
                $consultant ->con_person = $conPerson;
                $consultant ->con_time = $conTime;
                $consultant ->con_tel = $conTel;
                $consultant ->con_content = $conContent;
                $consultant ->con_content_area = $conContentArea;
                $consultant ->con_content_range = $conContentRange;
                $consultant ->con_add = $conAdd;
                $consultant ->save();

            }else{

                $consultant = Consultant::find($conId);

                if (isset($conPic)){
                    if(!empty($consultant['con_pic'])){
                        $images = public_path('build/uploads/') . $consultant['con_pic'];
                        if (file_exists ($images )) {
                            unlink ($images);
                        }
                    }
                }

                if (isset($conPicAll)){
                    if (!$consultant['con_pic_all']==""){
                        foreach (unserialize($consultant['con_pic_all']) as $basicList){
                            if(!empty($basicList)){
                                $images = public_path('build/uploads/') . $basicList;
                                if (file_exists ($images )) {
                                    unlink ($images);
                                }
                            }
                        }
                    }
                }

                if (isset($conWxPic)){
                    if(!empty($consultant['con_wx_pic'])){
                        $images = public_path('build/uploads/') . $consultant['con_wx_pic'];
                        if (file_exists ($images )) {
                            unlink ($images);
                        }
                    }
                }

                $consultant ->con_name = $conName;
                $consultant ->con_pic = empty($av_pic)? $consultant['con_pic'] :$av_pic;
                $consultant ->con_pic_all = empty($all_pic)? $consultant['con_pic_all'] :$all_pic;
                $consultant ->con_person = $conPerson;
                $consultant ->con_time = $conTime;
                $consultant ->con_tel = $conTel;
                $consultant ->con_wx_pic = empty($wx)? $consultant['con_wx_pic'] :$wx;
                $consultant ->con_content = $conContent;
                $consultant ->con_content_area = $conContentArea;
                $consultant ->con_content_range = $conContentRange;
                $consultant ->con_add = $conAdd;
                $consultant ->save();
            }
            return redirect('/admin/shop-list');
        }
    }

    public function ShopDel($id){
        $conId =Consultant::find($id);
        if(!empty($conId['con_pic'])){
            $images = public_path('build/uploads/') . $conId['con_pic'];
            if (file_exists ($images )) {
                unlink ($images);
            }
        }
        foreach (unserialize($conId['con_pic_all']) as $basicList){
            if(!empty($basicList)){
                $images = public_path('build/uploads/') . $basicList;
                if (file_exists ($images )) {
                    unlink ($images);
                }
            }
        }
        if(!empty($conId['con_wx_pic'])){
            $images = public_path('build/uploads/') . $conId['con_wx_pic'];
            if (file_exists ($images )) {
                unlink ($images);
            }
        }        $conId->delete();
        return Redirect::back();
    }

}
