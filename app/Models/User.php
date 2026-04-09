<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\Company;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    const TYPE_ADMIN = 'admin';
    const TYPE_SUPERVISOR = 'supervisor';
    const TYPE_AGENT = 'agent';
    const TYPE_STAFF = 'staff';

    protected $fillable = [
        'name',
        'username',
        'email',
        'user_type',
        'password',
        'position',
        'phone',
        'is_active',
        'company_id',
        'supervisor_id',
        'evaluation_role_id',
    ];

    public function evaluationRole()
    {
        return $this->belongsTo(AgentEvaluationRole::class, 'evaluation_role_id');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active'         => 'boolean',
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    /** Is the user an Admin? */
    public function isAdmin(): bool
    {
        return $this->user_type === self::TYPE_ADMIN;
    }

    /** Is the user a Supervisor? */
    public function isSupervisor(): bool
    {
        return $this->user_type === self::TYPE_SUPERVISOR;
    }

    /** Is the user an Agent? */
    public function isAgent(): bool
    {
        return $this->user_type === self::TYPE_AGENT;
    }

    /** Is the user general staff? */
    public function isStaff(): bool
    {
        return $this->user_type === self::TYPE_STAFF;
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Return the user as the session array the app expects.
     * This keeps all views working without changes.
     */
    public function toSessionArray(): array
    {
        $role = $this->roles->first();
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'full_name' => $this->name,
            'username'  => $this->username,
            'email'     => $this->email,
            'user_type' => $this->user_type,
            'position'  => $this->position,
            'phone'     => $this->phone,
            'is_active' => $this->is_active,
            'company_id'   => $this->company_id,
            'supervisor_id'=> $this->supervisor_id,
            'supervisor_name' => ($this->supervisor && $this->supervisor->id != $this->id) ? $this->supervisor->name : 'Self',
            'evaluation_role_id' => $this->evaluation_role_id,
            'evaluation_role_name' => $this->evaluationRole ? $this->evaluationRole->name : 'None',
            'role'      => [
                'id' => $role ? $role->id : null, 
                'name' => $role ? $role->name : 'No Role'
            ],
            'company'   => [
                'id' => $this->company_id ?? '1', 
                'name' => $this->company_id ? (Company::find($this->company_id)->company_name ?? 'N/A') : 'Evalia HQ'
            ],
        ];
    }
}
