<?php
namespace Admin\Controller;

use Think\Controller;
use Common\Basic\CsException;

/**
 * 内容管理控制器
 */
class ContentController extends CommonController {
    // 获取平台简介、法律声明、联系我们数据
    public function aboutUs() {
        $contentsObj = D('Contents');
        $data = [];
        //平台简介
        $descriptionList = $contentsObj->getDescription();
        $data['about'] = empty($descriptionList['content']) ? '' : $descriptionList['content'];
        $data['about'] = htmlspecialchars_decode($data['about']);
        //法律声明
        $lgeal = $contentsObj->getLegalStatement();
        $data['com'] = empty($lgeal['content']) ? '' : $lgeal['content'];
        $data['com'] = htmlspecialchars_decode($data['com']);
        //佛山总公司
        $data['fs'] = $contentsObj->getContact(1);
        //广州分公司
        $data['gz'] = $contentsObj->getContact(2);
        //供应商合作
        $data['p'] = $contentsObj->getCooperation(1);
        //采购合作
        $data['b'] = $contentsObj->getCooperation(2);
        //品牌推广
        $data['e'] = $contentsObj->getCooperation(3);
        //投资洽谈
        $data['i'] = $contentsObj->getCooperation(4);
        //客户服务
        $data['k'] = $contentsObj->getCooperation(5);
        $this->ajaxReturn(['data'=> $data]);
    }

    /*
     * 平台简介&&法律声明
     * type   int  1-平台简介，2-法律声明
     * content   string 内容
     */
    public function about() {
        $type = I('json.type', 0, 'intval');
        $content = I('json.content');
        if ($type < 1 || !in_array($type, [1, 2]) || empty($content)) {
            throw new CsException('参数错误', 400);
        }

        $contentsObj = D('Contents');
        if ($type == 1) {
            $rs = $contentsObj->editDescription(['content' => $content]);
        } else {
            $rs = $contentsObj->editLegalStatement(['content' => $content]);
        }
        if ($rs) {
            $this->ajaxReturn('更新成功');
        } else {
            $this->ajaxReturn('更新失败');
        }
    }

    /*
     * 网站公告列表
     */

    public function noticeList() {
        $page = I('post.page');
        $pagesize = I('post.rows');

        $contentsObj = D('Contents');
        $sortParams = array(
            'limit' => [($page - 1) * $pagesize, $pagesize],
            'get' => array('hash:aboutUs:notice:*->id', 'hash:aboutUs:notice:*->title',
                'hash:aboutUs:notice:*->addTime', 'hash:aboutUs:notice:*->userId',
                'hash:aboutUs:notice:*->content',
            ),
            'sort' => 'desc',
            'alpha' => true,
            'by' => 'hash:aboutUs:notice:*->id',
        );
        $res = $contentsObj->getNoticeList($sortParams, 'handleNoticeData');
        $this->ajaxReturn(array('total' => empty($res['total']) ? 0 : $res['total'], 'rows' => empty($res['rows']) ? 0 : $res['rows']));
    }


    /*
     * 新增和编辑网站公告
     */
    public function notice() {
        $notice['title'] = I('post.title');
        $notice['content'] = I('post.content');
//        $notice['type'] = '网站公告';
//        $notice['createTime'] = time();
//        $contents = D('Contents');
        foreach ($notice as $key => $value) {
            if (empty($value)) {
                $mess = array('code' => 400, 'msg' => '标题和内容都不可以为空！', 'data' => '');
                $this->ajaxReturn($mess);
            }
        }
        $notice['userId'] = session('userid');
        $contentsObj = D('Contents');
        if (empty($_POST['id'])) {
            $res = $contentsObj->addNotice($notice);
        } else {
            $id = I('post.id') + 0;
            $res = $contentsObj->editNotice($id, $notice);
        }
        if ($res) {
            $mess = array('code' => 200, 'msg' => '操作成功', 'data' => '');
        } else {
            $mess = array('code' => 400, 'msg' => '操作失败', 'data' => '');
        }
        $this->ajaxReturn($mess);
    }

    /*
     * 删除网站公告，包涵批量删除和删除
     */
    public function delnotice() {
        if (IS_AJAX && IS_POST) {
            $id = I('post.id') ;
             $ids = explode(',',$id);
            foreach( $ids as $v){
                $res = D('Contents')->delNotice( $v );
            }
            if ($res) {
                $mess = array('code' => 200, 'msg' => '操作成功', 'data' => '');
            } else {
                $mess = array('code' => 400, 'msg' => '操作失败', 'data' => '');
            }
            $this->ajaxReturn($mess);
        }
    }

    /*
     * 新增和修改媒体报道
     */
    public function news() {
        $title = I('json.title');
        $content = I('json.content');
        $referer = I('json.referer');
        $reportDate = I('json.reportDate');
        $img = I('json.img');
        $id = I('json.id', 0, 'intval');
        if (empty($title) || empty($content) || empty($referer) || empty($reportDate) || empty($img)) {
            throw new CsException('参数错误', 400);            
        }
        if (strlen($img) > 1000) {
            $img = toImg($img);
        }
        $contentsObj = D('Contents');
        $new = [
            'title' => $title,
            'content' => $content,
            'referer' => $referer,
            'reportDate' => $reportDate,
            'img' => $img,
            'userId' => session('userid'),
        ];
        if ($id < 1) {
            $res = $contentsObj->addMediaReport($new);
        } else {
            $res = $contentsObj->editMediaReport($id, $new);
        }
        if (!$res) {
            throw new CsException('操作失败', 400);  
        }
        $this->ajaxReturn('操作成功');
    }

    /*
     *媒体报道列表
     */
    public function newsList() {
        $page = I('json.page', 1, 'intval');
        $rows = I('json.rows', 20, 'intval');

        $contentsObj = D('Contents');
        $sortParams = array(
            'limit' => [($page - 1) * $rows, $rows],
            'get' => array('hash:aboutUs:mediaReport:*->id', 'hash:aboutUs:mediaReport:*->title',
                    'hash:aboutUs:mediaReport:*->img', 'hash:aboutUs:mediaReport:*->reportDate',
                    'hash:aboutUs:mediaReport:*->referer', 'hash:aboutUs:mediaReport:*->userId',
                    'hash:aboutUs:mediaReport:*->content'
                    ),
            'sort' => 'desc',
            'alpha' => true,
            'by' => 'hash:aboutUs:mediaReport:*->id',
        );
        $res = $contentsObj->getMediaReportList($sortParams, 'handleMediaReportData');
        $this->ajaxReturn([
            'total' => empty($res['total']) ? 0 : $res['total'],
            'rows' => empty($res['rows']) ? 0 : $res['rows']
        ]);
    }

    /**
     * 删除媒体报道
     */
    public function delNews() {
        $id = I('json.id');
        $ids = explode( ',',$id );
        $res = false;
        foreach( $ids as $v ){
            $res = D('Contents')->delMediaReport( $v );
        }
        if (!$res) {
            throw new CsException('操作失败', 400); 
        }
        $this->ajaxReturn("删除成功");
    }

    /*
     * 合作伙伴列表
     */
    public function partnerList() {
        $model = D('Home/Partner');
        $data = $model->lists(array());
        $list = $data['lists'];
        $this->ajaxReturn(['data'=> $list]);
        // $ret = array(
        //     array(
        //         'attributes' => array(
        //             'img'  => '{"type": 1}',
        //             'type' => 1,
        //         ),
        //         'children'   => array(),
        //         'createTime' => null,
        //         'id'         => 1,
        //         'parent_id'  => 0,
        //         'path'       => 0,
        //         'text'       => '合作伙伴',
        //         'type'       => 0,
        //     ),
        // );
        // if (!empty($data['lists'])) {
        //     foreach ($data['lists'] as $v) {
        //         $new = array(
        //             'attributes' => array(
        //                 'img'  => $v['img'],
        //                 'type' => 2,
        //             ),
        //             'createTime' => $v['addTime'],
        //             'id'         => $v['id'],
        //             'parent_id'  => 1,
        //             'path'       => "1," . $v['id'],
        //             'text'       => $v['text'],
        //             'type'       => 0,
        //         );
        //         $ret[0]['children'][] = $new;
        //     }
        // }
        // echo json_encode($ret);
    }


    public function partner() {
        $this->display();
        exit;

    }

    //新增合作伙伴
    public function addpartner() {
        $id = I('json.id', 0, 'intval');
        $text = I('json.text');
        $img = I('json.img');
        if (empty($text) || empty($img)) {
            throw new CsException('标题和图标都不能为空', 400);
        }
        if (strlen($img) > 1000) {
            $img = toImg($img);
        }
        $model = D('Home/Partner');
        if (!empty($id)) {
            // 修改
            $result = $model->edit($id, ['text'=> $text, 'img'=> $img]);
        } else {
            // 添加
            $result = $model->insert(['text'=> $text, 'img'=> $img]);
        }
        if (!$result) {
            throw new CsException('操作失败', 400);
        }
        $this->ajaxReturn('操作成功');
    }

    //删除合作伙伴
    public function delpartner() {
        $id = I('json.id', 0, 'intval');
        $model = D('Home/Partner');
        $result = $model->remove($id);
        if (!$result) {
            throw new CsException('删除失败', 400);
        }
        $this->ajaxReturn('删除成功');
    }

    /*
     * 图片上传
     */
    public function upload() {
        $upload = new \Think\Upload();
        $upload->maxSize = 2097152;
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
        $upload->savePath = 'Admin/'; // 设置附件上传目录
        $upload->subName = array('date', 'Ymd');
        $upload->saveName = array('time', '');
        $info = $upload->upload();
        if (!$info) {
            throw new CsException($upload->getError(), 400);
        }
        foreach ($info as $file) {
            $filename = '/Uploads/' . $file['savepath'] . $file['savename'];
        }
        $arr = array();
        //	print_r($filename);exit;
        $arr['error'] = 0;
        $arr['url'] = $filename;
        $this->ajaxReturn($arr);
    }

    /*
     * 文件上传
     */
    public function uploadFile() {
        $upload = new \Think\Upload();
        $upload->maxSize = 2097152;
        $upload->savePath = 'Admin/'; // 设置附件上传目录
        $upload->subName = array('date', 'Ymd');
        $upload->saveName = array('time', '');
        $info = $upload->upload();
        if (!$info) {
            throw new CsException($upload->getError(), 400);
        }
        foreach ($info as $file) {
            $filename = '/Uploads/' . $file['savepath'] . $file['savename'];
        }
        $this->ajaxReturn(['url'=> $filename]);
    }

     /*
     * 文件上传
     */
    public function editorUpload() {
        $upload = new \Think\Upload();
        $upload->maxSize = 2097152;
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
        $upload->savePath = 'Admin/'; // 设置附件上传目录
        $upload->subName = array('date', 'Ymd');
        $upload->saveName = array('time', '');
        $info = $upload->upload();
        if (!$info) {
            echo json_encode(['state'=>'请求出错']);
            exit;
        }
        foreach ($info as $file) {
            $filename = '/Uploads/' . $file['savepath'] . $file['savename'];
        }
        echo json_encode([
            'state' => 'SUCCESS',
            'url' => $filename,
            'title' => $file['savename'],
            'original' => $file['name'],
            'type' => $file['type'],
            'size' => $file['size'],
        ]);
        exit;
    }

    /*
     * 用户服务协议
     */
    public function getProtocol() {
        $contentsObj = D('Contents');
        $res = $contentsObj->getProtocol();
        $res['content'] = htmlspecialchars_decode($res['content']);
        $this->ajaxReturn(['data'=> $res]);
    }

    /*
     * 编辑用户服务协议
     */
    public function protocol() {
        $contentsObj = D('Contents');
        $title = I('json.title');
        $content = I('json.content');
        if (empty($title) || empty($content)) {
            throw new CsException('标题和内容都不可以为空！', 400);
        }
        $res = $contentsObj->editProtocol(['title'=> $title, 'content'=> $content]);
        if (!$res) {
            throw new CsException('保存失败！', 400);
        }
        $this->ajaxReturn("保存成功");
    }


    //帮助中心
    public function help() {
        $this->display();
    }

    //帮助中心列表
    public function helpList() {
        if (!$_POST['id']) $id = 0;
        $id = I('post.id');
        $partner = D('Partner');
        $res = $partner->gethelp($id);
        echo json_encode($res);
    }

    //新增帮助中心
    public function addhelp() {
        if (!$_POST) exit;
        $data = I('post.');
        $data['createTime'] = time();
        if (empty($data['text'])) $this->ajaxReturn(array('code' => 400, 'msg' => '标题不能为空！', 'data' => ''));

        //if(empty($data['content']))$this->ajaxReturn(array('code'=>400,'msg'=>'内容不能为空！','data'=>''));
        $partner = D('Partner');
        $this->ajaxReturn($partner->addhelp($data));
    }

    //联系我们
    public function contact() {
        $address = I('json.address');
        $companyName = I('json.companyName');
        $tel = I('json.tel');
        $serviceTel = I('json.serviceTel');
        $email = I('json.email');
        $fax = I('json.fax');
        $type = I('json.type', 1, 'intval');
        if (empty($address) || empty($companyName) || empty($tel) || empty($serviceTel) || empty($email) || empty($fax) || !in_array($type, [1,2])) {
            throw new CsException('参数错误', 400);  
        }
        $contentsObj = D('Contents');
        $res = $contentsObj->editContact([
            'address' => $address,
            'companyName' => $companyName,
            'tel' => $tel,
            'serviceTel' => $serviceTel,
            'email' => $email,
            'fax' => $fax,
        ], $type);
        if (!$res) {
            throw new CsException('保存失败！', 400);  
        }
        $this->ajaxReturn('保存成功！');
    }

    //商务合作
    public function cooperate() {
        $contentsObj = D('Contents');
        //供应商合作
//		$rec['title']='供应商合作';
        $rec['phone'] = I('json.providephone');
        $rec['mail'] = I('json.providemail');
        $rec['name'] = I('json.provider');
        $rec['qq'] = I('json.provideqq');
        if (empty($rec['phone']) || empty($rec['mail']) || empty($rec['name']) || empty($rec['qq'])) {
            throw new CsException('销售合作参数不完整', 400);  
        }
        $contentsObj->editCooperation($rec, 1);
        //采购合作
//		$rec['title']='采购合作';
        $rec['phone'] = I('json.buyphone');
        $rec['mail'] = I('json.buymail');
        $rec['name'] = I('json.buyer');
        $rec['qq'] = I('json.buyqq');
        if (empty($rec['phone']) || empty($rec['mail']) || empty($rec['name']) || empty($rec['qq'])) {
            throw new CsException('采购合作参数不完整', 400);  
        }
        $contentsObj->editCooperation($rec, 2);
        //品牌推广
//		$rec['title']='品牌推广';
        $rec['phone'] = I('json.extendphone');
        $rec['mail'] = I('json.extendmail');
        $rec['name'] = I('json.extender');
        $rec['qq'] = I('json.extendqq');
        if (empty($rec['phone']) || empty($rec['mail']) || empty($rec['name']) || empty($rec['qq'])) {
            throw new CsException('品牌推广参数不完整', 400);  
        }
        $contentsObj->editCooperation($rec, 3);

        //投资洽谈
//		$rec['title']='投资洽谈';
        $rec['phone'] = I('json.investphone');
        $rec['mail'] = I('json.investmail');
        $rec['name'] = I('json.invest');
        $rec['qq'] = I('json.investqq');
        if (empty($rec['phone']) || empty($rec['mail']) || empty($rec['name']) || empty($rec['qq'])) {
            throw new CsException('投资洽谈参数不完整', 400);  
        }
        $contentsObj->editCooperation($rec, 4);

        //客户服务
//		$rec['title']='客户服务';
        $rec['phone'] = I('json.kefuphone');
        $rec['mail'] = I('json.kefumail');
        $rec['name'] = I('json.kefu');
        $rec['qq'] = I('json.kefuqq');
        if (empty($rec['phone']) || empty($rec['mail']) || empty($rec['name']) || empty($rec['qq'])) {
            throw new CsException('客户服务参数不完整', 400);  
        }
        $res = $contentsObj->editCooperation($rec, 5);

        $this->ajaxReturn('保存成功！');
    }

    //联系我们列表
    protected function contactList() {
        $contentsObj = D('Contents');
        //佛山总公司
        $this->assign('fs', $contentsObj->getContact(1));
        //广州分公司
        $this->assign('gz', $contentsObj->getContact(2));
        //供应商合作
        $this->assign('p', $contentsObj->getCooperation(1));
        //采购合作
        $this->assign('b', $contentsObj->getCooperation(2));
        //品牌推广
        $this->assign('e', $contentsObj->getCooperation(3));
        //投资洽谈
        $this->assign('i', $contentsObj->getCooperation(4));
        //客户服务
        $this->assign('k', $contentsObj->getCooperation(5));
        //投诉建议
    }
}