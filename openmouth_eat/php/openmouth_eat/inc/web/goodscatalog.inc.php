<?php
global $_W,$_GPC;

$op=$_GPC['op']?$_GPC['op']:'display';

$template='goods-catalog-list';

if ($op=='post'){
	$template='goods-catalog-post';
    $id = $_GPC['id'];
    if ($id){
        $list = pdo_fetch('SELECT * FROM '.tablename('openmouth_eat_goods_catalog').' WHERE id=:id',array(':id'=>$id));
    }
    if($_W['ispost']){
		if($_GPC['menu_name'] == ""){
			message("名称不得为空");
		}
        $data['menu_name']=$_GPC['menu_name'];
		$data['description']=$_GPC['description'];
		$data['status']=$_GPC['status']?$_GPC['status']:'00';
		$data['priority']=$_GPC['priority']?$_GPC['priority']:10;
        
        if ($id){
			$data['update_time'] = TIMESTAMP;
            $res = pdo_update('openmouth_eat_goods_catalog',$data,array('id'=>$id));
            message('更新成功',$this->createWebUrl('goodscatalog',array('op'=>'display')));
        }else{
			$data['seller_id'] = $_W['uniacid'];
			$data['uniacid'] = $_W['uniacid'];
			$data['create_time'] = TIMESTAMP;
			$res = pdo_insert('openmouth_eat_goods_catalog',$data);
            message('添加成功',$this->createWebUrl('goodscatalog',array('op'=>'display')));
        }
    }
} elseif ($op=='display'){
    $page = max(1,intval($_GPC['page']));
    $pagesize = 10;

	$menu_name = $_GPC['menu_name'];

	$where = 'and 1=1';
	
	$params = array(':uniacid'=>$_W['uniacid']);

	if(!empty($menu_name)){
		$params['menu_name'] = '%'.$menu_name.'%';
		$where = $where.' and menu_name like :menu_name ';
	}

	$sql = 'SELECT * FROM '.tablename('openmouth_eat_goods_catalog').' WHERE uniacid =:uniacid '.$where.' ORDER BY priority DESC,`id` ASC LIMIT '.($page - 1) * $pagesize . "," . $pagesize;

    $lists = pdo_fetchall($sql,$params);	
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM ". tablename('openmouth_eat_goods_catalog').' WHERE uniacid =:uniacid '.$where.' ',$params );
    $pagination = pagination($total, $page,$pagesize);
} elseif ($op == 'delete'){
    $id = $_GPC['id'];
    $res = pdo_delete('openmouth_eat_goods_catalog',array('id'=>$id));
    if ($res) {
        message('删除成功',$this->createWebUrl('goodscatalog',array('op'=>'display')));
    }
}

load()->func('tpl');
include $this->template($template);
?>