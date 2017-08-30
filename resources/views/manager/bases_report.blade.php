@extends('layout.manager.master')
@section('content')
    <h4 class="text-center bg-primary" style="padding:10px;"><?php echo $date;?></h4>
    <div class="block">
       <table class="table responsive">
           <thead>
               <tr>
                   <th>ID</th>
                   <th>Name</th>
                   <th>Total</th>
                   <th>Evaluate</th>
               </tr>
           </thead>
           <tbody>
                <?php $i=1;?>
               @foreach($bases as $base)
                <tr>
                    <td>{{$i}}</td>
                    <td>{{$base->UserName}}</td>
                    <td>{{$base->Total}}</td>
                    <td>{{$i}}</td>
                </tr>
                <?php $i++;?>
               @endforeach
           </tbody>
       </table>
   </div>
    </div>
@stop