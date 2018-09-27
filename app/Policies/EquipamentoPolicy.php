<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

use App\Equipamento;

class EquipamentoPolicy
{
    use HandlesAuthorization;

    public $is_superAdmin;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        $codpesAdmins = explode(',', trim(env('SUPERADMIN_IDS')));
        $this->is_superAdmin = Auth::check() && in_array(Auth::user()->id, $codpesAdmins);
    }

    public function update(User $user, Equipamento $equipamento)
    {
        $owner = $user->id === $equipamento->user_id;
        return $owner || $this->is_superAdmin || $this->temAcessoNaRedeDeUmAdminGrupo($user,$equipamento->rede_id);
    }

    public function view(User $user, Equipamento $equipamento)
    {
        $owner = $user->id === $equipamento->user_id;
        return $owner || $this->is_superAdmin || $this->temAcessoNaRedeDeUmAdminGrupo($user,$equipamento->rede_id);
    }

    public function delete(User $user, Equipamento $equipamento)
    {
        $owner = $user->id === $equipamento->user_id;
        return $owner || $this->is_superAdmin || $this->temAcessoNaRedeDeUmAdminGrupo($user,$equipamento->rede_id);
    }

    public function create(User $user)
    {
        return true;
    }

    public function temAcessoNaRedeDeUmAdminGrupo($user,$rede_id)
    {
        foreach($user->roles()->get() as $role){       
            foreach($role->redes()->get() as $rede){
                if($role->grupoadmin && $rede->id==$rede_id) {
                    return true;
                }
            }
        }
        return false;
    }

}
