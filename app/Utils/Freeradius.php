<?php

namespace App\Utils;

use Illuminate\Support\Facades\DB;

use App\Rede;
use App\Equipamento;

use App\Utils\NetworkOps;

use IPTools\IP;
use IPTools\Network;
use IPTools\Range;

class Freeradius
{
    public function file()
    {
        $ops = new NetworkOps;
        $build = "";

        $redes = Rede::all();
        foreach ($redes as $rede) {
            if (isset($rede->vlan) && !empty($rede->vlan)) {
                foreach ($rede->equipamentos as $equipamento) {
                    $macaddress = strtolower(str_replace(':', '', $equipamento->macaddress));
                    $build .= "$macaddress   Cleartext-Password := $macaddress\n";
                    $build .= "    Tunnel-Type = \"VLAN\"\n";
                    $build .= "    Tunnel-Medium-Type = \"IEEE-802\",\n";
                    $build .= "    Tunnel-Private-Group-Id = \"1188\"\n\n";
                }
            }
        }
    
        $build .= "DEFAULT Framed-Protocol == PPP\n";
        $build .= "    Framed-Protocol = PPP,\n";
        $build .= "    Framed-Compression = Van-Jacobson-TCP-IP\n\n";

        $build .= "DEFAULT Hint == \"CSLIP\"\n";
        $build .= "    Framed-Protocol = SLIP,\n";
        $build .= "    Framed-Compression = Van-Jacobson-TCP-IP\n\n";

        $build .= "DEFAULT Hint == \"SLIP\"\n";
        $build .= "    Framed-Protocol = SLIP";

        return $build;
    }

    public function cadastraOuAtualizaRede($rede)
    {
        $records = 
            [
                [$rede->id,'Tunnel-Type',':=','VLAN'],
                [$rede->id,'Tunnel-Medium-Type',':=','IEEE-802'],
                [$rede->id,'Tunnel-Private-Group-Id',':=',$rede->vlan]
            ];

        foreach($records as $record) {

            $fields = [
                'groupname' => $record[0],
                'attribute' => $record[1],
                'op'        => $record[2],
                'value'     => $record[3]
            ];

            $filter = [ 
                'groupname' =>$rede->id,
                'attribute' =>$record[1]
            ];

            // first, check if this record exist before insert
            $check = DB::connection('freeradius')->table('radgroupreply')->select()->where($filter)->first();
            if(is_null($check)){
                DB::connection('freeradius')->table('radgroupreply')->insert($fields);
            } else {
                DB::connection('freeradius')->table('radgroupreply')->where($filter)->update($fields);
            }
        }
    }

    public function deletaRede($rede)
    {
        $filter = [ 
            'groupname' =>$rede->id,
        ];

        DB::connection('freeradius')->table('radgroupreply')->where($filter)->delete();
    }

    public function cadastraOuAtualizaEquipamento($equipamento,$mac_antigo = null)
    {
        $fields = [
            'UserName'  => strtolower(str_replace(':','',$equipamento->macaddress)),
            'GroupName' => $equipamento->rede->id,
        ];

        $filter = [ 
            'UserName' => strtolower(str_replace(':','',$mac_antigo)),
        ];

        // Garante que a rede para esse equipamento também esteja cadastrada
        $this->cadastraOuAtualizaRede($equipamento->rede);
        
        // first, check if this record exist before insert
        $check = DB::connection('freeradius')->table('radusergroup')->select()->where($filter)->first();
        
        if(is_null($mac_antigo) || is_null($check)){
            DB::connection('freeradius')->table('radusergroup')->insert($fields);
        } else {               
            DB::connection('freeradius')->table('radusergroup')->where($filter)->update($fields);
        }
    }

    public function deletaEquipamento($equipamento)
    {
        $filter = [ 
            'UserName' => $equipamento->macaddress,
        ];
        DB::connection('freeradius')->table('radusergroup')->where($filter)->delete();
    }

}

