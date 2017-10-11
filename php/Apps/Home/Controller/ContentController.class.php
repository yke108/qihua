<?php
namespace Home\Controller;

use Think\Controller;
use Think\Page;

class ContentController extends CommonController {

    //关于我们-联系我们
    public function contact() {
        $contentsObj = D('Contents');
        $data = [
            'fs'=> $contentsObj->getContact(1),
            'gz'=> $contentsObj->getContact(2),
            'bc'=> []
        ];
        $ret = $contentsObj->getCooperation(1);
        $data['bc'][] = [
            'title'=> 'Sales Partnership',
            'contact'=> $ret['name'],
            'telephone'=> $ret['phone'],
            'email'=> $ret['mail'],
        ];
        $ret = $contentsObj->getCooperation(2);
        $data['bc'][] = [
            'title'=> 'Purchasing Partnership',
            'contact'=> $ret['name'],
            'telephone'=> $ret['phone'],
            'email'=> $ret['mail'],
        ];
        $ret = $contentsObj->getCooperation(3);
        $data['bc'][] = [
            'title'=> 'Brand Promotion',
            'contact'=> $ret['name'],
            'telephone'=> $ret['phone'],
            'email'=> $ret['mail'],
        ];
        $ret = $contentsObj->getCooperation(4);
        $data['bc'][] = [
            'title'=> 'Investment Negotiation',
            'contact'=> $ret['name'],
            'telephone'=> $ret['phone'],
            'email'=> $ret['mail'],
        ];
        $ret = $contentsObj->getCooperation(5);
        $data['bc'][] = [
            'title'=> 'Customer Service',
            'contact'=> $ret['name'],
            'telephone'=> $ret['phone'],
            'email'=> $ret['mail'],
        ];
        $this->assign( 'data', $data );
        $this->display('about-contact');
    }

    //获取联系我们
    public function getContact() {
        $contentsObj = D('Contents');
        $data = [
            'fs'=> $contentsObj->getContact(1),
            'gz'=> $contentsObj->getContact(2),
            'bc'=> []
        ];
        $ret = $contentsObj->getCooperation(1);
        $data['bc'][] = [
            'title'=> 'Sales Partnership',
            'contact'=> $ret['name'],
            'telephone'=> $ret['phone'],
            'email'=> $ret['mail'],
        ];
        $ret = $contentsObj->getCooperation(2);
        $data['bc'][] = [
            'title'=> 'Purchasing Partnership',
            'contact'=> $ret['name'],
            'telephone'=> $ret['phone'],
            'email'=> $ret['mail'],
        ];
        $ret = $contentsObj->getCooperation(3);
        $data['bc'][] = [
            'title'=> 'Brand Promotion',
            'contact'=> $ret['name'],
            'telephone'=> $ret['phone'],
            'email'=> $ret['mail'],
        ];
        $ret = $contentsObj->getCooperation(4);
        $data['bc'][] = [
            'title'=> 'Investment Negotiation',
            'contact'=> $ret['name'],
            'telephone'=> $ret['phone'],
            'email'=> $ret['mail'],
        ];
        $ret = $contentsObj->getCooperation(5);
        $data['bc'][] = [
            'title'=> 'Customer Service',
            'contact'=> $ret['name'],
            'telephone'=> $ret['phone'],
            'email'=> $ret['mail'],
        ];
        $this->ajaxReturn(['data'=> $data]);
    }

    //关于我们--网站公告
    public function notice() {
        //取出数据
        $con = D('Contents');
        $res = $con->notice();
        $this->assign('show', $res[0]);
        $this->assign('list', $res[1]);
        $this->assign('cate', getcategory());
        $this->display('about-notice');
    }

    //网站公告详情页
    public function noticedetails() {
        if (!$_GET) exit;
        $id = I('get.id');
        $con = D('Contents');
        $res = $con->noticedetails($id);
        $this->assign('details', $res[0]);
        $this->assign('pre', $res[1]);
        $this->assign('next', $res[2]);
        $this->assign('cate', getcategory());
        $this->display('about-notice-details');
    }

    //法律声明
    public function legal() {
        // $contentsObj = D('Contents');
        // $this->assign('legal', $contentsObj->getLegalStatement());
        // $this->assign( 'cate', getcategory() );
        $this->display('about-legal');
    }

    //法律声明
    public function getLegal() {
        $contentsObj = D('Contents');
        $ret = $contentsObj->getLegalStatement();
        $data = html_entity_decode($ret['content']);
        $this->ajaxReturn(['data'=> $data]);
    }

    public function protocol() {
        $contentsObj = D('Contents');
        $ret = $contentsObj->getProtocol();
        $data = [
            'title'=> $ret['title'],
            'content'=> htmlspecialchars_decode($ret['content']),
        ];
        $this->assign('data', $data);
        $this->display('about-protocol');
    }

    public function getProtocol() {
        $contentsObj = D('Contents');
        $ret = $contentsObj->getProtocol();
        $data = [
            'title'=> $ret['title'],
            'content'=> htmlspecialchars_decode($ret['content']),
        ];
        $this->ajaxReturn(['data'=> $data]);
    }

    //获取新闻报道
    public function getNews() {
        $contentsObj = D('Contents');
        $sortParams = array(
            'get' => array('hash:aboutUs:mediaReport:*->id', 'hash:aboutUs:mediaReport:*->title',
                'hash:aboutUs:mediaReport:*->img', 'hash:aboutUs:mediaReport:*->reportDate',
                'hash:aboutUs:mediaReport:*->referer', 'hash:aboutUs:mediaReport:*->content',
            ),
            'sort' => 'desc',
            'by' => 'hash:aboutUs:mediaReport:*->id',
        );
        $rs = $contentsObj->getMediaReportList($sortParams);
        $data = empty($rs['rows']) ? [] : $rs['rows'];
        foreach ($data as $key => $value) {
            $data[$key]['content'] = subtext(strip_tags(htmlspecialchars_decode($value['content'])), 366);
            $data[$key]['url'] = U('newsdetails', array('id'=>$value['id']));
        }
        
        //分页
        $pageHtml = new Page( $rs['total'], 2 );
        $pageHtml->setConfig( 'prev', '<i class="icon-prev"></i>Previous Page' );
        $pageHtml->setConfig( 'next', 'Next Page<i class="icon-next"></i>' );
        $page_html = $pageHtml->show();
        
        $this->ajaxReturn(['data'=> $data]);
    }

    //新闻报道详情
    public function newsdetails($id = 0) {
        // if (!$_GET) exit;
        // $id = I('get.id') + 0;
        // $contentsObj = D('Contents');
        // $this->assign('news', $r = $contentsObj->getMediaReport($id));
        // $this->assign('cate', getcategory());
    	$this->assign('id', $id);
    	$this->assign('url', C('KW_DOMAIN').'content/getNewsdetail');
    	
    	$this->assign('page_title', 'News Detail');
        $this->display('about-news-details');
    }

    //获取新闻报道详情
    public function getNewsdetail() {
        $id = I('json.id', 0, 'intval');
        $contentsObj = D('Contents');
        $data = $contentsObj->getMediaReport($id);
        if (empty($data)) {
            throw new \Common\Basic\CsException("News detail does not exist", 400);
        }
        unset($data['editTime'], $data['addTime'], $data['userId']);
        $data['content'] = htmlspecialchars_decode($data['content']);
        $this->ajaxReturn(['data'=> $data]);
    }

    //平台简介
    public function description() {
        $contentsObj = D('Contents');
        $data = $contentsObj->getDescription();
        $content = htmlspecialchars_decode($data['content']);
        $this->assign( 'content', $content);
        $this->display('about-description');
    }

    //平台简介
    public function getDescription() {
        $contentsObj = D('Contents');
        $data = $contentsObj->getDescription();
        $content = htmlspecialchars_decode($data['content']);
        $this->ajaxReturn(['data'=> $content]);
    }
    
    public function terms(){$this->display();}
    public function news(){$this->display();}
    public function news_detaild(){$this->display();}
    public function about_us(){$this->display();}
    public function contact_us(){$this->display();}
}
