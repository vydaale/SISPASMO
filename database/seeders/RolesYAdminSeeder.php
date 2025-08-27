<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; 

class RolesYAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $roles = ['Administrador','Coordinador','Docente','Alumno','Aspirante'];
        foreach ($roles as $r) {
            DB::table('roles')->updateOrInsert(['nombre_rol' => $r], []);
        }

        $idRolAdmin = DB::table('roles')->where('nombre_rol','Administrador')->value('id_rol');

        $idUsuario = DB::table('usuarios')->updateOrInsert(
            ['usuario' => 'admin'],
            [
                'nombre' => 'Jesus',
                'apellidoP' => 'Rios',
                'apellidoM' => 'Armenta',
                'fecha_nac' => '2002-12-16',
                'pass' => Hash::make('adminjesus'),
                'genero' => 'M',
                'correo' => 'admin@morelos.test',
                'telefono' => '7352452435',
                'direccion' => 'S/N',
                'id_rol' => $idRolAdmin,
                'fecha_registro' => now(),
            ]
        );

        $idUsuario = DB::table('usuarios')->where('usuario','admin')->value('id_usuario');

        DB::table('administradores')->updateOrInsert(
            ['id_usuario' => $idUsuario],
            ['fecha_ingreso' => now()->toDateString(), 'rol' => 'General', 'estatus' => 'activo']
        );
    }
}
