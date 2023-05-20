<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Db;
use think\Request;
use think\db\Expression;

/**
 * 首页接口
 */
class Index extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 首页
     *
     */
    public function huanopenid($code)
    {


        $request_https = 'https://api.weixin.qq.com/sns/jscode2session?appid=wxcbf33d0e03b886f6&secret=c22c620bd782b9a4095583d57fad83b5&js_code=' . $code . '&grant_type=authorization_code';
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $request_https);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($ch);

        curl_close($ch);

        $jsoninfo = $output;
        echo json_decode($jsoninfo)->openid;
    }
    public function wenzi()
    {
        echo json_encode(Db::name('wenzi')->select());
    }

    public function check1($key, $openid)
    {




        if ($key) {
            $opid =  Db::name('yonghu')->where('cdkey', $key)->select();

            if ($opid) {
                if ($openid == $opid[0]['openid']) {
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://cdkey.24servercool.com/api/key/check',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => array('key' => $key),

                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);
                    echo $response;
                } else {
                    echo ("已绑定其他微信号");
                    exit;
                }
            } else {

                $op = Db::name('yonghu')->where('openid', $openid)->select();

                if ($op) {
                    $data = ['cdkey' => $key];
                    Db::name('yonghu')->where('openid', $openid)->update($data);
                } else {
                    $data = ['openid' => $openid];
                    Db::name('yonghu')->insert($data);
                    $data = ['cdkey' => $key];
                    Db::name('yonghu')->where('openid', $openid)->update($data);
                }

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://cdkey.24servercool.com/api/key/check',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array('key' => $key),

                ));

                $response = curl_exec($curl);

                curl_close($curl);
                echo $response;
            }
        }
    }
    public function check($key)
    {


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://cdkey.24servercool.com/api/key/check',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('key' => $key),

        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }
    public function guoqi($key)
    {


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://cdkey.24servercool.com/api/key/expired',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('key' => $key),

        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }
    public function dianpuall()
    {
        $fuzhuang = Db::name('fuzhuang')->distinct(true)->field('store')->select();
        $xie = Db::name('xie')->distinct(true)->field('store')->select();
        $peijian = Db::name('peijian')->distinct(true)->field('store')->select();
        $k = array_merge_recursive($fuzhuang, $xie, $peijian);
        $result = array_unique($k, SORT_REGULAR);
        echo  json_encode($result);
    }


    public function dianpu(Request $request)
    {
        $shuzu = $request->param('shuzu'); // 可获取get和post类型参数
        $shuzu = stripslashes(html_entity_decode($shuzu));
        $shuzu = json_decode($shuzu, true);
        $page = $request->param('page');
        $peijian = Db::name('peijian')->field('*,sum(shuliang) as dpshuliang')->where('store', 'in', $shuzu)->group("sku,store")->paginate(10, false, ['page' => $page])->toArray();
        $xie = Db::name('xie')->field('*,sum(shuliang) as dpshuliang')->where('store', 'in', $shuzu)->group("sku,store")->paginate(10, false, ['page' => $page])->toArray();
        $fuzhuang = Db::name('fuzhuang')->field('*,sum(shuliang) as dpshuliang')->where('store', 'in', $shuzu)->group("sku,store")->paginate(10, false, ['page' => $page])->toArray();

        echo  json_encode(array_merge_recursive($fuzhuang, $xie, $peijian));
    }

    public function index()
    {
        $this->success('请求成功');
    }
    public function quanguokucun($sku, $category)
    {
        if ($category == 10) {
            echo  Db::name('fuzhuang')->where('sku', $sku)->sum('shuliang');
        }
        if ($category == 20) {
            echo  Db::name('xie')->where('sku', $sku)->sum('shuliang');
        }

        if ($category == 30) {
            echo  Db::name('peijian')->where('sku', $sku)->sum('shuliang');
        }
    }
    public function kaizhe()
    {
        echo json_encode(Db::name("kaizhe")->select());
    }
    public function chima($sku, $category, $store)
    {
        // 构建 where 条件
        $where = array();
        if ($store) {
            $where['store'] = $store;
        }
        if ($sku) {
            $where['sku'] = $sku;
        }
        if ($category == 10) {
            echo json_encode(Db::name('fuzhuang')->where($where)->select());
        }
        if ($category == 20) {
            echo json_encode(Db::name('xie')->where($where)->select());
        }

        if ($category == 30) {
            echo json_encode(Db::name('peijian')->where($where)->select());
        }
    }
    public function youhui($sku)
    {
        $youhui = Db::name('youhui')->where('sku', $sku)->select();
        echo json_encode($youhui);
    }
    public function detail($sku, $category, $store)
    {
        if ($category == 10) {
            $k = Db::name('fuzhuang')->where('sku', $sku)->where('store', $store)->select();
            echo json_encode($k);
        }
        if ($category == 20) {
            $k =  Db::name('xie')->where('sku', $sku)->where('store', $store)->select();
            echo json_encode($k);
        }

        if ($category == 30) {
            $k =  Db::name('peijian')->where('sku', $sku)->where('store', $store)->select();
            echo json_encode($k);
        }
    }



    public function quanbu($page, $sku, $fuzhuang = 'true', $xie = 'true', $peijian = 'true', $youhui = 'true', $shuzu = [], $kcls = 3, $jg = 3)
    {
        
        $fuzhuang1 = [];
        $xie1 = [];
        $peijian1 = [];
        if ($shuzu) {
            $shuzu = stripslashes(html_entity_decode($shuzu));
            $shuzu = json_decode($shuzu, true);
        }

        // $c=[];

        // for ($i = 0; $i < count($shuzu); $i++) {
        //      array_push($c,$shuzu[$i]['store']);
        // }
        // $shuzu=$c;


        $kcls = (int)$kcls;
        $jg = (int)$jg;

        if (strlen($sku)) {
            
            if ($fuzhuang == 'true') {
                if ($youhui == 'true') {
                    if (count($shuzu) == 0) {

                        if ($kcls == 1) {
                            $fuzhuang1 = Db::name('fuzhuang')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')->where('a.sku', $sku)->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $fuzhuang1 = Db::name('fuzhuang')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')->where('a.sku', $sku)->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {
                            $fuzhuang1 = Db::name('fuzhuang')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')
                                // ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->group("a.store,a.sku")
                                ->order('a.xianjia', 'desc')
                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } elseif ($jg == 2) {
                            $fuzhuang1 = Db::name('fuzhuang')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')
                                // ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->group("a.store,a.sku")
                                ->order('a.xianjia', 'asc')
                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } else {

                            $fuzhuang1 = Db::name('fuzhuang')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku')->where('a.sku', $sku)->field('a.*,sum(shuliang) as dpshuliang')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    } else {


                        if ($kcls == 1) {
                            $fuzhuang1 = Db::name('fuzhuang')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')->where('a.store', 'in', $shuzu)->where('a.sku', $sku)->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $fuzhuang1 = Db::name('fuzhuang')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')->where('a.store', 'in', $shuzu)->where('a.sku', $sku)->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {
                            $fuzhuang1 = Db::name('fuzhuang')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')
                                ->where('a.store', 'in', $shuzu)
                                // ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->group("a.store,a.sku")
                                ->order('a.xianjia', 'desc')
                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } elseif ($jg == 2) {
                            $fuzhuang1 = Db::name('fuzhuang')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')
                                ->where('a.store', 'in', $shuzu)
                                // ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->group("a.store,a.sku")
                                ->order('a.xianjia', 'asc')
                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } else {


                            $fuzhuang1 = Db::name('fuzhuang')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku')->where('a.store', 'in', $shuzu)->where('a.sku', $sku)->field('a.*,sum(shuliang) as dpshuliang')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    }
                } else {
                    if (count($shuzu) == 0) {


                        if ($kcls == 1) {
                            $fuzhuang1 = Db::name('fuzhuang')->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $fuzhuang1 = Db::name('fuzhuang')->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {
                            $fuzhuang1 = Db::name('fuzhuang')->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 2) {
                            $fuzhuang1 = Db::name('fuzhuang')->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } else {
                            $fuzhuang1 = Db::name('fuzhuang')->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    } else {



                        if ($kcls == 1) {
                            $fuzhuang1 = Db::name('fuzhuang')->where('store', 'in', $shuzu)->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $fuzhuang1 = Db::name('fuzhuang')->where('store', 'in', $shuzu)->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {
                            $fuzhuang1 = Db::name('fuzhuang')->where('store', 'in', $shuzu)->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 2) {
                            $fuzhuang1 = Db::name('fuzhuang')->where('store', 'in', $shuzu)->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } else {
                            $fuzhuang1 = Db::name('fuzhuang')->where('store', 'in', $shuzu)->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    }
                }
            }
            if ($xie == 'true') {
                if ($youhui == 'true') {
                    if (count($shuzu) == 0) {

                        if ($kcls == 1) {
                            $xie1 = Db::name('xie')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')->where('a.sku', $sku)->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $xie1 = Db::name('xie')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')->where('a.sku', $sku)->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {
                            $xie1 = Db::name('xie')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')
                                // ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->group("a.store,a.sku")
                                ->order('a.xianjia', 'desc')
                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } elseif ($jg == 2) {
                            $xie1 = Db::name('xie')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')
                                // ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->group("a.store,a.sku")
                                ->order('a.xianjia', 'asc')
                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } else {
                            $xie1 = Db::name('xie')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')->where('a.sku', $sku)->field('a.*,sum(shuliang) as dpshuliang')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    } else {

                        if ($kcls == 1) {
                            $xie1 = Db::name('xie')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')->where('a.store', 'in', $shuzu)->where('a.sku', $sku)->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $xie1 = Db::name('xie')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')->where('a.store', 'in', $shuzu)->where('a.sku', $sku)->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {
                            $xie1 = Db::name('xie')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')
                                ->where('a.store', 'in', $shuzu)
                                // ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->group("a.store,a.sku")
                                ->order('a.xianjia', 'desc')
                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } elseif ($jg == 2) {
                            $xie1 = Db::name('xie')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')
                                ->where('a.store', 'in', $shuzu)
                                // ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->group("a.store,a.sku")
                                ->order('a.xianjia', 'asc')
                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } else {

                            $xie1 = Db::name('xie')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')->where('a.store', 'in', $shuzu)->where('a.sku', $sku)->field('a.*,sum(shuliang) as dpshuliang')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    }
                } else {
                    if (count($shuzu) == 0) {


                        if ($kcls == 1) {
                            $xie1 = Db::name('xie')->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $xie1 = Db::name('xie')->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {
                            $xie1 = Db::name('xie')->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 2) {
                            $xie1 = Db::name('xie')->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } else {
                            $xie1 = Db::name('xie')->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    } else {



                        if ($kcls == 1) {
                            $xie1 = Db::name('xie')->where('store', 'in', $shuzu)->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $xie1 = Db::name('xie')->where('store', 'in', $shuzu)->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {
                            $xie1 = Db::name('xie')->where('store', 'in', $shuzu)->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 2) {
                            $xie1 = Db::name('xie')->where('store', 'in', $shuzu)->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } else {
                            $xie1 = Db::name('xie')->where('store', 'in', $shuzu)->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    }
                }
            }

            if ($peijian == 'true') {
                if ($youhui == 'true') {
                    if (count($shuzu) == 0) {

                        if ($kcls == 1) {
                            $peijian1 = Db::name('peijian')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')->where('a.sku', $sku)->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $peijian1 = Db::name('peijian')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')->where('a.sku', $sku)->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {
                            $peijian1 = Db::name('peijian')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')
                                // ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->group("a.store,a.sku")
                                ->order('a.xianjia', 'desc')
                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } elseif ($jg == 2) {
                            $peijian1 = Db::name('peijian')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')
                                // ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->group("a.store,a.sku")
                                ->order('a.xianjia', 'asc')
                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } else {
                            $peijian1 = Db::name('peijian')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')->where('a.sku', $sku)->field('a.*,sum(shuliang) as dpshuliang')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    } else {

                        if ($kcls == 1) {
                            $peijian1 = Db::name('peijian')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')->where('a.store', 'in', $shuzu)->where('a.sku', $sku)->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $peijian1 = Db::name('peijian')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')->where('a.store', 'in', $shuzu)->where('a.sku', $sku)->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {
                            $peijian1 = Db::name('peijian')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')
                                ->where('a.store', 'in', $shuzu)
                                // ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->group("a.store,a.sku")
                                ->order('a.xianjia', 'desc')
                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } elseif ($jg == 2) {
                            $peijian1 = Db::name('peijian')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')
                                ->where('a.store', 'in', $shuzu)
                                // ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->group("a.store,a.sku")
                                ->order('a.xianjia', 'asc')
                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } else {

                            $peijian1 = Db::name('peijian')->alias('a')
                                ->join('youhui w', 'a.sku = w.sku', 'LEFT')->where('a.store', 'in', $shuzu)->where('a.sku', $sku)->field('a.*,sum(shuliang) as dpshuliang')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    }
                } else {
                    if (count($shuzu) == 0) {


                        if ($kcls == 1) {
                            $peijian1 = Db::name('peijian')->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $peijian1 = Db::name('peijian')->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {
                            $peijian1 = Db::name('peijian')->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 2) {
                            $peijian1 = Db::name('peijian')->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } else {
                            $peijian1 = Db::name('peijian')->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    } else {



                        if ($kcls == 1) {
                            $peijian1 = Db::name('peijian')->where('store', 'in', $shuzu)->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $peijian1 = Db::name('peijian')->where('store', 'in', $shuzu)->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {
                            $peijian1 = Db::name('peijian')->where('store', 'in', $shuzu)->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 2) {
                            $peijian1 = Db::name('peijian')->where('store', 'in', $shuzu)->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } else {
                            $peijian1 = Db::name('peijian')->where('store', 'in', $shuzu)->where('sku', $sku)->field('*,sum(shuliang) as dpshuliang')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    }
                }
            }


            echo  json_encode(array_merge_recursive($fuzhuang1, $xie1, $peijian1));
        } else {

            //         if($fuzhuang=='true'){

            //             if($youhui=='true'){
            //     if(count($shuzu)==0){






            //          $fuzhuang1=Db::name('fuzhuang')->alias('a')
            // ->join('youhui w','a.sku = w.sku','LEFT') ->field('a.*,sum(shuliang) as dpshuliang')->group("a.store,a.sku")->paginate(10,true,['page'=>$page])->toArray();
            //     }else{
            //              $fuzhuang1=Db::name('fuzhuang')->alias('a')
            // ->join('youhui w','a.sku = w.sku','LEFT')->where('store','in',$shuzu)->field('a.*,sum(shuliang) as dpshuliang')->group("a.store,a.sku")->paginate(10,true,['page'=>$page])->toArray();
            //     }


            //             }else{
            //                   if(count($shuzu)==0){
            //                  $fuzhuang1=Db::name('fuzhuang')->field('*,sum(shuliang) as dpshuliang')->group("store,sku")->paginate(10,true,['page'=>$page])->toArray();
            //                   }else{
            //                          $fuzhuang1=Db::name('fuzhuang')->field('*,sum(shuliang) as dpshuliang')->where('store','in',$shuzu)->group("store,sku")->paginate(10,true,['page'=>$page])->toArray();
            //                   }


            //             }


            //         }
            //         if($xie=='true'){
            //             if($youhui=='true'){
            //   if(count($shuzu)==0){
            //               $xie1=Db::name('xie')->alias('a')
            // ->join('youhui w','a.sku = w.sku','LEFT')  ->field('a.*,sum(shuliang) as dpshuliang')->group("a.store,a.sku")->paginate(10,true,['page'=>$page])->toArray();
            // }else{
            //     $xie1=Db::name('xie')->alias('a')
            // ->join('youhui w','a.sku = w.sku','LEFT') ->field('a.*,sum(shuliang) as dpshuliang')->where('a.store','in',$shuzu)->group("a.store,a.sku")->paginate(10,true,['page'=>$page])->toArray();
            // }
            //             }else{
            //       if(count($shuzu)==0){
            //             $xie1=Db::name('xie')->field('*,sum(shuliang) as dpshuliang')->group("store,sku")->paginate(10,true,['page'=>$page])->toArray();
            //       }else{
            //             $xie1=Db::name('xie')->field('*,sum(shuliang) as dpshuliang')->where('store','in',$shuzu)->group("store,sku")->paginate(10,true,['page'=>$page])->toArray(); 
            //       }
            //         }


            //         }
            //       if($peijian=='true'){
            //     if($youhui=='true'){
            //       if(count($shuzu)==0){
            //               $peijian1=Db::name('peijian')->alias('a')
            // ->join('youhui w','a.sku = w.sku','LEFT') ->field('a.*,sum(shuliang) as dpshuliang')->group("a.store,a.sku")->paginate(10,true,['page'=>$page])->toArray();
            // }else {
            //       $peijian1=Db::name('peijian')->alias('a')
            // ->join('youhui w','a.sku = w.sku','LEFT') ->field('a.*,sum(shuliang) as dpshuliang')->where('a.store','in',$shuzu)->group("a.store,a.sku")->paginate(10,true,['page'=>$page])->toArray();
            // }
            //             }else{
            //                   if(count($shuzu)==0){
            //   $peijian1=Db::name('peijian')->field('*,sum(shuliang) as dpshuliang')->group("store,sku")->paginate(10,true,['page'=>$page])->toArray(); }else{
            //           $peijian1=Db::name('peijian')->field('*,sum(shuliang) as dpshuliang')->where('store','in',$shuzu)->group("store,sku")->paginate(10,true,['page'=>$page])->toArray(); 
            //   }
            // }


            // }
            if ($fuzhuang == 'true') {
                if ($youhui == 'true') {
                    if (count($shuzu) == 0) {

                        if ($kcls == 1) {
                            $fuzhuang1 =  Db::name('youhui')->alias('w')
                            ->join('fuzhuang a', 'a.sku = w.sku', 'LEFT')->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $fuzhuang1 =  Db::name('youhui')->alias('w')
                            ->join('fuzhuang a', 'a.sku = w.sku', 'LEFT')->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {

                            $fuzhuang1 = Db::name('youhui')->alias('w')
                            ->join('fuzhuang a', 'a.sku = w.sku', 'LEFT')
                                //   ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->group("a.store,a.sku")
                                ->order('a.xianjia', 'desc')
                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } elseif ($jg == 2) {
                            $fuzhuang1 =  Db::name('youhui')->alias('w')
                            ->join('fuzhuang a', 'a.sku = w.sku', 'LEFT')
                                //   ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->group("a.store,a.sku")
                                ->order('a.xianjia', 'asc')
                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } else {
                            $fuzhuang1 =  Db::name('youhui')->alias('w')
                            ->join('fuzhuang a', 'a.sku = w.sku', 'LEFT')->field('a.*,sum(shuliang) as dpshuliang')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    } else {

                        if ($kcls == 1) {
                            $fuzhuang1 = Db::name('youhui')->alias('w')
                            ->join('fuzhuang a', 'a.sku = w.sku', 'LEFT')->where('a.store', 'in', $shuzu)->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $fuzhuang1 =  Db::name('youhui')->alias('w')
                            ->join('fuzhuang a', 'a.sku = w.sku', 'LEFT')->where('a.store', 'in', $shuzu)->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {

                            $fuzhuang1 =  Db::name('youhui')->alias('w')
                            ->join('fuzhuang a', 'a.sku = w.sku', 'LEFT')
                                ->where('a.store', 'in', $shuzu)
                                //   ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->order('a.xianjia', 'desc')
                                ->group("a.store,a.sku")

                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } elseif ($jg == 2) {
                            $fuzhuang1 = Db::name('youhui')->alias('w')
                            ->join('fuzhuang a', 'a.sku = w.sku', 'LEFT')
                                ->where('a.store', 'in', $shuzu)
                                //   ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->order('a.xianjia', 'asc')
                                ->group("a.store,a.sku")

                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } else {



                            $fuzhuang1 =  Db::name('youhui')->alias('w')
                            ->join('fuzhuang a', 'a.sku = w.sku', 'LEFT')->where('a.store', 'in', $shuzu)->field('a.*,sum(shuliang) as dpshuliang')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    }
                } else {
                    if (count($shuzu) == 0) {


                        if ($kcls == 1) {
                            $fuzhuang1 = Db::name('fuzhuang')->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $fuzhuang1 = Db::name('fuzhuang')->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {
                            $fuzhuang1 = Db::name('fuzhuang')->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 2) {
                            $fuzhuang1 = Db::name('fuzhuang')->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } else {
                            $fuzhuang1 = Db::name('fuzhuang')->field('*,sum(shuliang) as dpshuliang')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    } else {



                        if ($kcls == 1) {
                            $fuzhuang1 = Db::name('fuzhuang')->where('store', 'in', $shuzu)->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $fuzhuang1 = Db::name('fuzhuang')->where('store', 'in', $shuzu)->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {

                            $fuzhuang1 = Db::name('fuzhuang')->where('store', 'in', $shuzu)->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 2) {
                            $fuzhuang1 = Db::name('fuzhuang')->where('store', 'in', $shuzu)->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } else {
                            $fuzhuang1 = Db::name('fuzhuang')->where('store', 'in', $shuzu)->field('*,sum(shuliang) as dpshuliang')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    }
                }
            }
            if ($xie == 'true') {
                if ($youhui == 'true') {
                    if (count($shuzu) == 0) {

                        if ($kcls == 1) {
                            $xie1 =  Db::name('youhui')->alias('w')
                            ->join('xie a', 'a.sku = w.sku', 'LEFT')->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $xie1 =  Db::name('youhui')->alias('w')
                            ->join('xie a', 'a.sku = w.sku', 'LEFT')->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {

                            $xie1 =  Db::name('youhui')->alias('w')
                            ->join('xie a', 'a.sku = w.sku', 'LEFT')
                                //   ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->group("a.store,a.sku")
                                ->order('a.xianjia', 'desc')
                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } elseif ($jg == 2) {
                            $xie1 =  Db::name('youhui')->alias('w')
                            ->join('xie a', 'a.sku = w.sku', 'LEFT')
                                //   ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->group("a.store,a.sku")
                                ->order('a.xianjia', 'asc')
                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } else {
                            $xie1 =  Db::name('youhui')->alias('w')
                            ->join('xie a', 'a.sku = w.sku', 'LEFT')->field('a.*,sum(shuliang) as dpshuliang')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    } else {

                        if ($kcls == 1) {
                            $xie1 =  Db::name('youhui')->alias('w')
                            ->join('xie a', 'a.sku = w.sku', 'LEFT')->where('a.store', 'in', $shuzu)->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $xie1 =  Db::name('youhui')->alias('w')
                            ->join('xie a', 'a.sku = w.sku', 'LEFT')->where('a.store', 'in', $shuzu)->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {
                            $xie1 =  Db::name('youhui')->alias('w')
                            ->join('xie a', 'a.sku = w.sku', 'LEFT')
                                ->where('a.store', 'in', $shuzu)
                                //   ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->group("a.store,a.sku")
                                ->order('a.xianjia', 'desc')
                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } elseif ($jg == 2) {
                            $xie1 =  Db::name('youhui')->alias('w')
                            ->join('xie a', 'a.sku = w.sku', 'LEFT')
                                ->where('a.store', 'in', $shuzu)
                                //   ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->group("a.store,a.sku")
                                ->order('a.xianjia', 'asc')
                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } else {

                            $xie1 =  Db::name('youhui')->alias('w')
                            ->join('xie a', 'a.sku = w.sku', 'LEFT')->where('a.store', 'in', $shuzu)->field('a.*,sum(shuliang) as dpshuliang')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    }
                } else {
                    if (count($shuzu) == 0) {


                        if ($kcls == 1) {
                            $xie1 = Db::name('xie')->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $xie1 = Db::name('xie')->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {
                            $xie1 = Db::name('xie')->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 2) {
                            $xie1 = Db::name('xie')->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } else {
                            $xie1 = Db::name('xie')->field('*,sum(shuliang) as dpshuliang')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    } else {



                        if ($kcls == 1) {
                            $xie1 = Db::name('xie')->where('store', 'in', $shuzu)->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $xie1 = Db::name('xie')->where('store', 'in', $shuzu)->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {
                            $xie1 = Db::name('xie')->where('store', 'in', $shuzu)->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 2) {
                            $xie1 = Db::name('xie')->where('store', 'in', $shuzu)->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } else {
                            $xie1 = Db::name('xie')->where('store', 'in', $shuzu)->field('*,sum(shuliang) as dpshuliang')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    }
                }
            }

            if ($peijian == 'true') {
                if ($youhui == 'true') {
                    if (count($shuzu) == 0) {

                        if ($kcls == 1) {
                            $peijian1 =  Db::name('youhui')->alias('w')
                            ->join('peijian a', 'a.sku = w.sku', 'LEFT')->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $peijian1 =  Db::name('youhui')->alias('w')
                            ->join('peijian a', 'a.sku = w.sku', 'LEFT')->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {
                            $peijian1 =  Db::name('youhui')->alias('w')
                            ->join('peijian a', 'a.sku = w.sku', 'LEFT')
                                //   ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->group("a.store,a.sku")
                                ->order('a.xianjia', 'desc')
                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } elseif ($jg == 2) {
                            $peijian1 = Db::name('youhui')->alias('w')
                            ->join('peijian a', 'a.sku = w.sku', 'LEFT')
                                //   ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->group("a.store,a.sku")
                                ->order('a.xianjia', 'asc')
                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } else {
                            $peijian1 =  Db::name('youhui')->alias('w')
                            ->join('peijian a', 'a.sku = w.sku', 'LEFT')->field('a.*,sum(shuliang) as dpshuliang')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    } else {

                        if ($kcls == 1) {
                            $peijian1 = Db::name('youhui')->alias('w')
                                ->join('peijian a', 'a.sku = w.sku', 'LEFT')->where('a.store', 'in', $shuzu)->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $peijian1 = Db::name('youhui')->alias('w')
                                ->join('peijian a', 'a.sku = w.sku', 'LEFT')
                                ->where('a.store', 'in', $shuzu)
                                ->field('a.*,sum(shuliang) as dpshuliang')
                                ->order('dpshuliang', 'asc')
                                ->group("a.store,a.sku")
                                ->paginate(10, true, ['page' => $page])->toArray();
                            // $peijian1 = Db::name('peijian')->alias('a')
                            //     ->join('youhui w', 'a.sku = w.sku', 'LEFT')->where('a.store', 'in', $shuzu)->field('a.*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {
                            $peijian1 = Db::name('youhui')->alias('w')
                                ->join('peijian a', 'a.sku = w.sku', 'LEFT')
                                ->where('a.store', 'in', $shuzu)
                                //   ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->group("a.store,a.sku")
                                ->order('a.xianjia', 'desc')
                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } elseif ($jg == 2) {
                            $peijian1 = Db::name('youhui')->alias('w')
                                ->join('peijian a', 'a.sku = w.sku', 'LEFT')
                                ->where('a.store', 'in', $shuzu)
                                //   ->where('a.sku', $sku)
                                ->field('a.*, sum(shuliang) as dpshuliang')
                                ->group("a.store,a.sku")
                                ->order('a.xianjia', 'asc')
                                ->paginate(10, true, ['page' => $page])
                                ->toArray();
                        } else {

                            $peijian1 = Db::name('youhui')->alias('w')
                                ->join('peijian a', 'a.sku = w.sku', 'LEFT')->where('a.store', 'in', $shuzu)->field('a.*,sum(shuliang) as dpshuliang')->group("a.store,a.sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    }
                } else {
                    if (count($shuzu) == 0) {


                        if ($kcls == 1) {
                            $peijian1 = Db::name('peijian')->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $peijian1 = Db::name('peijian')->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {
                            $peijian1 = Db::name('peijian')->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 2) {
                            $peijian1 = Db::name('peijian')->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } else {
                            $peijian1 = Db::name('peijian')->field('*,sum(shuliang) as dpshuliang')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    } else {



                        if ($kcls == 1) {
                            $peijian1 = Db::name('peijian')->where('store', 'in', $shuzu)->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($kcls == 2) {
                            $peijian1 = Db::name('peijian')->where('store', 'in', $shuzu)->field('*,sum(shuliang) as dpshuliang')->order('dpshuliang', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 1) {
                            $peijian1 = Db::name('peijian')->where('store', 'in', $shuzu)->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'desc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } elseif ($jg == 2) {
                            $peijian1 = Db::name('peijian')->where('store', 'in', $shuzu)->field('*,sum(shuliang) as dpshuliang')->order('xianjia', 'asc')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        } else {
                            $peijian1 = Db::name('peijian')->where('store', 'in', $shuzu)->field('*,sum(shuliang) as dpshuliang')->group("store,sku")->paginate(10, true, ['page' => $page])->toArray();
                        }
                    }
                }
            }

            echo  json_encode(array_merge_recursive($fuzhuang1, $xie1, $peijian1));
        }
    }
}
//           if($xie=='true'){
//              if($youhui=='true'){
//                   if(count($shuzu)==0){
//               $xie1=Db::name('xie')->alias('a')
// ->join('youhui w','a.sku = w.sku')->where('a.sku',$sku) ->field('a.*,sum(shuliang) as dpshuliang')->group("a.store,a.sku")->paginate(10,true,['page'=>$page])->toArray(); 
// }else
// {
//         $xie1=Db::name('xie')->alias('a')
// ->join('youhui w','a.sku = w.sku')->where('a.store','in',$shuzu)->where('a.sku',$sku) ->field('a.*,sum(shuliang) as dpshuliang')->group("a.store,a.sku")->paginate(10,true,['page'=>$page])->toArray(); 
// }
//             }else{
                
                
//                 if(count($shuzu)==0){
//                      $xie1=Db::name('xie')->where('sku',$sku)->field('*,sum(shuliang) as dpshuliang')->group("store,sku")->paginate(10,true,['page'=>$page])->toArray();
//                 }else{
//                      $xie1=Db::name('xie')->where('a.store','in',$shuzu)->where('sku',$sku)->field('*,sum(shuliang) as dpshuliang')->group("a.store,sku")->paginate(10,true,['page'=>$page])->toArray();
//                 }
          
//       }
                        
//           }
// if($peijian=='true'){
//     if($youhui=='true'){
//         if(count($shuzu)==0){
//             $peijian1=Db::name('peijian')->alias('a')
// ->join('youhui w','a.sku = w.sku')->where('a.sku',$sku) ->field('a.*,sum(shuliang) as dpshuliang')->group("a.store,a.sku")->paginate(10,true,['page'=>$page])->toArray(); 
//         }else{
//               $peijian1=Db::name('peijian')->alias('a')
// ->join('youhui w','a.sku = w.sku')->where('a.store','in',$shuzu)->where('a.sku',$sku) ->field('a.*,sum(shuliang) as dpshuliang')->group("a.store,a.sku")->paginate(10,true,['page'=>$page])->toArray(); 
//         }
               
//             }else{
//                 if(count($shuzu)==0){
//                       $peijian1=Db::name('peijian')->where('sku',$sku)->field('*,sum(shuliang) as dpshuliang')->group("store,sku")->paginate(10,true,['page'=>$page])->toArray();
//                 }else{
//                          $peijian1=Db::name('peijian')->where('store','in',$shuzu)->where('sku',$sku)->field('*,sum(shuliang) as dpshuliang')->group("store,sku")->paginate(10,true,['page'=>$page])->toArray();  
//                 }
 
// }

  
// }
