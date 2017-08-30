<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use PDF;
use App\Models\Order;
use App\Models\Base;

class ManagerController extends Controller
{
    public function getIndex(Request $request){
        session(['start'=>$request->start]);
        session(['end'=>$request->end]);
        if(!empty($request->start) && !empty($request->end)){
            $start=$request->start;
            $arrayStart=explode("-",$start);
            $end  =$request->end;
            $arrayEnd=explode("-",$end);
            $yearStart=$arrayStart[0];
            $monthStart=$arrayStart[2];
            $yearEnd=$arrayEnd[0];
            $monthEnd=$arrayEnd[2];
            $orders = DB::table('orders')->selectRaw('*, count(order_id) as Total')->groupBy('member_id')->whereRaw('MONTH(created_at) >= ?',[$monthStart])->whereRaw('YEAR(created_at) = ?',[$yearStart])->whereRaw('MONTH(created_at) <= ?',[$monthEnd])->whereRaw('YEAR(created_at) = ?',[$yearEnd])->orderBy('Total','desc')->get();
            $first = DB::table('orders')->selectRaw('*, count(order_id) as Total')->groupBy('group_name')->whereRaw('MONTH(created_at) = ?',[$monthStart])->whereRaw('YEAR(created_at) = ?',[$yearStart])->whereRaw('MONTH(created_at) <= ?',[$monthEnd])->whereRaw('YEAR(created_at) = ?',[$yearEnd])->orderBy('Total','desc')->get();
            $bases=DB::table('bases')->selectRaw('*,count(id) as Total')->groupBy('user_id')->whereRaw('MONTH(created_at) = ?',[$monthStart])->whereRaw('YEAR(created_at) = ?',[$yearStart])->whereRaw('MONTH(created_at) <= ?',[$monthEnd])->whereRaw('YEAR(created_at) = ?',[$yearEnd])->orderBy('Total','DESC')->get();
            return view('manager.index')->with('orders',$orders)->with('first',$first)->with('bases',$bases);



        }elseif(!empty($request->start) && empty($request->end)){
            $start=$request->start;
            $arrayStart=explode("-",$start);
            $yearStart=$arrayStart[0];
            $monthStart=$arrayStart[2];
            $orders = DB::table('orders')->selectRaw('*, count(order_id) as Total')->groupBy('member_id')->whereRaw('MONTH(created_at) >= ?',[$monthStart])->whereRaw('YEAR(created_at) = ?',[$yearStart])->orderBy('Total','desc')->get();
            $first = DB::table('orders')->selectRaw('*, count(order_id) as Total')->groupBy('group_name')->whereRaw('MONTH(created_at) = ?',[$monthStart])->whereRaw('YEAR(created_at) = ?',[$yearStart])->orderBy('Total','desc')->get();
            $bases=DB::table('bases')->selectRaw('*,count(id) as Total')->groupBy('user_id')->whereRaw('MONTH(created_at) = ?',[$monthStart])->whereRaw('YEAR(created_at) = ?',[$yearStart])->orderBy('Total','DESC')->get();
            return view('manager.index')->with('orders',$orders)->with('first',$first)->with('bases',$bases);

        }elseif(empty($request->start) && !empty($request->end)){
            $end  =$request->end;
            $arrayEnd=explode("-",$end);
            $yearEnd=$arrayEnd[0];
            $monthEnd=$arrayEnd[2];
            $orders = DB::table('orders')->selectRaw('*, count(order_id) as Total')->groupBy('member_id')->whereRaw('MONTH(created_at) <= ?',[$monthEnd])->whereRaw('YEAR(created_at) = ?',[$yearEnd])->orderBy('Total','desc')->get();
            $first = DB::table('orders')->selectRaw('*, count(order_id) as Total')->groupBy('group_name')->whereRaw('MONTH(created_at) <= ?',[$monthEnd])->whereRaw('YEAR(created_at) = ?',[$yearEnd])->orderBy('Total','desc')->get();
            $bases=DB::table('bases')->selectRaw('*,count(id) as Total')->groupBy('user_id')->whereRaw('MONTH(created_at) <= ?',[$monthEnd])->whereRaw('YEAR(created_at) = ?',[$yearEnd])->orderBy('Total','DESC')->get();
            return view('manager.index')->with('orders',$orders)->with('first',$first)->with('bases',$bases);

        }

    	$currentMonth = date('m');
    	$currentYear=date('Y');
		$orders = DB::table('orders')->selectRaw('*, count(order_id) as Total')->groupBy('member_id')->whereRaw('MONTH(created_at) = ?',[$currentMonth])->whereRaw('YEAR(created_at) = ?',[$currentYear])->orderBy('Total','desc')->get();

		$first = DB::table('orders')->selectRaw('*, count(order_id) as Total')->groupBy('group_name')->whereRaw('MONTH(created_at) = ?',[$currentMonth])->whereRaw('YEAR(created_at) = ?',[$currentYear])->orderBy('Total','desc')->get();
		$bases=DB::table('bases')->selectRaw('*,count(id) as Total')->groupBy('user_id')->whereRaw('MONth(created_at)=?',[$currentMonth])->orderBy('Total','DESC')->get();

    	return view('manager.index')->with('orders',$orders)->with('first',$first)->with('bases',$bases);
    }

    public function getReport(Request $request){
        if(!empty(session()->get('start')) && !empty(session()->get('end'))){
            $start=session()->get('start');
            $arrayStart=explode("-",$start);
            $end  =session()->get('end');
            $arrayEnd=explode("-",$end);
            $yearStart=$arrayStart[0];
            $monthStart=$arrayStart[2];
            $yearEnd=$arrayEnd[0];
            $monthEnd=$arrayEnd[2];
            $orders = DB::table('orders')->selectRaw('*, count(order_id) as Total')->groupBy('member_id')->whereRaw('MONTH(created_at) >= ?',[$monthStart])->whereRaw('YEAR(created_at) = ?',[$yearStart])->whereRaw('MONTH(created_at) <= ?',[$monthEnd])->whereRaw('YEAR(created_at) = ?',[$yearEnd])->orderBy('Total','desc')->get();
            $first = DB::table('orders')->selectRaw('*, count(order_id) as Total')->groupBy('group_name')->whereRaw('MONTH(created_at) = ?',[$monthStart])->whereRaw('YEAR(created_at) = ?',[$yearStart])->whereRaw('MONTH(created_at) <= ?',[$monthEnd])->whereRaw('YEAR(created_at) = ?',[$yearEnd])->orderBy('Total','desc')->get();
            $bases=DB::table('bases')->selectRaw('*,count(id) as Total')->groupBy('user_id')->whereRaw('MONTH(created_at) = ?',[$monthStart])->whereRaw('YEAR(created_at) = ?',[$yearStart])->whereRaw('MONTH(created_at) <= ?',[$monthEnd])->whereRaw('YEAR(created_at) = ?',[$yearEnd])->orderBy('Total','DESC')->get();
            $pdf = PDF::loadView('manager.report',['orders'=>$orders,'first'=>$first,'bases'=>$bases]);
            return $pdf->download('report.pdf',['bases'=>$bases,'first'=>$first,'orders'=>$orders]);


        }elseif(!empty(session()->get('start')) && empty(session()->get('end'))){
            $start=session()->get('start');
            $arrayStart=explode("-",$start);
            $yearStart=$arrayStart[0];
            $monthStart=$arrayStart[2];
            $orders = DB::table('orders')->selectRaw('*, count(order_id) as Total')->groupBy('member_id')->whereRaw('MONTH(created_at) >= ?',[$monthStart])->whereRaw('YEAR(created_at) = ?',[$yearStart])->orderBy('Total','desc')->get();
            $first = DB::table('orders')->selectRaw('*, count(order_id) as Total')->groupBy('group_name')->whereRaw('MONTH(created_at) = ?',[$monthStart])->whereRaw('YEAR(created_at) = ?',[$yearStart])->orderBy('Total','desc')->get();
            $bases=DB::table('bases')->selectRaw('*,count(id) as Total')->groupBy('user_id')->whereRaw('MONTH(created_at) = ?',[$monthStart])->whereRaw('YEAR(created_at) = ?',[$yearStart])->orderBy('Total','DESC')->get();
            $pdf = PDF::loadView('manager.report',['orders'=>$orders,'first'=>$first,'bases'=>$bases]);
            return $pdf->download('report.pdf',['bases'=>$bases,'first'=>$first,'orders'=>$orders]);
        }elseif(empty(session()->get('start')) && !empty(session()->get('end'))){
            $end  =session()->get('end');
            $arrayEnd=explode("-",$end);
            $yearEnd=$arrayEnd[0];
            $monthEnd=$arrayEnd[2];
            $orders = DB::table('orders')->selectRaw('*, count(order_id) as Total')->groupBy('member_id')->whereRaw('MONTH(created_at) <= ?',[$monthEnd])->whereRaw('YEAR(created_at) = ?',[$yearEnd])->orderBy('Total','desc')->get();
            $first = DB::table('orders')->selectRaw('*, count(order_id) as Total')->groupBy('group_name')->whereRaw('MONTH(created_at) <= ?',[$monthEnd])->whereRaw('YEAR(created_at) = ?',[$yearEnd])->orderBy('Total','desc')->get();
            $bases=DB::table('bases')->selectRaw('*,count(id) as Total')->groupBy('user_id')->whereRaw('MONTH(created_at) <= ?',[$monthEnd])->whereRaw('YEAR(created_at) = ?',[$yearEnd])->orderBy('Total','DESC')->get();
            $pdf = PDF::loadView('manager.report',['orders'=>$orders,'first'=>$first,'bases'=>$bases]);
            return $pdf->download('report.pdf',['bases'=>$bases,'first'=>$first,'orders'=>$orders]);
        }

        $currentMonth = date('m');
    	$currentYear=date('Y');
		$orders = DB::table('orders')->selectRaw('*, count(order_id) as Total')->groupBy('member_id')->whereRaw('MONTH(created_at) = ?',[$currentMonth])->whereRaw('YEAR(created_at) = ?',[$currentYear])->orderBy('Total','desc')->get();

		$first = DB::table('orders')->selectRaw('*, count(order_id) as Total')->groupBy('group_name')->whereRaw('MONTH(created_at) = ?',[$currentMonth])->whereRaw('YEAR(created_at) = ?',[$currentYear])->orderBy('Total','desc')->get();
		$bases=DB::table('bases')->selectRaw('*,count(id) as Total')->groupBy('user_id')->whereRaw('MONth(created_at)=?',[$currentMonth])->orderBy('Total','DESC')->get();
  		$pdf = PDF::loadView('manager.report',['orders'=>$orders,'first'=>$first,'bases'=>$bases]);
  		return $pdf->download('report.pdf',['bases'=>$bases,'first'=>$first,'orders'=>$orders]);
    }

    public function getManagerLoadFirst(Request $request){
        $today=date("Y-m-d");
        $start=$request->start;
        if($request->filter=="all"){
            $order=Order::orderBy('id','DESC')->skip($start)->take(15)->get();
            return $order;
        }elseif($request->filter=="ready"){
            $order=Order::orderBy('id','DESC')->where('status','1')->skip($start)->take(15)->get();
            return $order;
        }elseif($request->filter=="not_yet"){
            $order=Order::orderBy('id','DESC')->where('status','!=','1')->skip($start)->take(15)->get();
            return $order;
        }else{
            $current_time=date("Y-m-d");
            $order = DB::select("SELECT *, DATEDIFF(orders.dateline,'$today') as Expire from orders WHERE status !=1  HAVING Expire <=3   ORDER BY id  DESC LIMIT $start,15"); 
            return $order;
        }
       
    }

    public function getManagerCountOrder(Request $request){
        $date=date("Y-m-d");
        if($request->filter=="all"){
            return count(Order::all());
        }elseif($request->filter=="rest_deadline"){
            $order = DB::select("SELECT *, DATEDIFF(orders.dateline,'$date') as Expire from orders WHERE status !=1  HAVING Expire <=3   ORDER BY id  DESC"); 
            return count($order);
        }elseif($request->filter=="ready"){
            $order=Order::orderBy('id','DESC')->where('status','1')->get();
            return count($order);
        }elseif($request->filter=="not_yet"){
             $order=Order::orderBy('id','DESC')->where('status','!=','1')->get();
             return count($order);
        }
    }

    public function getManagerOptionSelect(Request $request){
       $option=$request->option;
       $start=$request->start?:'0';
       $filter=$request->filter;
       $day=date("d");
       $month=date("m");
       $year=date("Y");
       if($option=="today"){
            if($filter=="all"){
                return $order=Order::orderBy('id','DESC')->where('day',$day)->where('month',$month)->where('year',$year)->get();
            }elseif($filter=="ready"){
                return $order=Order::orderBy('id','DESC')->where('status','1')->where('day',$day)->where('month',$month)->where('year',$year)->get();
            }elseif($filter=="not_yet"){
                return $order=Order::orderBy('id','DESC')->where('status','!=','1')->where('day',$day)->where('month',$month)->where('year',$year)->get();
            }elseif($filter=="rest_deadline"){
                $order = DB::select("SELECT *, DATEDIFF(orders.dateline,'2017-08-07') as Expire from orders WHERE status !=1 AND day = '".$day."' AND month='".$month."' AND year='".$year."' HAVING Expire <=3   ORDER BY id  DESC"); 
            return $order;
            }
       }elseif($option=="this_month"){
            if($filter=="all"){
                return $order=Order::orderBy('id','DESC')->where('month',$month)->where('year',$year)->get();
            }elseif($filter=="ready"){
                return $order=Order::orderBy('id','DESC')->where('status','1')->where('month',$month)->where('year',$year)->get();
            }elseif($filter=="not_yet"){
                return $order=Order::orderBy('id','DESC')->where('status','!=','1')->where('month',$month)->where('year',$year)->get();
            }elseif($filter=="rest_deadline"){
                $order = DB::select("SELECT *, DATEDIFF(orders.dateline,'2017-08-07') as Expire from orders WHERE status !=1  AND month='".$month."' AND year='".$year."' HAVING Expire <=3   ORDER BY id  DESC "); 
            return $order;
            }
       }elseif($option=="this_year"){
            if($filter=="all"){
                return $order=Order::orderBy('id','DESC')->where('year',$year)->get();
            }elseif($filter=="ready"){
                return $order=Order::orderBy('id','DESC')->where('status','1')->where('year',$year)->get();
            }elseif($filter=="not_yet"){
                return $order=Order::orderBy('id','DESC')->where('status','!=','1')->where('year',$year)->get();
            }elseif($filter=="rest_deadline"){
                $order = DB::select("SELECT *, DATEDIFF(orders.dateline,'2017-08-07') as Expire from orders WHERE status !=1  AND year='".$year."' HAVING Expire <=3   ORDER BY id  DESC"); 
            return $order;
            }
       }
    }


    public function getManagerCountOrderOption(Request $request){
        $day=date('d');
        $month=date('m');
        $year=date('Y');
       if($request->filter=="all"){
            if($request->option=="today"){
                $order=Order::where('day',$day)->where('month',$month)->where('year',$year)->get();
                return count($order);
            }elseif($request->option=="this_month"){
                $order=Order::where('month',$month)->where('year',$year)->get();
                 return count($order);
            }elseif($request->option=="this_year"){
               $order=Order::where('year',$year)->get();
                 return count($order);
            }else{
                 return count(Order::all());
            }
           
        }elseif($request->filter=="rest_deadline"){
            if($request->option=="today"){
                 $order = DB::select("SELECT *, DATEDIFF(orders.dateline,'2017-08-07') as Expire from orders WHERE status !=1 AND  day='".$day."' AND month='".$month."' AND year='".$year."'  HAVING Expire <=3   ORDER BY id  DESC"); 
                 return count($order);
            }elseif($request->option=="this_month"){
                $order = DB::select("SELECT *, DATEDIFF(orders.dateline,'2017-08-07') as Expire from orders WHERE status !=1  AND month='".$month."' AND year='".$year."'  HAVING Expire <=3   ORDER BY id  DESC"); 
                 return count($order);
            }elseif($request->option=="this_year"){
                    $order = DB::select("SELECT *, DATEDIFF(orders.dateline,'2017-08-07') as Expire from orders WHERE status !=1  AND year='".$year."'  HAVING Expire <=3   ORDER BY id  DESC"); 
                 return count($order);
            }else{
                $order = DB::select("SELECT *, DATEDIFF(orders.dateline,'2017-08-07') as Expire from orders WHERE status !=1  HAVING Expire <=3   ORDER BY id  DESC"); 
                return count($order);
            }
        }elseif($request->filter=="ready"){
            if($request->option=="today"){
                  $order=Order::orderBy('id','DESC')->where('day',$day)->where('month',$month)->where('year',$year)->where('status','1')->get();
                  return count($order);
            }elseif($request->option=="this_month"){
                     $order=Order::orderBy('id','DESC')->where('month',$month)->where('year',$year)->where('status','1')->get();
                     return count($order);
            }elseif($request->option=="this_year"){
                    $order=Order::orderBy('id','DESC')->where('year',$year)->where('status','1')->get();
                     return count($order);
            }else{
                    $order=Order::orderBy('id','DESC')->where('status','1')->get();
                    return count($order);
            }
        }elseif($request->filter=="not_yet"){
            if($request->option=="today"){
                 $order=Order::orderBy('id','DESC')->where('day',$day)->where('month',$month)->where('year',$year)->where('status','!=','1')->get();
                 return count($order);
            }elseif($request->option=="this_month"){
                $order=Order::orderBy('id','DESC')->where('month',$month)->where('year',$year)->where('status','!=','1')->get();
                return count($order);
            }elseif($request->option=="this_year"){
                 $order=Order::orderBy('id','DESC')->where('year',$year)->where('status','!=','1')->get();
                  return count($order);
            }else{
                 $order=Order::orderBy('id','DESC')->where('status','!=','1')->get();
                  return count($order);
            }
        }
    }

    public function getManageLoadFirst(Request $request){
            $filter=$request->filter;
            $day=$request->first_select_date;
            $month=$request->first_select_month;
            $year=$request->select_year;
            $order=Order::where('id','!=','0');
            if($filter=="all"){
                    if(!empty($day)){
                       $order=$order->where('day',$day);
                    }
                    if(!empty($month)){
                        $order=$order->where('month',$month);
                    }
                    if(!empty($year)){
                        $order=$order->where('year',$year);
                    }
                    return $order->get();
            }elseif($filter=="ready"){
                    $order=$order->where('status','1');
                     if(!empty($day)){
                       $order=$order->where('day',$day);
                    }
                    if(!empty($month)){
                        $order=$order->where('month',$month);
                    }
                    if(!empty($year)){
                        $order=$order->where('year',$year);
                    }
                     return $order->get();
            }elseif($filter=="not_yet"){
                 $order=$order->where('status','!=','1');
                     if(!empty($day)){
                       $order=$order->where('day',$day);
                    }
                    if(!empty($month)){
                        $order=$order->where('month',$month);
                    }
                    if(!empty($year)){
                        $order=$order->where('year',$year);
                    }
                    return $order->get();
            }else{
                 if(!empty($day) && !empty($month) && !empty($year)){
                          $order = DB::select("SELECT *, DATEDIFF(orders.dateline,'2017-08-07') as Expire from orders WHERE status !=1 AND day= '".$day."' AND month= '".$month."'  AND year ='".$year."'  HAVING Expire <=3   ORDER BY id  DESC"); 
                          return $order;
                 }elseif(!empty($day) && empty($month) && empty($year)){
                      $order = DB::select("SELECT *, DATEDIFF(orders.dateline,'2017-08-07') as Expire from orders WHERE status !=1 AND day= '".$day."'   HAVING Expire <=3   ORDER BY id  DESC"); 
                          return $order;
                 }elseif(!empty($day) && !empty($month) && empty($year)){
                          $order = DB::select("SELECT *, DATEDIFF(orders.dateline,'2017-08-07') as Expire from orders WHERE status !=1 AND day= '".$day."' AND month= '".$month."'   HAVING Expire <=3   ORDER BY id  DESC"); 
                          return $order;
                 }elseif(empty($day) && !empty($month) && !empty($year)){
                            $order = DB::select("SELECT *, DATEDIFF(orders.dateline,'2017-08-07') as Expire from orders WHERE status !=1  AND month= '".$month."'  AND year ='".$year."'  HAVING Expire <=3   ORDER BY id  DESC"); 
                          return $order;
                 }elseif(empty($day) && empty($month) && !empty($year)){
                          $order = DB::select("SELECT *, DATEDIFF(orders.dateline,'2017-08-07') as Expire from orders WHERE status !=1  AND year ='".$year."'  HAVING Expire <=3   ORDER BY id  DESC"); 
                          return $order;
                 }elseif(!empty($day) && empty($month) && !empty($year)){
                        $order = DB::select("SELECT *, DATEDIFF(orders.dateline,'2017-08-07') as Expire from orders WHERE status !=1 AND day= '".$day."'   AND year ='".$year."'  HAVING Expire <=3   ORDER BY id  DESC"); 
                          return $order;

                 }elseif(empty($day) && !empty($month) && empty($year)){
                          $order = DB::select("SELECT *, DATEDIFF(orders.dateline,'2017-08-07') as Expire from orders WHERE status !=1  AND month= '".$month."'   HAVING Expire <=3   ORDER BY id  DESC"); 
                          return $order;
                 }else{
                        $order = DB::select("SELECT *, DATEDIFF(orders.dateline,'2017-08-07') as Expire from orders WHERE status !=1   HAVING Expire <=3   ORDER BY id  DESC"); 
                          return $order;
                 }
            }
    }

    public function getPrintReportFirst(Request $request){
        $day=date('d');
        $month=date('m');
        $year=date('Y');
        $date=date("Y-m-d");
        $filter=$request->filter;
        // $sql="SELECT *,count(orders.id) as Total FROM orders";
        // if($filter=="all"){
        //     $sql.=" WHERE  id !=0";
        // }
        // if($request->filter=="ready"){
        //     $sql.=" WHERE status =1";
        // }
        // if($request->filter=="not_yet"){
        //     $sql.=" WHERE status !=1";
        // }
        // if($request->filter=="rest_deadline"){
        //     $sql="SELECT *,count(orders.id) as Total,DATEDIFF(orders.dateline,'$date') as Expire FROM orders";
        // }

        // if($request->option !=""){
        //     if($request->option=="today"){
        //         $sql.=" WHERE day =$day AND month=$month AND year =$year";
        //     }elseif($request->option=="this_month"){
        //         $sql.=" WHERE day =$day AND month=$month AND year =$year";
        //     }elseif($request->option=="this_year"){
        //          $sql.=" WHERE year =$year";
        //     }
        //    $sql.=" GROUP BY member_id";
        //    $orders=DB::select($sql);
        //    $pdf = PDF::loadView('manager.report',['orders'=>$orders]);
        //    return $pdf->download('report.pdf',['orders'=>$orders]);
        // }else{

        // }
        if($request->option !=""){
            if($request->option=="today"){
                $sql="SELECT *,count(orders.id) as Total FROM orders";
                if($filter=="all"){
                    $sql.=" WHERE orders.id !=1 AND day=$day AND month=$month AND year=$year";
                }
                if($request->filter=="ready"){
                    $sql.=" WHERE orders.status =1 AND day=$day AND month=$month AND year=$year";
                }
                if($request->filter=="not_yet"){
                    $sql.=" WHERE orders.status !=1 AND day=$day AND month=$month AND year=$year";
                }
                if($request->filter=="rest_deadline"){
                    $sql="SELECT *,count(orders.id) as Total,DATEDIFF(orders.dateline,'$date') as Expire FROM orders WHERE orders.status !=1 AND day =$day AND month=$month AND year =$year GROUP BY orders.member_id HAVING Expire <=3";
                }
                return DB::select($sql);
            }elseif($request->option=="this_month"){
                $sql="SELECT *,count(orders.id) as Total FROM orders";
                if($filter=="all"){
                    $sql.=" WHERE orders.id !=1  AND month=$month AND year=$year";
                }
                if($request->filter=="ready"){
                    $sql.=" WHERE orders.status =1  AND month=$month AND year=$year";
                }
                if($request->filter=="not_yet"){
                    $sql.=" WHERE orders.status !=1  AND month=$month AND year=$year";
                }
                if($request->filter=="rest_deadline"){
                     $sql="SELECT *,count(orders.id) as Total,DATEDIFF(orders.dateline,'$date') as Expire FROM orders WHERE orders.status !=1  AND month=$month AND year =$year GROUP BY orders.member_id HAVING Expire <=3 ";
                }
                return DB::select($sql);
            }elseif($request->option=="this_year"){
                $sql="SELECT *,count(orders.id) as Total FROM orders";
                if($filter=="all"){
                    $sql.=" WHERE orders.id !=1 AND year=$year";
                }
                if($request->filter=="ready"){
                    $sql.=" WHERE orders.status =1 AND year=$year";
                }
                if($request->filter=="not_yet"){
                    $sql.=" WHERE orders.status !=1 AND year=$year";
                }
                if($request->filter=="rest_deadline"){
                    $sql="SELECT *,count(orders.id) as Total,DATEDIFF(orders.dateline,'$date') as Expire FROM orders WHERE orders.status !=1 AND year =$year GROUP BY orders.member_id  HAVING Expire <=3";
                }
                return DB::select($sql);
            }
        }else{

        }


    }

    public function getManagerBase(Request $request){
        return view('manager.bases');
    }

    public function getManagerGetBaseTemplate(Request $request){
        $day=date("d");
        $month=date("m");
        $year=date("Y");
        $start=$request->start;
        $page=$request->page;
        return $bases=DB::select("SELECT bases.name,bases.created_at,bases.type_name,bases.version_name,users.name as UserName FROM bases LEFT JOIN users ON bases.user_id = users.id WHERE bases.day =$day AND month=$month AND YEAR =$year limit $start,$page");
    }

    public function getManagerSearchBaseTemplate(Request $request){
        $day=date("d");
        $month=date("m");
        $year=date("Y");
        $start=$request->start;
        $page=$request->page;
        if(is_numeric($request->search)){
                return $bases=DB::select("SELECT bases.name,bases.created_at,bases.type_name,bases.version_name,users.name as UserName FROM bases LEFT JOIN users ON bases.user_id = users.id WHERE bases.name LIKE'%$request->search%'   LIMIT 0,20");

        }else{
             return $bases=DB::select("SELECT bases.name,bases.created_at,bases.type_name,bases.version_name,users.name as UserName FROM bases LEFT JOIN users ON bases.user_id = users.id WHERE users.name LIKE'%$request->search%'   LIMIT 0,20");

        }
    }
    public function getManagerFilterBaseOption(Request $request){
         $day=date("d");
         $month=date("m");
         $year=date("Y");
         $start=$request->start;
         $page=$request->page;
         $search=$request->search;
         $filter=$request->filter;
         if(!empty($search)){
            if($filter=="today"){
                return $bases=DB::select("SELECT bases.name,bases.created_at,bases.type_name,bases.version_name,users.name as UserName FROM bases LEFT JOIN users ON bases.user_id = users.id WHERE bases.name LIKE'%$request->search%' OR users.name LIKE'%$search%' AND day =$day AND month =$month AND year =$year");
            }elseif($filter=="this_month"){
                 return $bases=DB::select("SELECT bases.name,bases.created_at,bases.type_name,bases.version_name,users.name as UserName FROM bases LEFT JOIN users ON bases.user_id = users.id WHERE bases.name LIKE'%$request->search%' OR users.name LIKE'%$search%'  AND month =$month AND year =$year");

            }else{
                return $bases=DB::select("SELECT bases.name,bases.created_at,bases.type_name,bases.version_name,users.name as UserName FROM bases LEFT JOIN users ON bases.user_id = users.id WHERE users.name LIKE'%$request->search%' OR bases.name LIKE'%$search%'  AND year =$year");

            }
         }else{
            if($filter=="today"){
                return $bases=DB::select("SELECT bases.name,bases.created_at,bases.type_name,bases.version_name,users.name as UserName FROM bases LEFT JOIN users ON bases.user_id = users.id WHERE  day =$day AND month =$month AND year =$year ");
            }elseif($filter=="this_month"){
                 return $bases=DB::select("SELECT bases.name,bases.created_at,bases.type_name,bases.version_name,users.name as UserName FROM bases LEFT JOIN users ON bases.user_id = users.id WHERE   month =$month AND year =$year");

            }else{
                return $bases=DB::select("SELECT bases.name,bases.created_at,bases.type_name,bases.version_name,users.name as UserName FROM bases LEFT JOIN users ON bases.user_id = users.id WHERE   year =$year");

            }
         }
    }
    

    public function getManagerLoadBaseTemplate(Request $request){
        $day=$request->day;
        $month=$request->month;
        $year=$request->year;
        $search=$request->search;
        if(!empty($search)){
            $sql="SELECT bases.name,bases.created_at,bases.type_name,bases.version_name,users.name as UserName FROM bases LEFT JOIN users ON bases.user_id = users.id WHERE bases.id !=0 ";
            if(!empty($day)){
               $sql.=" AND day =$day";
            }
            if(!empty($month)){
                $sql.=" AND month=$month";
            }
            if(!empty($year)){
                $sql.=" AND year=$year";
            }
            $sql.=" AND users.name LIKE'%$request->search%' OR bases.name LIKE'%$search%'";
            return $bases=DB::select($sql);
        }else{
            $sql="SELECT bases.name,bases.created_at,bases.type_name,bases.version_name,users.name as UserName FROM bases LEFT JOIN users ON bases.user_id = users.id WHERE bases.id !=0 ";
            if(!empty($day)){
                $sql.="AND day =$day";
            }
            if(!empty($month)){
                $sql.=" AND month=$month";
            }
            if(!empty($year)){
                $sql.=" AND year=$year";
            }
            if(empty($day) && empty($month) && empty($year)){
                return Null;
            }
           return $bases=DB::select($sql);
        }
    }

    public function getManagerPrintReportBase(Request $request){
        $day=date("d");
        $month=date("m");
        $year=date("Y");
        if(!empty($request->filter)){
            $sql="SELECT count(bases.id) as Total, bases.name,bases.created_at,bases.type_name,bases.version_name,users.name as UserName FROM bases LEFT JOIN users ON bases.user_id = users.id WHERE bases.id !=0 ";
            if($request->filter=="today"){
                $sql.=" AND day =$day AND month=$month AND year =$year";
            }
            if($request->filter=="this_month"){
             $sql.=" AND month=$month AND year =$year";
            }
            if($request->filter=="this_year"){
                $sql.=" AND year =$year";
            }
            if(!empty($request->search)){
                if($request->search/2 !=0){
                  $sql.=" AND bases.name LIKE '%$request->search%'";
                }else{
                    $sql.=" AND users.name LIKE '%$request->search%'";
                }
            }
            $sql.=" GROUP BY users.id ORDER BY Total";
            $bases=DB::select($sql);
           
            //return view('manager.bases_report')->with('bases',$bases)->with('date',"Report ".date('m-d-Y'));
            $pdf = PDF::loadView('manager.bases_report',['bases'=>$bases,'date'=>'Report '.date('m-d-Y')]);
           return $pdf->download('report.pdf',['bases'=>$bases,'date'=>'Report '.date('m-d-Y')]);
            
        }else{
            $day=$request->day;
            $month=$request->month;
            $year=$request->year;
            $date="Report ";
            $sql="SELECT count(bases.id) as Total, bases.name,bases.created_at,bases.type_name,bases.version_name,users.name as UserName FROM bases LEFT JOIN users ON bases.user_id = users.id WHERE bases.id !=0 ";
            if(!empty($day)){
                $sql.=" AND day =$day";
                $date.=$day;
            }
            if(!empty($month)){
                $sql.=" AND month =$month";
                $date.="-".$month;
            }
            if(!empty($year)){
                 $sql.=" AND year =$year";
                 $date.="-".$year;
            }
            if(!empty($request->search)){
                if($request->search/2 !=0){
                  $sql.=" AND bases.name LIKE '%$request->search%'";
                }else{
                    $sql.=" AND users.name LIKE '%$request->search%'";
                }
            }
            $sql.=" GROUP BY users.id ORDER BY Total";
            $bases=DB::select($sql);
            // return view('manager.bases_report')->with('bases',$bases)->with('date',$date);
            $pdf = PDF::loadView('manager.bases_report',['bases'=>$bases,'date'=>$date]);
            return $pdf->download('report.pdf',['bases'=>$bases,'date'=>$date]);
        }
    }
}
