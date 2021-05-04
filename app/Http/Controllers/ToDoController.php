<?php

namespace App\Http\Controllers;
use App\Models\ToDo;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ToDoController extends Controller{
    use ApiResponser;
    
    public function create(Request $request) {
        $request->validate([ 'name' => 'required|string|max:100']);
        
        $toDo = new ToDo;
        $toDo->name = $request->name;
        $toDo->save();
        
        ToDo::increment('ord_num'); 
        
        return $this->success([ 'todo' => $toDo ]);        
    } 

    
    public function read(Request $request) {
        $toDos = ToDo::orderBy('ord_num')->get();
        return $this->success([ 'todos' => $toDos ]);       
    } 
    
    
    public function update(Request $request) {
        $request->validate([ 
            'name' => 'required|string|max:100',
            'id' => 'required|integer' ]);
        
        $toDo = ToDo::where('id',$request->id)->first();
        if(!$toDo)
            return $this->error('Wrong id', 400);
        
        $toDo->name = $request->name; 
        $toDo->save();
        
        return $this->success([ 'todo' => $toDo ]);
    } 
    
    
     public function delete(Request $request){
        $request->validate([ 
            'ids' => 'required|array',
            "ids.*"  => "required|integer|distinct" ]);
        
        $deleted = ToDo::destroy($request->ids);
            
        return response()->json(['deleted' => $deleted], 200);  
     }
     
     
     public function reorder(Request $request){
        $request->validate([ 
            'ids' => 'required|array',
            "ids.*"  => "required|integer|distinct" ]);
        
        $ids = $request->ids;
        $table = ToDo::getModel()->getTable();
        $cases = []; $params = []; $i=count($ids);

        foreach ($ids as $id) {
            $cases[] = "WHEN {$id} then ?";
            $params[] = $i; 
            $i--; }

        $ids = implode(',', $ids);
        $cases = implode(' ', $cases);
        $params[] = now();
        $res = \DB::update("UPDATE `{$table}` SET `ord_num` = CASE `id` {$cases} END, `updated_at` = ? WHERE `id` in ({$ids})", $params);
        if ($res>-1){
            return $this->success([], 200);   }
        else
        return $this->error('Reorder failed', 500);  
     }
}