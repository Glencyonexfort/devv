<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class JobsMovingInventory extends Model
{
    use Notifiable;

    protected $table = 'jobs_moving_inventory';
    protected $primaryKey = 'id';

    public function getInventoryListForEmail($tenant_id, $job_id)
    {
        $inventory_groups = MovingInventoryGroups::where('tenant_id', '=', $tenant_id)->get();
        $getInventoryItems = MovingInventoryDefinitions::where('tenant_id', '=', $tenant_id)->get();
        $groups = [];
        foreach ($inventory_groups as $group) {
            $count = 0;
            $inv = [];
            foreach ($getInventoryItems as $item) {
                if ($group->group_id == $item->group_id) {
                    $moving_inv = JobsMovingInventory::where('inventory_id', '=', $item->id)->where('job_id', '=', $job_id)->where('quantity', '>', 0)->first();
                    if ($moving_inv) {
                        $count++;
                        $i['name'] = $item->item_name;
                        $i['qty'] = $moving_inv->quantity;
                        $inv[] = $i;
                        unset($i);
                        unset($moving_inv);
                    }
                }
            }
            if($count>0){
                $s['category'] = $group->group_name;
                $s['count'] = $count;
                $s['items'] = $inv;
                $groups[] = $s;
            }
            unset($s);
            unset($inv);
        }
        $other_count = 0;
        $o_inv = [];
        $other_inv = JobsMovingInventory::where('inventory_id', '>', 90000)->where('job_id', '=', $job_id)->where('quantity', '>', 0)->get();
        if ($other_inv) {
            foreach ($other_inv as $item) {
                $other_count++;
                $i['name'] = $item->misc_item_name;
                $i['qty'] = $item->quantity;
                $o_inv[] = $i;
                unset($i);
            }
            if($other_count>0){
                $os['category'] = 'Miscellaneous Items';
                $os['count'] = $other_count;
                $os['items'] = $o_inv;
                $groups[] = $os;
            }
        }
        $response='';
        if(count($groups)){
            foreach($groups as $group){
                $response .= '<b>'.$group['category']."</b><br/>";
                foreach($group['items'] as $item){
                    $response .= $item['name']." Qty: ".(int)$item['qty']."<br/>";
                }
            }
        }
        return $response;
    }

    public function getInventoryListForField($tenant_id, $job_id)
    {
        $inventory_groups = MovingInventoryGroups::where('tenant_id', '=', $tenant_id)->get();
        $getInventoryItems = MovingInventoryDefinitions::where('tenant_id', '=', $tenant_id)->get();
        $groups = [];
        foreach ($inventory_groups as $group) {
            $count = 0;
            $inv = [];
            foreach ($getInventoryItems as $item) {
                if ($group->group_id == $item->group_id) {
                    $moving_inv = JobsMovingInventory::where('inventory_id', '=', $item->id)->where('job_id', '=', $job_id)->where('quantity', '>', 0)->first();
                    if ($moving_inv) {
                        $count++;
                        $i['name'] = $item->item_name;
                        $i['qty'] = $moving_inv->quantity;
                        $inv[] = $i;
                        unset($i);
                        unset($moving_inv);
                    }
                }
            }
            if($count>0){
                $s['category'] = $group->group_name;
                $s['count'] = $count;
                $s['items'] = $inv;
                $groups[] = $s;
            }
            unset($s);
            unset($inv);
        }
        $other_count = 0;
        $o_inv = [];
        $other_inv = JobsMovingInventory::where('inventory_id', '>', 90000)->where('job_id', '=', $job_id)->where('quantity', '>', 0)->get();
        if ($other_inv) {
            foreach ($other_inv as $item) {
                $other_count++;
                $i['name'] = $item->misc_item_name;
                $i['qty'] = $item->quantity;
                $o_inv[] = $i;
                unset($i);
            }
            if($other_count>0){
                $os['category'] = 'Miscellaneous Items';
                $os['count'] = $other_count;
                $os['items'] = $o_inv;
                $groups[] = $os;
            }
        }
        $response='';
        if(count($groups)){
            foreach($groups as $group){
                $response .= $group['category']."\n";
                foreach($group['items'] as $item){
                    $response .= $item['name']." Qty: ".(int)$item['qty']."\n";
                }
            }
        }
        return htmlspecialchars_decode($response);
    }

}
