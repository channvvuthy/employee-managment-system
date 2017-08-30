<?php

namespace App\Http\Controllers;

use App\Models\Base;
use App\Models\Group;
use App\Models\Layout;
use App\Models\Order;
use App\Models\Path;
use App\Models\Pattern;
use App\Models\User;
use App\Models\Variation;
use Auth;
use DB;
use Dompdf\Exception;
use Excel;
use File;
use Illuminate\Http\Request;
use PDF;
use phpDocumentor\Reflection\Types\Null_;
use PhpParser\Node\Stmt\GroupUse;

class LeaderController extends Controller
{
    public function getIndex()
    {
        $groups = Group::where('status', '1')->get();
        return view('leader.index')->withGroups($groups);
    }

    public function getCreateBaseType()
    {
        $variations = Variation::paginate(10);
        return view('leader.baseType')->withVariations($variations);
    }

    public function postCreateVariation(Request $request)
    {
        $this->validate($request, [
            'variationName' => 'required',
            'variationDescription' => 'required'
        ]);
        $variation = new Variation();
        $variation->name = $request->variationName;
        $variation->description = $request->variationDescription;
        $variation->save();
        return redirect()->back()->withInput()->withErrors(['notice' => 'variation has been created']);
    }

    public function getEditVariation(Request $request, $id)
    {
        $variation = Variation::find($id);
        return view('leader.editVariation')->withVariation($variation);
    }

    public function postUpdateVariation(Request $request)
    {
        $id = $request->id;
        $name = $request->variationName;
        $description = $request->variationDescription;
        $variation = Variation::find($id);
        $variation->name = $name;
        $variation->description = $description;
        $variation->save();
        return redirect()->route('createBaseType')->withInput()->withErrors(['notice' => 'variation has been update complete']);
    }

    public function getDeleteVariation($id)
    {
        $variation = Variation::find($id);
        $variation->delete();
        $variation->pattern()->detach($id);
        return redirect()->back()->withInput()->withErrors(['notice' => 'variation has been deleted']);
    }

    public function getAjaxUpdateOrder(Request $request)
    {
        $id = $request->id;
        if ($request->CheckResult != "") {
            $order = Order::find($id);
            $order->leader_check_result = $request->CheckResult;
            $order->save();
            return "Order has been update";
        } else {
            $value = $request->value;
            $order = Order::find($id);
            $order->leader_check_description = $value;
            $order->save();
            return "Order description has been updated";
        }
    }

    public function getActiveVariation($id)
    {
        $variation = Variation::find($id);
        if ($variation->status == 1) {
            $variation->status = "0";
        } else {
            $variation->status = "1";
        }
        $variation->save();
        return redirect()->back()->withInput()->withErrors(['notice' => 'variation has been update']);

    }

    public function getCreatePattern()
    {

        $variations = Variation::where('status', '1')->get();
        $patterns = Pattern::paginate(10);
        return view('leader.pattern')->withVariations($variations)->withPatterns($patterns);
    }

    public function postCreatePattern(Request $request)
    {
        $this->validate($request, [
            'patternName' => 'required',
            'patternURL' => 'required',

        ]);
        $patternFile = $request->patternFile;
        if (!empty($patternFile)) {
            $fileName = time() . $patternFile->getClientOriginalName();
            $ex = explode(".", $fileName);
            $ex = end($ex);
            if ($ex == "zip" || $ex == "ra") {
                $patternFile->move('uploads/', $fileName);

            } else {
                return redirect()->back()->withInput()->withErrors(['patternFile' => 'File allow only zip and ra']);
            }
            $pattern = new Pattern();
            $pattern->variation_id = $request->variation;
            $pattern->name = $request->patternName;
            $pattern->url = $request->patternURL;
            $pattern->file_name = $fileName;
            $pattern->description = $request->patternDescription;
            $pattern->save();
        } else {
            $pattern = new Pattern();
            $pattern->variation_id = $request->variation;
            $pattern->name = $request->patternName;
            $pattern->url = $request->patternURL;
            $pattern->description = $request->patternDescription;
            $pattern->save();
        }
        return redirect()->route('createPattern')->withErrors(['notice' => 'pattern has been created']);


    }

    public function getEditPattern($id)
    {
        $pattern = Pattern::find($id);
        $variations = Variation::where('status', '1')->get();
        return view('leader.editPattern')->withPattern($pattern)->withVariations($variations);
    }

    public function postUpdatePattern(Request $request)
    {
        $pattern = Pattern::find($request->id);
        $patternFile = $request->patternFile;
        if (!empty($patternFile)) {
            $fileName = time() . $patternFile->getClientOriginalName();
            $ex = explode(".", $fileName);
            $ex = end($ex);
            if ($ex == "zip" || $ex == "ra") {
                $patternFile->move('uploads/', $fileName);

            } else {
                return redirect()->back()->withInput()->withErrors(['patternFile' => 'File allow only zip and ra']);
            }
            $pattern->variation_id = $request->variation;
            $pattern->name = $request->patternName;
            $pattern->url = $request->patternURL;
            $pattern->file_name = $fileName;
            $pattern->description = $request->patternDescription;
            File::delete('uploads/' . $pattern->file_name);
            $pattern->save();
        } else {

            $pattern->variation_id = $request->variation;
            $pattern->name = $request->patternName;
            $pattern->url = $request->patternURL;
            $pattern->description = $request->patternDescription;
            $pattern->save();
        }
        return redirect()->route('createPattern')->withInput()->withErrors(['notice' => 'pattern update complete']);
    }

    public function getActivePattern($id)
    {
        $pattern = Pattern::find($id);
        if ($pattern->status == "1") {
            $pattern->status = "0";
        } else {
            $pattern->status = "1";
        }
        $pattern->save();
        return redirect()->route('createPattern')->withInput()->withErrors(['notice' => 'pattern has been updated']);
    }

    public function getDeletePattern($id)
    {
        $pattern = Pattern::find($id);
        $pattern->delete();
        return redirect()->route('createPattern')->withInput()->withErrors(['notice' => 'pattern has been deleted']);
    }

    public function getBaseDirectory()
    {
        $path = Path::where('user_id', Auth::user()->id)->first();
        return view('leader.createPath')->with('path', $path);
    }

    public function postBaseDirectory(Request $request)
    {
        $oldPath = $request->oldPath;
        $path = Path::where('user_id', Auth::user()->id)->first();
        $this->validate($request, [
            'pathName' => 'required',
            'pathDescription' => 'required'
        ]);
        if (!empty($path)) {
            $path->path = $request->pathName;
            $path->user_id = Auth::user()->id;
            $path->description = $request->pathDescription;
            $path->path_for = 'base';
            $path->save();
            return redirect()->back()->withInput()->withErrors(['notice' => 'directory has been updated!']);
        } else {
            $path = new Path();
            $path->path = $request->pathName;
            $path->user_id = Auth::user()->id;
            $path->description = $request->pathDescription;
            $path->path_for = 'base';
            $path->save();
            return redirect()->back()->withInput()->withErrors(['notice' => 'directory has been created!']);

        }
    }

    public function getBaseList(Request $request)
    {
        if (!empty($request->num_row)) {
            $bases = Base::where('used', NULL)->paginate($request->num_row);
        } else {
            $bases = Base::where('used', NULL)->paginate(100);
        }

        $layouts = Layout::where('status', '1')->get();
        $groups = Group::where('type', 'first')->get();
        return view('leader.getBaseList')->withBases($bases)->withLayouts($layouts)->withGroups($groups);
    }

    public function getMessageQC(Request $request)
    {
        if (!empty($request->qc)) {
            $bases = DB::table('bases')
                ->leftJoin('users', 'users.id', '=', 'bases.user_id')
                ->where('first_checker_result', '0')
                ->select('users.name as userName', 'bases.*')
                ->get();
            return $bases;
        }
        $bases = DB::table('bases')
            ->leftJoin('users', 'users.id', '=', 'bases.user_id')
            ->where('first_checker_result', '0')->orWhere('first_checker_result', '2')
            ->select('users.name as userName', 'bases.*')
            ->get();
        return $bases;
    }

    public function postBaseList(Request $request)
    {

    }

    public function getLeaderDirectory()
    {
        $path=Path::where('user_id',Auth::user()->id)->first();
        return view('leader.firstDirectory')->with('path',$path);
    }

    public function postLeaderDirectory(Request $request)
    {
        $groupID = Auth::user()->group->id;
        $oldPath = $request->oldPath;
        $path = Path::where('user_id', Auth::user()->id)->first();
        $this->validate($request, [
            'pathName' => 'required',
            'pathDescription' => 'required'
        ]);
        if (!empty($path)) {
            $path->path = $request->pathName;
            $path->user_id = Auth::user()->id;
            $path->description = $request->pathDescription;
            $path->path_for = 'first';
            $path->group_id = $groupID;
            $path->save();
            return redirect()->back()->withInput()->withErrors(['notice' => 'directory has been updated!']);
        } else {
            $path = new Path();
            $path->path = $request->pathName;
            $path->user_id = Auth::user()->id;
            $path->description = $request->pathDescription;
            $path->path_for = 'first';
            $path->group_id = $groupID;
            $path->save();
            return redirect()->back()->withInput()->withErrors(['notice' => 'directory has been created!']);

        }
    }

    public function getUpdateStatusOrder(Request $request)
    {
        $id = $request->id;
        $vl = $request->vl;
        $order = Order::find($id);
        if ($vl == "1") {
            $order->date_ready = date("d-m-Y");
        } else {
            $order->date_ready = "";
        }
        $order->status = $vl;
        $order->save();
        return "Update Staus Already!";

    }

    public function getSaveLayoutAjax(Request $request)
    {
        $layout = $request->layout;
        $baseId = $request->baseId;
        $baseExist = \App\Models\BaseLayout::where('base_id', $baseId);
        $baseExist->delete();
        if (!empty($layout)) {
            foreach ($layout as $l) {
                DB::table('base_layout')->insert(['base_id' => $baseId, 'layout_id' => $l]);
            }
        }
        return "Save Success";

    }

    public function getLeaderUpdateBase(Request $request)
    {
        if (!empty($request->listFolder)) {
            $select = $this->openDir($request->listFolder);
            if (!empty($select)) {
                $bases = Base::whereIn('name', $select)->get();
                return view('leader.listFolder')->with('bases', $bases);
            }
            return redirect()->back();
        }
        if (!empty($request->search)) {
            $search = $request->search;
            $bases = Base::where('name', 'LIKE', '%' . $search . '%')->get();
            $layouts = Layout::where('status', '1')->get();
            $groups = Group::where('type', 'first')->get();
            return view('leader.getBaseSearch')->withBases($bases)->withLayouts($layouts)->withGroups($groups);

        }
        if (!empty($request->from && !empty($request->to))) {
            $from = $request->from;
            $from = explode("-", $from);
            $yearFrom = $from[0];
            $monthFrom = $from[1];
            $dateFrom = $from[2];
            $dateFrom;
            $to = $request->to;
            $to = explode("-", $to);
            $yearTo = $to[0];
            $monthTo = $to[1];
            $dateTo = $to[2];
            $bases = Base::whereMonth('created_at', '>=', $monthFrom)->whereMonth('created_at', '<=', $monthTo)->whereYear('created_at', '>=', $yearFrom)->whereYear('created_at', '<=', $yearTo)->get();
            $layouts = Layout::where('status', '1')->get();
            $groups = Group::where('type', 'first')->get();
            return view('leader.getBaseSearch')->withBases($bases)->withLayouts($layouts)->withGroups($groups);
        } else if (!empty($request->from && empty($request->to))) {
            $from = $request->from;
            $from = explode("-", $from);
            $yearFrom = $from[0];
            $monthFrom = $from[1];
            $dateFrom = $from[2];
            $dateFrom;
            $bases = Base::whereMonth('created_at', '>=', $monthFrom)->whereYear('created_at', '>=', $yearFrom)->get();
            $layouts = Layout::where('status', '1')->get();
            $groups = Group::where('type', 'first')->get();
            return view('leader.getBaseSearch')->withBases($bases)->withLayouts($layouts)->withGroups($groups);
        }

        $ids = $request->id;
        $i = 0;
        $version = $request->version_name;
        $version_id = $request->version_id;
        $note = $request->note;
        $used_by = $request->used_by;
        $used_by_id = $request->used_by_id;
        $leader_check_name = $request->leader_check_name;
        $submit = $request->submit;
        if (!empty($request->choose_action)) {
            $action = $request->choose_action;
            if ($action == "Update") {
                if (!empty($ids)) {
                    foreach ($ids as $id) {
                        $base = Base::find($id);
                        $base->version_name = $version[$i];
                        $base->version_id = $version_id[$i];
                        $base->note = $note[$i];
                        $base->used_by = $used_by[$i];
                        $base->used_by_id = $used_by_id[$i];
                        $base->get_it = $submit[$i];
                        $base->leader_check_name = $leader_check_name[$i];
                        $base->save();
                        $i++;
                    }
                    return redirect()->back()->withInput()->withErrors(['notice' => 'update success']);
                }
            } elseif ($action == "Delete") {
                if (!empty($request->check)) {
                    foreach ($request->check as $key) {
                        $base = Base::find($key);
                        $base->delete();
                    }
                }
                return redirect()->back()->withInput()->withErrors(['notice' => 'Base delete success']);
            }
        }
        return redirect()->back();

    }

    public function openDir($dir = null)
    {
        try {
            $folders = "";
            if (!empty($dir)) {
                $ds = scandir($dir);
                foreach ($ds as $d) {
                    if ($d != "." && $d != "..") {
                        $folders[] = $d;
                    }
                }
                return $folders;

            }
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function getUpdateStatusBaseLeaderCheck(Request $request)
    {
        if (!empty($request->member)) {
            $baseId = $request->baseId;
            $baseName = $request->baseName;
            $base = Base::find($baseId);
            $base->first_checker_result = $baseName;
            $base->save();
            return "Update Status Success";
        }
        if (!empty($request->qc)) {
            $baseId = $request->baseId;
            $baseName = $request->baseName;
            $base = Base::find($baseId);
            $base->first_checker_result = $baseName;
            $base->save();
            return "Update Status Success";

        }
        $baseId = $request->baseId;
        $baseName = $request->baseName;
        $base = Base::find($baseId);
        $base->leader_check_result = $baseName;
        $base->save();
        return "Update Status Success";
    }

    public function getUpdateProblemBase(Request $request)
    {

        $baseId = $request->baseId;
        $baseProblem = $request->val;
        if (!empty($request->qc)) {
            $base = Base::find($baseId);
            $base->first_checker_problem = $baseProblem;
            $base->save();
            return $baseProblem;
        }
        if (!empty($baseId)) {
            $base = Base::find($baseId);
            $base->leader_check_problem = $baseProblem;
            $base->save();
        }

        return $baseProblem;
    }

    public function getUpdatNote(Request $request)
    {
        $baseId = $request->baseId;
        $baseProblem = $request->val;
        if (!empty($baseId)) {
            $base = Base::find($baseId);
            $base->note = $baseProblem;
            $base->save();
        }
        return $baseProblem;
    }

    public function getSubmitQC(Request $request)
    {
        $base = Base::find($request->baseId);
        if ($base->get_it == "0") {
            $base->get_it = "1";
        } else {
            $base->get_it = "0";
        }
        $base->save();
        return "updated";
    }

    public function getNotificationLeader()
    {

        $bases = DB::table('bases')
            ->leftJoin('users', 'users.id', '=', 'bases.user_id')
            ->where('leader_check_result', '2')
            ->select('users.name as userName', 'bases.*')
            ->get();
        return $bases;
    }

    public function getMessageLeader(Request $request)
    {

        if (!empty($request->qc)) {
            $bases = DB::table('bases')
                ->leftJoin('users', 'users.id', '=', 'bases.user_id')
                ->where('first_checker_result', '2')
                ->select('users.name as userName', 'bases.*')
                ->get();
            return $bases;
        } else {
            $bases = DB::table('bases')
                ->leftJoin('users', 'users.id', '=', 'bases.user_id')
                ->where('leader_check_result', '0')
                ->select('users.name as userName', 'bases.*')
                ->get();
            return $bases;
        }

    }

    public function getBaseReport(Request $request)
    {
        $select_report = $request->select_report;
        $date = date("d");
        $month = date('m');
        $year = date('Y');
        $last_year = $year - 1;
        if ($select_report == "today") {
            return DB::select(DB::raw("select bases.id,bases.name as BaseName,bases.user_id,count(bases.user_id) as Total,users.name  from bases LEFT JOIN users on users.id=bases.user_id where day =$date  AND MONTH  =$month AND year=$year GROUP BY bases.user_id"));
        } elseif ($select_report == 'month') {
            return DB::select(DB::raw("select bases.id,bases.name as BaseName,bases.user_id,count(bases.user_id) as Total,users.name  from bases LEFT JOIN users on users.id=bases.user_id where month =$month AND year=$year GROUP BY bases.user_id"));
        } else if ($select_report == 'year') {
            return DB::select(DB::raw("select bases.id,bases.name as BaseName,bases.user_id,count(bases.user_id) as Total,users.name  from bases LEFT JOIN users on users.id=bases.user_id where year=$year GROUP BY bases.user_id"));
        } elseif ($select_report == 'last_year') {
            return DB::select(DB::raw("select bases.id,bases.name as BaseName,bases.user_id,count(bases.user_id) as Total,users.name  from bases LEFT JOIN users on users.id=bases.user_id where year=$last_year GROUP BY bases.user_id"));
        } else {
            return DB::select(DB::raw("select bases.id,bases.name as BaseName,bases.user_id,count(bases.user_id) as Total,users.name  from bases LEFT JOIN users on users.id=bases.user_id  GROUP BY bases.user_id"));
        }
    }

    public function getLoadBaseReport(Request $request)
    {
        $dateReport = $request->dateReport;
        $monthReport = $request->monthReport;
        $yearReport = $request->yearReport;
        if (!empty($dateReport) && !empty($monthReport) && !empty($yearReport)) {
            return DB::select(DB::raw("select bases.id,bases.name as BaseName,bases.user_id,count(bases.user_id) as Total,users.name  from bases LEFT JOIN users on users.id=bases.user_id where day =$dateReport AND month=$monthReport AND  year=$yearReport GROUP BY bases.user_id"));
        }
        if (!empty($dateReport) && !empty($monthReport) && empty($yearReport)) {
            return DB::select(DB::raw("select bases.id,bases.name as BaseName,bases.user_id,count(bases.user_id) as Total,users.name  from bases LEFT JOIN users on users.id=bases.user_id where day =$dateReport AND month=$monthReport GROUP BY bases.user_id"));

        }
        if (!empty($dateReport) && empty($monthReport) && empty($yearReport)) {
            return DB::select(DB::raw("select bases.id,bases.name as BaseName,bases.user_id,count(bases.user_id) as Total,users.name  from bases LEFT JOIN users on users.id=bases.user_id where  day=$dateReport GROUP BY bases.user_id"));

        }
        if (empty($dateReport) && empty($monthReport) && !empty($yearReport)) {
            return DB::select(DB::raw("select bases.id,bases.name as BaseName,bases.user_id,count(bases.user_id) as Total,users.name  from bases LEFT JOIN users on users.id=bases.user_id where  year=$yearReport GROUP BY bases.user_id"));

        }
        if (empty($dateReport) && !empty($monthReport) && !empty($yearReport)) {
            return DB::select(DB::raw("select bases.id,bases.name as BaseName,bases.user_id,count(bases.user_id) as Total,users.name  from bases LEFT JOIN users on users.id=bases.user_id where month =$monthReport  AND  year=$yearReport GROUP BY bases.user_id"));

        }
        if (empty($dateReport) && !empty($monthReport) && empty($yearReport)) {
            return DB::select(DB::raw("select bases.id,bases.name as BaseName,bases.user_id,count(bases.user_id) as Total,users.name  from bases LEFT JOIN users on users.id=bases.user_id where month=$monthReport GROUP BY bases.user_id"));
        }
        if (!empty($dateReport) && empty($monthReport) && !empty($yearReport)) {
            return DB::select(DB::raw("select bases.id,bases.name as BaseName,bases.user_id,count(bases.user_id) as Total,users.name  from bases LEFT JOIN users on users.id=bases.user_id where day =$dateReport  AND  year =$yearReport GROUP BY bases.user_id"));
        }
        return null;

    }

    public function getLeaderFirstGetBase(Request $request)
    {

        $leaderGroupID = Auth::user()->group->id;
        if ($request->action) {
            $filter_export = $request->option_filter;
            if ($filter_export == "" || $filter_export == 'all') {
                $export = DB::table('bases')
                    ->rightJoin('base_layout', 'bases.id', '=', 'base_layout.base_id')
                    ->rightJoin('layouts', 'layouts.id', '=', 'base_layout.layout_id')
                    ->leftJoin('versions', 'bases.version_id', '=', 'versions.id')
                    ->leftJoin('types', 'bases.type_id', '=', 'types.id')
                    ->select('bases.name AS Base_Name', 'layouts.name AS Layout_Name', 'versions.name as VersionName', 'types.name as typeName')
                    ->where('used_by_id', $leaderGroupID)
                    ->get();
                foreach ($export as $ex) {
                    DB::table('exports')->insert(['Base_Name' => $ex->Base_Name, 'Layout_Name' => $ex->Layout_Name, 'type' => $ex->typeName, 'version' => $ex->VersionName]);
                }
                $export = \App\Models\Export::orderBy('Base_Name', 'DESC')->get();
                DB::table('exports')->truncate();
                Excel::create('Export Custome', function ($excel) use ($export) {
                    $excel->sheet('Sheet1', function ($sheet) use ($export) {
                        $sheet->fromArray($export);
                    });
                })->export('xlsx');

            }
            if ($filter_export == "used") {

                $export = DB::table('bases')
                    ->rightJoin('base_layout', 'bases.id', '=', 'base_layout.base_id')
                    ->rightJoin('layouts', 'layouts.id', '=', 'base_layout.layout_id')
                    ->leftJoin('versions', 'bases.version_id', '=', 'versions.id')
                    ->leftJoin('types', 'bases.type_id', '=', 'types.id')
                    ->select('bases.name AS Base_Name', 'layouts.name AS Layout_Name', 'versions.name as VersionName', 'types.name as typeName')
                    ->where('used', '!=', Null)
                    ->where('used_by_id', $leaderGroupID)
                    ->get();
                foreach ($export as $ex) {
                    DB::table('exports')->insert(['Base_Name' => $ex->Base_Name, 'Layout_Name' => $ex->Layout_Name, 'type' => $ex->typeName, 'version' => $ex->VersionName]);
                }
                $export = \App\Models\Export::orderBy('Base_Name', 'DESC')->get();
                DB::table('exports')->truncate();
                Excel::create('Export Custome', function ($excel) use ($export) {
                    $excel->sheet('Sheet1', function ($sheet) use ($export) {
                        $sheet->fromArray($export);
                    });
                })->export('xlsx');
            }
            if ($filter_export == "not_yet") {
                $export = DB::table('bases')
                    ->rightJoin('base_layout', 'bases.id', '=', 'base_layout.base_id')
                    ->rightJoin('layouts', 'layouts.id', '=', 'base_layout.layout_id')
                    ->leftJoin('versions', 'bases.version_id', '=', 'versions.id')
                    ->leftJoin('types', 'bases.type_id', '=', 'types.id')
                    ->select('bases.name AS Base_Name', 'layouts.name AS Layout_Name', 'versions.name as VersionName', 'types.name as typeName')
                    ->where('used', '=', Null)
                    ->where('used_by_id', $leaderGroupID)
                    ->get();
                foreach ($export as $ex) {
                    DB::table('exports')->insert(['Base_Name' => $ex->Base_Name, 'Layout_Name' => $ex->Layout_Name, 'type' => $ex->typeName, 'version' => $ex->VersionName]);
                }
                $export = \App\Models\Export::orderBy('Base_Name', 'DESC')->get();
                DB::table('exports')->truncate();
                Excel::create('Export Custome', function ($excel) use ($export) {
                    $excel->sheet('Sheet1', function ($sheet) use ($export) {
                        $sheet->fromArray($export);
                    });
                })->export('xlsx');
            }
        }

        if (!empty($request->option_filter)) {
            $filter = $request->option_filter;
            if ($filter == "used") {
                $baseOfGroups = Base::where('used_by_id', $leaderGroupID)->where('used', '!=', '')->get();
                return view('leader.leaderFirstGetBase')->with('baseOfGroups', $baseOfGroups);
            } else if ($filter == "not_yet") {
                $baseOfGroups = Base::where('used_by_id', $leaderGroupID)->where('used', '=', Null)->get();
                return view('leader.leaderFirstGetBase')->with('baseOfGroups', $baseOfGroups);
            }
        }

        $baseOfGroups = Base::where('used_by_id', $leaderGroupID)->get();
        return view('leader.leaderFirstGetBase')->with('baseOfGroups', $baseOfGroups);
    }


    public function getUpdateBaseStatus(Request $request)
    {
        $baseID = $request->id;
        $base = Base::where('name', $baseID)->first();
        if ($base->used == Null) {
            $base->used = 1;
            $base->save();
            return "Base has been updated";
        } else {
            $base->used = Null;
            $base->save();
            return "Base has been updated";
        }
    }

    public function getAddNewOrder(Request $request)
    {
        return view('leader.addNewOrder');
    }

    public function postAddNewOrder(Request $request)
    {
        $this->validate($request, [
            'file' => 'required'
        ]);
        $file = $request->file('file');
        $fileName = "";
        if (!empty($file)) {
            $fileName = $file->getClientOriginalName();
            $arrayString = explode(".", $fileName);
            $extension = end($arrayString);
            if ($extension != "csv") {
                return redirect()->back()->withInput()->withErrors(['error' => 'Please upload only csv file']);
            } else {
                Excel::load($request->file('file'), function ($reader) {
                    $reader->each(function ($sheet) {
                        try {
                            //Order::firstOrCreate($sheet->toArray());
                            $order = new Order();
                            $order->order_id = $sheet->order_id;
                            $order->dateline = $sheet->dateline;
                            $order->layout = $sheet->layout;
                            $order->base_name = $sheet->base_name;
                            $order->layout = $sheet->layout;
                            $order->version = $sheet->version;
                            $order->type = $sheet->type;
                            $order->save();
                        } catch (\Exception $ex) {

                        }
                    });
                });
                return redirect()->back()->withInput()->withErrors(['notice' => 'Order Uploaded']);

            }


        }

    }


    public function getLeaderFirstGetOrder(Request $request)
    {

        $Leader = Auth::user();
        $groupID = $Leader->group;
        $groupName = $groupID->name;
        $users = $groupID->users;
        $groups = Group::where('status', '1')->where('type', 'first')->get();
        $qcs = Group::where('type', 'qc')->first()->id;
        $member_qc = User::where('group_id', $qcs)->where('status', '1')->get();
        if (!empty($request->searchOrder)) {

            if (Auth::user()->level == "2") {
                if (@$_GET['filter_group'] == "all") {
                    $search = $request->searchOrder;
                    $search = explode(",", $search);
                    $orders = Order::orderBy('id', 'desc')->whereIn('order_id', $search)->get();
                    return view('leader.leaderFirstGetOrder')->with('orders', $orders)->with('users', $users)->with('groups', $groups)->with('member_qc', $member_qc);
                } else {
                    $search = $request->searchOrder;
                    $search = explode(",", $search);
                    $orders = Order::orderBy('id', 'desc')->where('group_name', $groupName)->whereIn('order_id', $search)->get();
                    return view('leader.leaderFirstGetOrder')->with('orders', $orders)->with('users', $users)->with('groups', $groups)->with('member_qc', $member_qc);
                }
            } else {
                $search = $request->searchOrder;
                $search = explode(",", $search);
                $orders = Order::orderBy('id', 'desc')->where('group_name', $groupName)->whereIn('order_id', $search)->get();
                return view('leader.leaderFirstGetOrder')->with('orders', $orders)->with('users', $users)->with('groups', $groups)->with('member_qc', $member_qc);
            }

        }
        if (!empty($request->listFolder)) {
            try {
                if (!empty($this->openDir($request->listFolder))) {
                    if (Auth::user()->level == "2") {
                        if (@$_GET['filter_group'] == "all") {
                            $orders = Order::orderBy('id', 'desc')->whereIn('order_id', $this->openDir($request->listFolder))->get();
                            return view('leader.leaderFirstGetOrder')->with('orders', $orders)->with('users', $users)->with('groups', $groups)->with('member_qc', $member_qc);
                        } else {
                            $orders = Order::orderBy('id', 'desc')->where('group_name', $groupName)->whereIn('order_id', $this->openDir($request->listFolder))->get();
                            return view('leader.leaderFirstGetOrder')->with('orders', $orders)->with('users', $users)->with('groups', $groups)->with('member_qc', $member_qc);
                        }
                    } else {
                        $orders = Order::orderBy('id', 'desc')->where('group_name', $groupName)->whereIn('order_id', $this->openDir($request->listFolder))->get();
                        return view('leader.leaderFirstGetOrder')->with('orders', $orders)->with('users', $users)->with('groups', $groups)->with('member_qc', $member_qc);
                    }

                }
            } catch (\Exception $ex) {
                return redirect()->route('leaderFirstGetOrder')->withInput()->withErrors(['danger' => 'Please enter your location orders']);
            }
        }
        if (!empty($request->action)) {
            $action = $request->action;
            if ($action == "update") {
                $id = $request->orderID;
                $userID = $request->userID;
                $group = $request->group;
                $leader_description = $request->leader_description;
                $user = $request->user;
                $index = 0;
                foreach ($id as $key => $value) {
                    $order = Order::find($value);
                    $order->member_id = $userID[$index];
                    $order->member_name = $user[$index];
                    $order->leader_check_description = $leader_description[$index];
                    $order->group_name = $group[$index];
                    $order->save();
                    $index++;
                }
                return redirect()->route('leaderFirstGetOrder')->withInput()->withErrors(['notice' => 'Order has been update completed']);

            } else {
                $deleteID = $request->deleteID;
                if (!empty($deleteID)) {
                    foreach ($deleteID as $key => $value) {
                        $order = Order::find($value);
                        $order->isdelete = "0";
                        $order->save();
                    }
                }
                return redirect()->route('leaderFirstGetOrder')->withInput()->withErrors(['notice' => 'Order has been deleted']);
            }
        }
        if (Auth::user()->level == "2") {
            if (@$_GET['filter_group'] == "all") {
                $orders = Order::orderBy('id', 'desc')->where('isdelete', '1')->paginate(200);
            } else {
                $orders = Order::orderBy('id', 'desc')->where('group_name', $groupName)->where('isdelete', '1')->paginate(200);
            }
        } else {
            $orders = Order::orderBy('id', 'desc')->where('group_name', $groupName)->where('isdelete', '1')->paginate(200);
        }
        return view('leader.leaderFirstGetOrder')->with('orders', $orders)->with('users', $users)->with('groups', $groups)->with('member_qc', $member_qc);
    }

    public function getFirstReport(Request $request)
    {
        $groupName = Auth::user()->group->name;
        session(['start' => $request->start]);
        session(['end' => $request->end]);
        if (!empty($request->start) && !empty($request->end)) {
            $start = $request->start;
            $end = $request->end;
            $orders = $orders = DB::select("SELECT *, COUNT(order_id) as Total  FROM orders WHERE DATE(`created_at`) >='" . $start . "'  AND  DATE(`created_at`) <='" . $end . "'  AND group_name='" . $groupName . "' GROUP BY member_id ");
            return view('leader.reportResult')->with('orders', $orders);


        } elseif (!empty($request->start) && empty($request->end)) {
            $start = $request->start;
            $orders = DB::select("SELECT *, COUNT(order_id) as Total  FROM orders WHERE DATE(`created_at`) ='" . $start . "' AND group_name='" . $groupName . "'  GROUP BY member_id ");
            return view('leader.reportResult')->with('orders', $orders);

        } elseif (empty($request->start) && !empty($request->end)) {
            $end = $request->end;
            $orders = DB::select("SELECT *, COUNT(order_id) as Total  FROM orders WHERE DATE(`created_at`) <='" . $end . "'  AND group_name='" . $groupName . "' GROUP BY member_id");
            return view('leader.reportResult')->with('orders', $orders);

        }

        $date = date("Y-m-d");
        $orders = DB::select("SELECT *, COUNT(order_id) as Total  FROM orders WHERE DATE(`created_at`) = '" . $date . "' AND group_name='" . $groupName . "' GROUP BY member_id");
        return view('leader.reportResult')->with('orders', $orders);


    }

    public function getLeaderReportFirstLeader(Request $request)
    {
        $groupName = Auth::user()->group->name;
        if (!empty(session()->get('start')) && !empty(session()->get('end'))) {
            $start = session()->get('start');
            $end = session()->get('end');
            $orders = $orders = DB::select("SELECT *, COUNT(order_id) as Total  FROM orders WHERE DATE(`created_at`) >='" . $start . "'  AND  DATE(`created_at`) <='" . $end . "' AND group_name='" . $groupName . "' GROUP BY member_id");
            $pdf = PDF::loadView('leader.month_report', ['orders' => $orders]);
            return $pdf->download('report.pdf', ['orders' => $orders]);
        } elseif (!empty(session()->get('start')) && empty(session()->get('end'))) {
            $start = session()->get('start');
            $orders = DB::select("SELECT *, COUNT(order_id) as Total  FROM orders WHERE DATE(`created_at`) >='" . $start . "' AND group_name='" . $groupName . "'  GROUP BY member_id");
            $pdf = PDF::loadView('leader.month_report', ['orders' => $orders]);
            return $pdf->download('report.pdf', ['orders' => $orders]);

        } elseif (empty(session()->get('start')) && !empty(session()->get('end'))) {
            $end = session()->get('end');
            $orders = DB::select("SELECT *, COUNT(order_id) as Total  FROM orders WHERE DATE(`created_at`) <='" . $end . "' AND group_name='" . $groupName . "' GROUP BY member_id");
            $pdf = PDF::loadView('leader.month_report', ['orders' => $orders]);
            return $pdf->download('report.pdf', ['orders' => $orders]);
        }


        $orders = DB::select("SELECT *, COUNT(order_id) as Total  FROM orders WHERE DATE(`created_at`) ='" . date('Y-m-d') . "' AND group_name='" . $groupName . "'  GROUP BY member_id");
        $pdf = PDF::loadView('leader.month_report', ['orders' => $orders]);
        return $pdf->download('report.pdf', ['orders' => $orders]);
    }

    public function getReportResult()
    {
        include 'Test.php';
    }

    public function getFilterOrder(Request $request)
    {
        $Leader = Auth::user();
        $groupID = $Leader->group;
        $groupName = $groupID->name;
        $users = $groupID->users;
        $groups = Group::where('status', '1')->where('type', 'first')->get();
        $qcs = Group::where('type', 'qc')->first()->id;
        $member_qc = User::where('group_id', $qcs)->where('status', '1')->get();
        if (!empty($request->start) && !empty($request->to)) {
            $start = $request->start;
            $arrayStart = explode("-", $start);
            $end = $request->to;
            $arrayEnd = explode("-", $end);
            $yearStart = $arrayStart[0];
            $monthStart = $arrayStart[2];
            $yearEnd = $arrayEnd[0];
            $monthEnd = $arrayEnd[2];
            $dateStart = $arrayStart[1];
            $dateEnd = $arrayEnd[1];
            $orders = Order::whereDay('created_at', '>=', $dateStart)->whereMonth('created_at', '>=', $monthStart)->whereYear('created_at', '>=', $yearStart)->orderBy('id', 'desc')->whereDay('created_at', '<=', $dateEnd)->whereMonth('created_at', '<=', $monthEnd)->whereYear('created_at', '<=', $yearEnd)->where('group_name', $groupName)->get();;
            return view('leader.leaderFirstGetOrder')->with('orders', $orders)->with('users', $users)->with('groups', $groups)->with('member_qc', $member_qc);

        } elseif (!empty($request->start) && empty($request->to)) {
            $start = $request->start;
            $arrayStart = explode("-", $start);
            $yearStart = $arrayStart[0];
            $monthStart = $arrayStart[2];
            $dateStart = $arrayStart[1];
            $orders = Order::whereDay('created_at', '=', $dateStart)->whereMonth('created_at', '=', $monthStart)->whereYear('created_at', '=', $yearStart)->where('group_name', $groupName)->orderBy('id', 'desc')->get();;
            return view('leader.leaderFirstGetOrder')->with('orders', $orders)->with('users', $users)->with('groups', $groups)->with('member_qc', $member_qc);

        } elseif (empty($request->start) && !empty($request->to)) {
            $end = $request->to;
            $arrayEnd = explode("-", $end);
            $yearEnd = $arrayEnd[0];
            $dateEnd = $arrayEnd[1];
            $monthEnd = $arrayEnd[2];
            $orders = Order::orderBy('id', 'desc')->whereDay('created_at', '<=', $dateEnd)->whereMonth('created_at', '<=', $monthEnd)->whereYear('created_at', '<=', $yearEnd)->where('group_name', $groupName)->get();;
            return view('leader.leaderFirstGetOrder')->with('orders', $orders)->with('users', $users)->with('groups', $groups)->with('member_qc', $member_qc);

        }
        if (!empty($request->filter_component)) {
            $filter = $request->filter_component;
            switch ($filter) {
                case "stock":
                    $orders = Order::where('isdelete', '!=', '0')->where('group_name', $groupName)->get();
                    return view('leader.leaderFirstGetOrder')->with('orders', $orders)->with('users', $users)->with('groups', $groups)->with('member_qc', $member_qc);

                    break;
                case "upload":
                    $orders = Order::where('upload_status', '=', '1')->where('isdelete', '!=', '0')->get();
                    return view('leader.leaderFirstGetOrder')->with('orders', $orders)->with('users', $users)->with('groups', $groups)->with('member_qc', $member_qc);

                    break;
                case "rest_date":
                    $orders= Order::select('*', DB::raw("DATEDIFF(dateline,created_at) as Expire"))->where('group_name',$groupName)->where('status','!=','1')->having('Expire','<=',3)->get();
                    return view('leader.leaderFirstGetOrder')->with('orders', $orders)->with('users', $users)->with('groups', $groups)->with('member_qc', $member_qc);

                    break;
                case "not_complete":
                    $orders = Order::where('status', '!=', '1')->where('isdelete', '!=', '0')->where('group_name', $groupName)->get();
                    return view('leader.leaderFirstGetOrder')->with('orders', $orders)->with('users', $users)->with('groups', $groups)->with('member_qc', $member_qc);

                    break;
                case "ready":
                    $orders = Order::where('status', '=', '1')->where('isdelete', '!=', '0')->where('group_name', $groupName)->get();
                    return view('leader.leaderFirstGetOrder')->with('orders', $orders)->with('users', $users)->with('groups', $groups)->with('member_qc', $member_qc);

                    break;
                default:
                    break;
            }

        }
        return redirect()->back();


    }


    public function getMessageFirst(Request $request){
        $groupName=Auth::user()->group->name;
        return $order=Order::where('group_name',$groupName)->where('leader_check_result','2')->get();
    }

    public function getMessageLeaderFirst(Request $request){
        $groupName=Auth::user()->group->name;
        return $order=Order::where('group_name',$groupName)->where('leader_check_result','0')->get();
    }

    public function getMessageQCCheck(Request $request){
        $groupName=Auth::user()->group->name;
        return $order=Order::where('group_name',$groupName)->where('qc_check_result','0')->get();
    }

    public function getLeaderCheckBases()
    {
        $bases = Base::where('get_it', '1')->orderBy('id', 'DESC')->paginate(200);
        $qc = Group::where('type', 'qc')->where('status', '1')->first();
        return view('leader.getCheckBase')->with('bases', $bases)->with('qc', $qc);
    }

    public function postLeaderCheckBases(Request $request)
    {
        $qc = Group::where('type', 'qc')->where('status', '1')->first();
        $id = $request->id;
        $qcName = $request->qcName;
        $first_checker_result = $request->leaderChingResult;
        $idCheck = $request->idCheck;
        $record = 0;
        $qcProblem = $request->qcProblem;
        if (!empty($request->choose_action)) {
            if ($request->choose_action == "Update") {
                foreach ($id as $key => $baseID) {
                    $base = Base::find($baseID);
                    $base->first_checker_name = $qcName[$key];
                    $base->first_checker_result = $first_checker_result[$key];
                    $base->first_checker_problem = $qcProblem[$key];
                    $base->save();
                }
                return redirect()->back()->withInput()->withErrors(['notice' => 'base has been updated']);
            } else {
                foreach ($idCheck as $key => $value) {
                    $base = Base::find($value);
                    $base->delete();
                    $record++;
                }
                return redirect()->back()->withInput()->withErrors(['notice' => "$record has been deleted"]);
            }
        }
        if (!empty($request->listFolder)) {
            $request->listFolder;
            $select = $this->openDir($request->listFolder);
            if (!empty($select)) {
                $bases = Base::whereIn('name', $select)->where('get_it', '1')->get();
                return view('leader.getCheckBase')->with('bases', $bases)->with('qc', $qc);
            }
            return redirect()->back();
        }
        if (!empty($request->search)) {
            if ($request->search == "n" || $request->search == "N") {
                $bases = Base::where('first_checker_result', Null)->where('get_it', '1')->get();
                return view('leader.getCheckBase')->with('bases', $bases)->with('qc', $qc);

            }
            $bases = Base::where('name', 'LIKE', '%' . $request->search . '%')->where('get_it', '1')->get();
            return view('leader.getCheckBase')->with('bases', $bases)->with('qc', $qc);
        }
        if (!empty($request->from && !empty($request->to))) {
            $from = $request->from;
            $from = explode("-", $from);
            $yearFrom = $from[0];
            $monthFrom = $from[1];
            $dateFrom = $from[2];
            $to = $request->to;
            $to = explode("-", $to);
            $yearTo = $to[0];
            $monthTo = $to[1];
            $dateTo = $to[2];
            $bases = Base::where('day', '=', $dateFrom)->where('month', '=', $monthFrom)->where('year', '=', $yearFrom)->where('day', '<=', $dateTo)->where('month', '<=', $monthTo)->where('year', '<=', $yearTo)->get();
            return view('leader.getCheckBase')->with('bases', $bases)->with('qc', $qc);

        } else if (!empty($request->from && empty($request->to))) {
            $from = $request->from;
            $from = explode("-", $from);
            $yearFrom = $from[0];
            $monthFrom = $from[1];
            $dateFrom = $from[2];
            $bases = Base::where('day', '=', $dateFrom)->where('month', '=', $monthFrom)->where('year', '=', $yearFrom)->get();
            return view('leader.getCheckBase')->with('bases', $bases)->with('qc', $qc);

        }

    }

    public function getLeaderCheckFirst()
    {
        return view('leader.getCheckFirst');
    }

    public function getLeaderQCReport()
    {

    }
}
