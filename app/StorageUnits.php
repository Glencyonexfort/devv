<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorageUnits extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'tenant_id','name','serial_number','storage_type_id','manufacturer_serial_number',
        'active','created_date','updated_date','created_by','updated_by'
    ];
    public static function getAvailableUnits($storage_type,$from,$to){

        $storage_units_list = StorageUnits::select("storage_units.*","storage_types.name as type_name")
        ->join('storage_types', 'storage_types.id', 'storage_units.storage_type_id')
        ->where(['storage_units.deleted'=>'0','storage_units.active'=>'1',
                'storage_units.storage_type_id'=> $storage_type,
                'storage_units.tenant_id'=> auth()->user()->tenant_id
                ])
        ->orderBy('storage_units.serial_number', 'ASC')->get();
        if(isset($storage_units_list)){
        $response = [];
        foreach($storage_units_list as $key=>$unit){
            $result = StorageUnitAllocation::where([
                'tenant_id'=>auth()->user()->tenant_id,
                'unit_id'=>$unit->id, 
                'deleted'=>'0'
                ])
                ->where(function ($query) use ($from,$to) {
                            $query->where(function ($query) use ($from,$to) {
                                $query->where('from_date','<=',$from)
                                    ->where('to_date','>=',$from);
                        });
                        $query->orWhere(function ($query) use ($from,$to) {
                                $query->where('from_date','<=',$to)
                                    ->where('to_date','>=',$to);
                        });
                            
                })
                ->count();
                if($result==0){
                    $response[]=$unit;
                }
            }
        }
        return $response;
    }

    public static function checkUnitAvailability($unit_id,$from,$to){
        $result = StorageUnitAllocation::where([
            'tenant_id'=>auth()->user()->tenant_id,
            'unit_id'=>$unit_id, 
            'deleted'=>'0'
            ])
        ->where(function ($query) use ($from,$to) {
                    $query->where(function ($query) use ($from,$to) {
                        $query->where('from_date','<=',$from)
                            ->where('to_date','>=',$from);
                });
                $query->orWhere(function ($query) use ($from,$to) {
                        $query->where('from_date','<=',$to)
                            ->where('to_date','>=',$to);
                });
                    
        })
        ->count();

        if($result>0){
            return false;
        }else{
            return true;
        }
    }
}
