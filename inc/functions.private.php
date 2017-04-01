<?php
    //获取用户头像
    function getAvatar($memberid, $size = 's'){
        switch($size){
            case 's':
                $size = 24;
                break;
            case 'n':
                $size = 48;
                break;
            default: // 'l'
                $size = 120;
        }
        if(file_exists('uploads/member/'.$memberid.'/avatar/'.$size.'.jpg')){
            $avatar = 'uploads/member/'.$memberid.'/avatar/'.$size.'.jpg';
        }else{
            $avatar = 'static/img/avatar_'.$size.'.jpg';
        }
        return $avatar;
    }
    //获取壁纸信息
    function getWallpaper(){
        global $db;
        if(checkLogin()){
            $rs = $db->get('tb_member', [
                'wallpaper_id',
                'wallpapertype',
                'wallpaperwebsite',
                'wallpaperstate'
            ], ['tbid' => session('member_id')]);
            switch($rs['wallpaperstate']){
                case '1':
                case '2':
                    $table = $rs['wallpaperstate'] == 1 ? 'tb_wallpaper' : 'tb_pwallpaper';
                    $wallpaper = $db->get($table, ['url', 'width', 'height'], ['tbid' => $rs['wallpaper_id']]);
                    $wallpaper_array = [
                        $rs['wallpaperstate'],
                        $wallpaper['url'],
                        $rs['wallpapertype'],
                        $wallpaper['width'],
                        $wallpaper['height']
                    ];
                    break;
                case '3':
                    $wallpaper_array = [
                        $rs['wallpaperstate'],
                        $rs['wallpaperwebsite']
                    ];
                    break;
            }
        }else{
            $set = $db->get('tb_setting', ['wallpaper_id', 'wallpapertype']);
            $wallpaper = $db->get('tb_wallpaper', ['url', 'width', 'height'], ['tbid' => $set['wallpaper_id']]);
            $wallpaper_array = [
                1,
                $wallpaper['url'],
                $set['wallpapertype'],
                $wallpaper['width'],
                $wallpaper['height']
            ];
        }
        return implode('<{|}>', $wallpaper_array);
    }
    //获取窗口皮肤
    function getSkin(){
        global $db;
        if(checkLogin()){
            $skin = $db->get('tb_member', 'skin', ['tbid' => session('member_id')]);
        }else{
            $skin = $db->get('tb_setting', 'skin');
        }
        return $skin;
    }
    //获取应用码头位置
    function getDockPos(){
        global $db;
        if(checkLogin()){
            $dockpos = $db->get('tb_member', 'dockpos', ['tbid' => session('member_id')]);
        }else{
            $dockpos = $db->get('tb_setting', 'dockpos');
        }
        return $dockpos;
    }
    //获取图标排列方式
    function getAppXY(){
        global $db;
        if(checkLogin()){
            $appxy = $db->get('tb_member', 'appxy', ['tbid' => session('member_id')]);
        }else{
            $appxy = $db->get('tb_setting', 'appxy');
        }
        return $appxy;
    }
    //获取图标显示尺寸
    function getAppSize(){
        global $db;
        if(checkLogin()){
            $appsize = $db->get('tb_member', 'appsize', ['tbid' => session('member_id')]);
        }else{
            $appsize = $db->get('tb_setting', 'appsize');
        }
        return $appsize;
    }
    //获取图标垂直间距
    function getAppVerticalSpacing(){
        global $db;
        if(checkLogin()){
            $appverticalspacing = $db->get('tb_member', 'appverticalspacing', ['tbid' => session('member_id')]);
        }else{
            $appverticalspacing = $db->get('tb_setting', 'appverticalspacing');
        }
        return $appverticalspacing;
    }
    //获取图标水平间距
    function getAppHorizontalSpacing(){
        global $db;
        if(checkLogin()){
            $apphorizontalspacing = $db->get('tb_member', 'apphorizontalspacing', ['tbid' => session('member_id')]);
        }else{
            $apphorizontalspacing = $db->get('tb_setting', 'apphorizontalspacing');
        }
        return $apphorizontalspacing;
    }
    //获取默认桌面
    function getDesk(){
        global $db;
        if(checkLogin()){
            $desk = $db->get('tb_member', 'desk', ['tbid' => session('member_id')]);
        }else{
            $desk = $db->get('tb_setting', 'desk');
        }
        return $desk;
    }
    //获取图片缩略图地址
    function getSimgSrc($string){
        return preg_replace("#(\w*\..*)$#U", "s_\${1}", $string);
    }
    //安装应用
    function addApp($opt){
        global $db;
        switch($opt['type']){
            case 'file':
                $db->insert('tb_member_app', [
                    'icon' => $opt['icon'],
                    'name' => $opt['name'],
                    'url' => $opt['url'],
                    'ext' => $opt['ext'],
                    'size' => $opt['size'],
                    'width' => 600,
                    'height' => 400,
                    'type' => $opt['type'],
                    'dt' => date('Y-m-d H:i:s'),
                    'lastdt' => date('Y-m-d H:i:s'),
                    'member_id' => session('member_id')
                ]);
                $appid = $db->id();
                break;
            case 'folder':
                $db->insert('tb_member_app', [
                    'icon' => 'static/img/folder.png',
                    'name' => $opt['name'],
                    'width' => 610,
                    'height' => 400,
                    'type' => $opt['type'],
                    'dt' => date('Y-m-d H:i:s'),
                    'lastdt' => date('Y-m-d H:i:s'),
                    'member_id' => session('member_id')
                ]);
                $appid = $db->id();
                break;
            case 'pwindow':
            case 'pwidget':
                $db->insert('tb_member_app', [
                    'icon' => $opt['icon'],
                    'name' => $opt['name'],
                    'url' => $opt['url'],
                    'type' => $opt['type'],
                    'width' => $opt['width'],
                    'height' => $opt['height'],
                    'isresize' => $opt['isresize'],
                    'isopenmax' => $opt['isopenmax'],
                    'isflash' => $opt['isflash'],
                    'dt' => date('Y-m-d H:i:s'),
                    'lastdt' => date('Y-m-d H:i:s'),
                    'member_id' => session('member_id')
                ]);
                $appid = $db->id();
                break;
            default:
                //检查应用是否已安装
                if(!$db->has('tb_member_app', [
                    'AND' => [
                        'realid' => $opt['id'],
                        'member_id' => session('member_id')
                    ]
                ])){
                    //查找应用信息
                    $app = $db->get('tb_app', '*', ['tbid' => $opt['id']]);
                    //在安装应用表里更新一条记录
                    $db->insert('tb_member_app', [
                        'realid' => $opt['id'],
                        'name' => $app['name'],
                        'icon' => $app['icon'],
                        'type' => $app['type'],
                        'width' => $app['width'],
                        'height' => $app['height'],
                        'isresize' => $app['isresize'],
                        'isopenmax' => $app['isopenmax'],
                        'issetbar' => $app['issetbar'],
                        'isflash' => $app['isflash'],
                        'dt' => date('Y-m-d H:i:s'),
                        'lastdt' => date('Y-m-d H:i:s'),
                        'member_id' => session('member_id')
                    ]);
                    $appid = $db->id();
                    //更新使用人数
                    $db->update('tb_app', ['usecount[+]' => 1], ['tbid' => $opt['id']]);
                }
        }
        if(!empty($appid) && (int)$opt['desk'] >= 1 && (int)$opt['desk'] <= 5){
            //将安装应用表返回的id记录到用户表
            $rs = $db->get('tb_member', 'desk'.(int)$opt['desk'], ['tbid' => session('member_id')]);
            $db->update('tb_member', ['desk'.(int)$opt['desk'] => $rs == '' ? $appid : $rs.','.$appid], ['tbid' => session('member_id')]);
        }
    }
    //删除应用
    function delApp($id){
        global $db;
        $member_app = $db->get('tb_member_app', ['realid', 'type', 'folder_id'], [
            'AND' => [
                'tbid' => $id,
                'member_id' => session('member_id')
            ]
        ]);
        //如果是文件夹，则先删除文件夹内的应用
        if($member_app['type'] == 'folder'){
            foreach($db->select('tb_member_app', 'tbid', ['folder_id' => $id]) as $v){
                delApp($v);
            }
        }
        //如果是系统应用，则更新应用的安装人数
        else if($member_app['type'] == 'window' || $member_app['type'] == 'widget'){
            $db->update('tb_app', ['usecount[-]' => 1], ['tbid' => $member_app['realid']]);
        }
        //查询用户应用码头以及5个桌面的数据
        $member = $db->get('tb_member', ['dock', 'desk1', 'desk2', 'desk3', 'desk4', 'desk5'], ['tbid' => session('member_id')]);
        $data = [];
        if($member['dock'] != ''){
            $dockapp = explode(',', $member['dock']);
            foreach($dockapp as $k => $v){
                if($v == (int)$id){
                    unset($dockapp[$k]);
                    break;
                }
            }
            $data['dock'] = implode(',', $dockapp);
        }
        for($i = 1; $i <= 5; $i++){
            if($member['desk'.$i] != ''){
                $deskapp = explode(',', $member['desk'.$i]);
                foreach($deskapp as $k => $v){
                    if($v == (int)$id){
                        unset($deskapp[$k]);
                        break;
                    }
                }
                $data['desk'.$i] = implode(',', $deskapp);
            }
        }
        $db->update('tb_member', $data, ['tbid' => session('member_id')]);
        $db->delete('tb_member_app', [
            'AND' => [
                'tbid' => $id,
                'member_id' => session('member_id')
            ]
        ]);
    }
    //格式化appid字符串
    function formatAppidArray($arr){
        //去空白，如：'10,13,,17,4,6,'，格式化后：'10,13,17,4,6'
        foreach($arr as $k => $v){
            if($v == ''){
                unset($arr[$k]);
            }
        }
        //去重复
        $arr = array_unique($arr);
        return $arr;
    }
    //验证是否登入
    function checkLogin(){
        return session('?member_id') == NULL || session('member_id') == 0 ? false : true;
    }
    //验证是否为管理员
    function checkAdmin(){
        global $db;
        return $db->get('tb_member', 'type', ['tbid' => session('member_id')]) == 1 ? true : false;
    }
    //验证是否有权限
    function checkPermissions($app_id){
        global $db;
        $isHavePermissions = false;
        $permission_id = $db->get('tb_member', 'permission_id', ['tbid' => session('member_id')]);
        if($permission_id != ''){
            $apps_id = $db->get('tb_permission', 'apps_id', ['tbid' => $permission_id]);
            if($apps_id != ''){
                $apps = explode(',', $apps_id);
                if(in_array($app_id, $apps)){
                    $isHavePermissions = true;
                }
            }
        }
        return $isHavePermissions;
    }
?>
