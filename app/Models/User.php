<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    const CODIGO_SUPERADMIN = "superadmin";
    const CODIGO_ADMIN = "admin";
    const CODIGO_FINANCEIRO = "financeiro";
    const CODIGO_MODERADOR = "moderador";
    const CODIGO_GERENTE = "gerente";
    const CODIGO_VENDEDOR = "vendedor";
    const CODIGO_CLIENTE = "cliente";
    const ROLES = [
        [
            'codigo' => User::CODIGO_SUPERADMIN,
            'nome' => 'Super Admin',
        ],
        [
            'codigo' => User::CODIGO_ADMIN,
            'nome' => 'Gerente Nacional',
        ],
        [
            'codigo' => User::CODIGO_FINANCEIRO,
            'nome' => 'Financeiro',
        ],
        // [
        //     'codigo' => User::CODIGO_MODERADOR,
        //     'nome' => 'Moderador',
        // ],
        [
            'codigo' => User::CODIGO_GERENTE,
            'nome' => 'Gerente Regional',
        ],
        [
            'codigo' => User::CODIGO_VENDEDOR,
            'nome' => 'Representante',
        ],
        [
            'codigo' => User::CODIGO_CLIENTE,
            'nome' => 'Cliente',
        ]
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'email',
        'password',
        'role',
        'active',
        'endereco',
        'telefone',
        'percentual_comissao',
        'nome_banco',
        'num_agencia',
        'num_conta',
        'inscricao_estadual',
        'cnpj',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $appends = [
        'role_str',
        'active_str',
    ];

    public function getRoleStrAttribute()
    {
        $roleStr = '';
        foreach (User::ROLES as $key => $perfil) {
            if($perfil['codigo'] == $this->role) $roleStr = $perfil['nome'];
        }
        return $roleStr;
    }

    public function getActiveStrAttribute()
    {
        if($this->active) return "Ativo";
        if(!$this->active) return "Inativo";
        return '';
    }

    public function escritorio()
    {
        return $this->hasOne(EscritorioRegional::class, 'id_usuario');
    }

    public function subregiao()
    {
        return $this->hasOne(Subregiao::class, 'id_usuario');
    }

    public function vendedores()
    {
        return $this->belongsToMany(User::class, 'gerentes_vendedores', 'id_gerente', 'id_vendedor');
    }

    public function gerente()
    {
        return $this->belongsToMany(User::class, 'gerentes_vendedores', 'id_vendedor', 'id_gerente');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
