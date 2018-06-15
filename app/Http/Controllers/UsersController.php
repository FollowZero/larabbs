<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Handlers\ImageUploadHandler;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['show']]);
    }

    public function test(){
        if ($pids) {
            $result=self::whereIn('uid',$pids)->where($where)->update(['count'=>DB::raw('`count`-'.$nums)]);
            $p_team_ids = $pid['my_team_ids'];
            if($p_team_ids){
                $p_ids =str2arr($p_team_ids);
                $child_ids =  Users::init()->getChildrenIds($uid);
                if($child_ids){
                    $my_team_ids = array_intersect($p_ids,$child_ids);
                    if($my_team_ids){
                        $uids = Users::where('is_use',1)->whereIn('uid',$my_team_ids)->pluck('uid')->toArray();
                        $uids = array_del_by_val($uids,$uid);
                        $my_count = 0;
                        if($uids){
                            $my_count = count($uids);
                        }
                        self::where('uid',$uid)->update(['my_team_ids'=>','.arr2str($my_team_ids).',','count'=>$my_count]);
                    }
                }
                $new_team_ids = array_diff($p_ids,$child_ids);
                $new_team_ids = arr2str(array_del_by_val($new_team_ids,$uid));
                \Log::debug(var_export(compact('p_ids','child_ids','new_team_ids'),true));
                if($new_team_ids){
                    $use_id = Users::where('is_use',1)->whereIn('uid',$new_team_ids)->pluck('uid')->toArray();
                    $count = 0;
                    if(!$use_id){
                        $count = count($use_id);
                    }
                    self::where('uid',$pid['uid'])->update(['my_team_ids'=>','.$new_team_ids.',','count'=>$count]);
                }
            }
            if ($result) {
                return true;
            }
        }
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('update',$user);
        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, ImageUploadHandler $uploader, User $user)
    {
        $this->authorize('update',$user);
        $data = $request->all();

        if ($request->avatar) {
            $result = $uploader->save($request->avatar, 'avatars', $user->id,362);
            if ($result) {
                $data['avatar'] = $result['path'];
            }
        }

        $user->update($data);

        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }
}
